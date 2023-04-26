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

    public function testFire()
    {
        $input = [
            'command' => 'sphinx:add',
            'id' => 'club1',
            'base URL' => 'https://club1.fr/docs/fr',
        ];
        $this->runCommand($input);
        $mappings = SphinxMapping::all();
        $this->assertCount(1, $mappings);
        $mapping = $mappings[0];
        $this->assertEquals('club1', $mapping->id);
        $this->assertEquals('https://club1.fr/docs/fr/', $mapping->base_url);
        $this->assertEquals('https://club1.fr/docs/fr/objects.inv', $mapping->inventory_url);
    }
}
