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

class SphinxUpdateCommandTest extends ConsoleTestCase
{
    /** @var string */
    protected $port;

    /** @var resource */
    protected $proc;

    /** @var resource[] */
    protected $pipes;

    public function setUp(): void
    {
        parent::setUp();

        $this->port = getenv('TEST_SERVER_PORT') ?: '8008';
        $descriptors = [
            0 => ['file', '/dev/null', 'r'],
            1 => STDOUT,
            2 => ['pipe', 'w'],
        ];
        $command = ['php', '-S', "127.0.0.1:$this->port", '-t', 'tests/data'];
        $this->proc= proc_open($command, $descriptors, $this->pipes);
        if (!is_resource($this->proc)) {
            throw new RuntimeException('Could not run command: ' . implode(' ', $command));
        }

        $this->extension('club-1-sphinx-glossary');
        $this->prepareDatabase([
            'sphinx_mappings' => [[
                'id' => 'club1',
                'base_url' => 'https://club1.fr/docs/fr/',
                'inventory_url' => "http://127.0.0.1:$this->port/objects.inv",
                'roles' => '["std:term"]',
            ]],
        ]);
    }

    public function tearDown(): void
    {
        stream_set_blocking($this->pipes[2], false);
        $log = stream_get_contents($this->pipes[2]);
        fclose($this->pipes[2]);
        if (stripos($log, 'fail') !== false) {
            error_log($log);
        }
        proc_terminate($this->proc);
        proc_close($this->proc);
        parent::tearDown();
    }

    public function testBasic(): void
    {
        $input = [
            'command' => 'sphinx:update',
        ];
        $this->runCommand($input);
        $this->assertCount(57, SphinxObject::all());
    }

    public function testNotExist(): void
    {
        $this->app();
        $mapping = SphinxMapping::findOrFail('club1');
        assert($mapping instanceof SphinxMapping);
        $mapping->inventory_url .= '.notexist';
        $mapping->save();
        $input = [
            'command' => 'sphinx:update',
        ];
        $output = $this->runCommand($input);
        $this->assertCount(0, SphinxObject::all());
        $this->assertEquals("Failed to update inventory 'club1': could not fetch inventory 'http://127.0.0.1:$this->port/objects.inv.notexist': code 404", $output);
    }
}
