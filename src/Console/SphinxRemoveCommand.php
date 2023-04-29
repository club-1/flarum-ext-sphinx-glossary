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
use Flarum\Console\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use Symfony\Component\Console\Input\InputArgument;

class SphinxRemoveCommand extends AbstractCommand
{
    /** @var Repository $cache */
    protected $cache;

    public function __construct(Factory $cacheFactory)
    {
        parent::__construct();
        $this->cache = $cacheFactory->store('club-1-sphinx-glossary');
    }

    protected function configure(): void
    {
        $this
            ->setName('sphinx:remove')
            ->setDescription('Remove a Sphinx documentation inventory from the mapping list and all its objects')
            ->addArgument('id', InputArgument::REQUIRED, 'Identifier of the Sphinx doc');
    }

    protected function fire(): void
    {
        $id = $this->input->getArgument('id');
        $mapping = SphinxMapping::findOrFail($id);
        $this->cache->delete($mapping->inventory_url);
        $mapping->delete();
    }
}
