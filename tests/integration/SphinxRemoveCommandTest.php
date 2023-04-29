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
use Club1\SphinxGlossary\SphinxObject;
use Flarum\Testing\integration\ConsoleTestCase;

class SphinxRemoveCommandTest extends ConsoleTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->extension('club-1-sphinx-glossary');
        $this->prepareDatabase([
            'sphinx_mappings' => [
                ['id' => 'club1', 'base_url' => 'https://club1.fr/docs/fr/', 'inventory_url' => 'https://club1.fr/docs/fr/objects.inv', 'roles' => '[]'],
            ],
            'sphinx_objects' => [
                ['id' => 0, 'name' => 'API', 'domain' => 'std', 'role' => 'term', 'priority' => -1, 'uri' => 'https://club1.fr/docs/fr/glossary.html#term-API', 'display_name' => 'API', 'sphinx_mapping_id' => 'club1'],
            ],
        ]);
    }

    public function testFire(): void
    {
        $input = [
            'command' => 'sphinx:remove',
            'id' => 'club1',
        ];
        $this->runCommand($input);
        $this->assertCount(0, SphinxMapping::all());
        $this->assertCount(0, SphinxObject::all());
    }
}
