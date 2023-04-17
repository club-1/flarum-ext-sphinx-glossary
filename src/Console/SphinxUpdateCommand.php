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
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use SplFixedArray;
use UnexpectedValueException;

class SphinxUpdateCommand extends AbstractCommand
{
    public const CHUNK_SIZE = 200;

    /** @var Filesystem $cacheDir */
    protected $cacheDir;

    public function __construct(Factory $filesystemFactory)
    {
        parent::__construct();
        $this->cacheDir = $filesystemFactory->disk('club-1-sphinx-glossary');
    }

    protected function configure()
    {
        $this
            ->setName('sphinx:update')
            ->setDescription('Update Sphinx glossary entries by downloading the latest inventories');
    }

    protected function fire()
    {
        foreach (SphinxMapping::all() as $mapping) {
            try {
                $this->updateEntries($mapping);
            } catch(\Throwable $t) {
                $this->error("Failed to update inventory '$mapping->id': " . $t->getMessage());
            }
        }
    }

    protected function updateEntries(SphinxMapping $mapping) {
        $cacheKey = hash('crc32b', $mapping->inventory_url);

        $tmp = tmpfile();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FILE, $tmp);
        curl_setopt($ch, CURLOPT_URL, $mapping->inventory_url);
        if ($this->cacheDir->exists($cacheKey)) {
            $lastModified = $this->cacheDir->lastModified($cacheKey);
            curl_setopt($ch, CURLOPT_TIMEVALUE, $lastModified);
            curl_setopt($ch, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
        }
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($code == 200) {
            $this->cacheDir->writeStream($cacheKey, $tmp);
        } elseif ($code == 304) {
            $this->info("Received '304 Not Modified' for inventory '$mapping->inventory_url': Skipping update.");
            return;
        } else {
            throw new UnexpectedValueException("could not fetch inventory '$mapping->inventory_url': code $code");
        }
        fclose($tmp);

        $mapping->objects()->delete();
        $stream = $this->cacheDir->readStream($cacheKey);
        $parser = new SphinxInventoryParser($stream);
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
        if ($count > 0) {
            $objects->setSize($count);
            $mapping->objects()->saveMany($objects);
        }
        fclose($stream);
    }
}
