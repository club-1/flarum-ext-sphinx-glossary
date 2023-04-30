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
use Flarum\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SphinxObjectsCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('sphinx:objects')
            ->setDescription('Display info about the loaded Sphinx objects')
            ->addArgument('id', InputArgument::OPTIONAL, 'Display only the objects of this mapping')
            ->addOption('count', 'c', InputOption::VALUE_NONE, 'Only return the number of objects');
    }

    protected function fire(): void
    {
        $id = $this->input->getArgument('id');
        $count = $this->input->getOption('count');
        $objects = [];
        if ($id == null) {
            $objects = SphinxObject::all();
        } else {
            $objects = SphinxMapping::findOrFail($id)->objects()->get();
        }
        if ($count) {
            $this->info(strval(count($objects)));
        } else {
            $this->info(sprintf("%-15s %-20s %s", 'Mapping', 'Domain:Role', 'Name'));
            foreach ($objects as $object) {
                assert($object instanceof SphinxObject);
                $this->info(sprintf(
                    "%-15s %-20s %s",
                    $object->sphinx_mapping_id, "$object->domain:$object->role", $object->name
                ));
            }
        }
    }
}
