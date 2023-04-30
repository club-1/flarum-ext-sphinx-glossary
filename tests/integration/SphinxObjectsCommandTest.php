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

class SphinxObjectsCommandTest extends ConsoleTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->extension('club-1-sphinx-glossary');
        $this->prepareDatabase([
            'sphinx_mappings' => [
                ['id' => 'club1', 'base_url' => 'https://club1.fr/docs/fr/', 'inventory_url' => 'https://club1.fr/docs/fr/objects.inv', 'roles' => '[]'],
                ['id' => 'parser', 'base_url' => 'https://club-1.github.io/sphinx-inventory-parser/', 'inventory_url' => 'https://club-1.github.io/sphinx-inventory-parser/objects.inv', 'roles' => '[]'],
            ],
            'sphinx_objects' => [
                ['id' => 0, 'name' => 'API', 'domain' => 'std', 'role' => 'term', 'priority' => -1, 'uri' => 'https://club1.fr/docs/fr/glossary.html#term-API', 'display_name' => 'API', 'sphinx_mapping_id' => 'club1'],
                ['id' => 1, 'name' => 'SphinxObject', 'domain' => 'php', 'role' => 'class', 'priority' => 0, 'uri' => 'https://club-1.github.io/sphinx-inventory-parser/api.html#SphinxObject', 'display_name' => 'SphinxObject', 'sphinx_mapping_id' => 'parser'],
            ],
        ]);
    }

    public function testWithId(): void
    {
        $input = [
            'command' => 'sphinx:objects',
            'id' => 'club1',
        ];
        $output = $this->runCommand($input);
        $lines = preg_split('/\R/', $output);
        $this->assertGreaterThanOrEqual( 0, strpos($lines[0], 'Mapping'));
        $this->assertGreaterThanOrEqual(10, strpos($lines[0], 'Domain:Role'));
        $this->assertGreaterThanOrEqual(30, strpos($lines[0], 'Name'));
        $this->assertGreaterThanOrEqual( 0, strpos($lines[1], 'club1'));
        $this->assertGreaterThanOrEqual(10, strpos($lines[1], 'std:term'));
        $this->assertGreaterThanOrEqual(30, strpos($lines[1], 'API'));
    }

    public function testWithoutId(): void
    {
        $input = [
            'command' => 'sphinx:objects',
        ];
        $output = $this->runCommand($input);
        $lines = preg_split('/\R/', $output);
        $this->assertCount(3, $lines);
    }

    public function testCount(): void
    {
        $input = [
            'command' => 'sphinx:objects',
            'id' => 'parser',
            '--count' => true,
        ];
        $output = $this->runCommand($input);
        $this->assertEquals(1, intval($output));
    }
}
