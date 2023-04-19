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

class SphinxRemoveCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('sphinx:remove')
            ->setDescription('Remove a Sphinx documentation inventory from the mapping list and all its objects')
            ->addArgument('id', InputArgument::REQUIRED, 'Identifier of the Sphinx doc');
    }

    protected function fire()
    {
        $id = $this->input->getArgument('id');
        SphinxMapping::findOrFail($id)->delete();
    }
}
