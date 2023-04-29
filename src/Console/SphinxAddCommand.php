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
use Flarum\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SphinxAddCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('sphinx:add')
            ->setDescription('Add a Sphinx documentation inventory to the mapping list')
            ->addArgument('id', InputArgument::REQUIRED, 'Identifier of the Sphinx doc')
            ->addArgument('base URL', InputArgument::REQUIRED, 'URL of the Sphinx doc')
            ->addArgument('path', InputArgument::OPTIONAL, 'Path to the inventory', 'objects.inv')
            ->addOption('role', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                        'Roles to use for the glossary, e.g. "term" or "py:func"', ['std:term']);
    }

    protected function fire(): void
    {
        $id = $this->input->getArgument('id');
        $baseURL = $this->input->getArgument('base URL');
        $path = $this->input->getArgument('path');
        $roles = $this->input->getOption('role');
        if (substr($baseURL, -1) !== '/') {
            $baseURL .= '/';
        }
        $mapping = new SphinxMapping();
        $mapping->id = $id;
        $mapping->base_url = $baseURL;
        $mapping->inventory_url = $baseURL . $path;
        $mapping->roles = $roles;
        $mapping->save();
    }
}
