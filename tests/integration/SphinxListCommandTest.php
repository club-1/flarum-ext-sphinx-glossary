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

use Flarum\Testing\integration\ConsoleTestCase;

class SphinxListCommandTest extends ConsoleTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->extension('club-1-sphinx-glossary');
        $this->prepareDatabase([
            'sphinx_mappings' => [
                ['id' => 'club1', 'base_url' => 'https://club1.fr/docs/fr/', 'inventory_url' => 'https://club1.fr/docs/fr/objects.inv', 'roles' => '["std:term", "std:label"]'],
                ['id' => 'parser', 'base_url' => 'https://club-1.github.io/sphinx-inventory-parser/', 'inventory_url' => 'https://club-1.github.io/sphinx-inventory-parser/objects.inv', 'roles' => '[]'],
            ],
        ]);
    }

    public function testBasic(): void
    {
        $input = ['command' => 'sphinx:list'];
        $output = $this->runCommand($input);
        $lines = preg_split('/\R/', $output);
        $this->assertGreaterThanOrEqual( 0, strpos($lines[0], 'Identifier'));
        $this->assertGreaterThanOrEqual(10, strpos($lines[0], 'Base URL'));
        $this->assertGreaterThanOrEqual(30, strpos($lines[0], 'Roles'));
        $this->assertGreaterThanOrEqual( 0, strpos($lines[1], 'club1'));
        $this->assertGreaterThanOrEqual(10, strpos($lines[1], 'https://club1.fr/docs/fr/'));
        $this->assertGreaterThanOrEqual(30, strpos($lines[1], '[std:term, std:label]'));
        $this->assertGreaterThanOrEqual( 0, strpos($lines[2], 'parser'));
        $this->assertGreaterThanOrEqual(10, strpos($lines[2], 'https://club-1.github.io/sphinx-inventory-parser/'));
        $this->assertGreaterThanOrEqual(30, strpos($lines[2], '[]'));
    }
}
