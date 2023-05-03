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

use Club1\SphinxGlossary\SphinxMapping;
use Flarum\Testing\integration\ConsoleTestCase;

class SphinxAddCommandTest extends ConsoleTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->extension('club-1-sphinx-glossary');
        $this->prepareDatabase([]);
    }

    /**
     * @dataProvider validProvider
     */
    public function testValid(array $input, string $id, string $baseUrl, string $inventoryUrl, array $roles): void
    {
        $input = array_merge(['command' => 'sphinx:add'], $input);
        $this->runCommand($input);
        $mapping = SphinxMapping::findOrFail($id);
        $this->assertEquals($id, $mapping->id);
        $this->assertEquals($baseUrl, $mapping->base_url);
        $this->assertEquals($inventoryUrl, $mapping->inventory_url);
        $this->assertEquals($roles, $mapping->roles);
    }

    public function validProvider(): array
    {
        return [
            "basic" => [
                ['id' => 'club1', 'base URL' => 'https://club1.fr/docs/fr'],
                'club1', 'https://club1.fr/docs/fr/', 'https://club1.fr/docs/fr/objects.inv', ['std:term'],
            ],
            "with role option" => [
                ['id' => 'club1', 'base URL' => 'https://club1.fr/docs/fr', '--role' => ['term', 'logiciel', 'commande']],
                'club1', 'https://club1.fr/docs/fr/', 'https://club1.fr/docs/fr/objects.inv', ['term', 'logiciel', 'commande'],
            ],
            "with path argument" => [
                ['id' => 'club1', 'base URL' => 'https://club1.fr/docs/fr', 'path' => 'other-objects.inv'],
                'club1', 'https://club1.fr/docs/fr/', 'https://club1.fr/docs/fr/other-objects.inv', ['std:term'],
            ],
        ];
    }
}
