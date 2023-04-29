<?php

/*
 * This file is part of club-1/flarum-ext-sphinx-glossary
 *
 * Copyright (c) 2023 Nicolas Peugnet <nicolas@club1.fr>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace Club1\SphinxGlossary\Console;

use Club1\SphinxGlossary\SphinxMapping;
use Club1\SphinxGlossary\SphinxObject;
use Club1\SphinxInventoryParser\SphinxInventoryParser;
use Flarum\Console\AbstractCommand;
use Flarum\Formatter\Formatter;
use Flarum\Console\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use SplFixedArray;
use UnexpectedValueException;

class SphinxUpdateCommand extends AbstractCommand
{
    public const CHUNK_SIZE = 200;

    /** @var Repository $cache */
    protected $cache;

    /** @var Formatter $formatter */
    protected $formatter;

    public function __construct(Factory $cacheFactory, Formatter $formatter)
    {
        parent::__construct();
        $this->cache = $cacheFactory->store('club-1-sphinx-glossary');
        $this->formatter = $formatter;
    }

    protected function configure(): void
    {
        $this
            ->setName('sphinx:update')
            ->setDescription('Update Sphinx glossary entries by downloading the latest inventories');
    }

    protected function fire(): void
    {
        $changed = false;
        foreach (SphinxMapping::all() as $mapping) {
            try {
                if ($this->updateObjects($mapping)) {
                    $changed = true;
                }
            } catch(\Throwable $t) {
                $this->error("Failed to update inventory '$mapping->id': " . $t->getMessage());
            }
        }
        if ($changed) {
            $this->formatter->flush();
        }
    }

    /**
     * Fetch and parse the inventory of a Sphinx mapping to update the objects in the database.
     *
     * @param SphinxMapping $mapping The Sphinx mapping to update.
     * @return bool True if the inventory has changed.
     * @throws UnexpectedValueException If an error is encountered while fetching or parsing.
     */
    protected function updateObjects(SphinxMapping $mapping): bool {
        $cacheKey = $mapping->inventory_url;

        $tmp = tmpfile();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FILE, $tmp);
        curl_setopt($ch, CURLOPT_FILETIME, true);
        curl_setopt($ch, CURLOPT_URL, $mapping->inventory_url);
        $lastModified = $this->cache->get($cacheKey);
        if ($lastModified != null) {
            curl_setopt($ch, CURLOPT_TIMEVALUE, $lastModified);
            curl_setopt($ch, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
        }
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $lastModified = curl_getinfo($ch, CURLINFO_FILETIME);
        curl_close($ch);
        if ($code == 200) {
            if ($lastModified != -1) {
                $this->cache->put($cacheKey, $lastModified);
            }
        } elseif ($code == 304) {
            $this->info("Received '304 Not Modified' for inventory '$mapping->inventory_url': Skipping update.");
            return false;
        } else {
            throw new UnexpectedValueException("could not fetch inventory '$mapping->inventory_url': code $code");
        }

        $mapping->objects()->delete();
        rewind($tmp);
        $parser = new SphinxInventoryParser($tmp);
        $header = $parser->parseHeader();
        $objects = new SplFixedArray(self::CHUNK_SIZE);
        $count = 0;
        foreach ($parser->parseObjects($header, $mapping->base_url) as $o) {
            $fqrole = "$o->domain:$o->role";
            if (!in_array($fqrole, $mapping->roles) && !in_array($o->role, $mapping->roles)) {
                continue;
            }
            $object = new SphinxObject();
            $object->name         = $o->name;
            $object->domain       = $o->domain;
            $object->role         = $o->role;
            $object->priority     = $o->priority;
            $object->uri          = $o->uri;
            $object->display_name = $o->displayName;
            $objects[$count++] = $object;
            if ($count == self::CHUNK_SIZE) {
                $count = 0;
                $mapping->objects()->saveMany($objects);
            }
        }
        fclose($tmp);
        if ($count > 0) {
            $objects->setSize($count);
            $mapping->objects()->saveMany($objects);
        }

        return true;
    }
}
