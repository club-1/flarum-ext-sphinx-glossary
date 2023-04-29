<?php

/*
 * This file is part of club-1/flarum-ext-sphinx-glossary.
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

namespace Club1\SphinxGlossary\Tests\unit;

use Club1\SphinxGlossary\Formatter\SphinxGlossaryConfigurator;
use Club1\SphinxGlossary\SphinxObject;
use Flarum\Testing\unit\TestCase;
use Mockery as m;
use Mockery\MockInterface;
use s9e\TextFormatter\Configurator;
use s9e\TextFormatter\Parser;

class ConfiguratorTest extends TestCase
{
    /** @var Configurator */
    protected $configurator;

    /** @var SphinxObject[] */
    protected $objects;

    public function setUp(): void
    {
        parent::setUp();
        $this->configurator = new Configurator;
        $this->objects = [];
    }

    public function setUpObject(string $name, string $uri, string $domain = 'std', string $role = 'term', int $priority = -1, string $displayName = null): void
    {
        $object = new SphinxObject();
        $object->name = $name;
        $object->uri = $uri;
        $object->domain = $domain;
        $object->role = $role;
        $object->priority = $priority;
        $object->display_name = $displayName ?? $name;
        $this->objects[] = $object;
    }

    public function setUpObjects(array $objects): void
    {
        foreach ($objects as $object) {
            $this->setUpObject(...$object);
        }
    }

    public function getParser(): Parser
    {
        /** @var SphinxGlossaryConfigurator&MockInterface */
        $configurator = m::mock(SphinxGlossaryConfigurator::class);
        $configurator->shouldAllowMockingProtectedMethods()->makePartial();
        $configurator->shouldReceive('getObjects')->andReturn(collect($this->objects));
        $configurator($this->configurator);
        extract($this->configurator->finalize());
        return $parser;
    }

    /**
     * @dataProvider basicProvider
     */
    public function testBasic(string $text, string $expected): void
    {
        $this->setUpObjects([
            ['API', '#term-API'],
            ['CLI', '#term-CLI'],
        ]);
        $parser = $this->getParser();
        $result = $parser->parse($text);
        $this->assertEquals($expected, substr($result, 3, -4));
    }

    public function basicProvider(): array
    {
        return [
            // Basic test
            ['test API', 'test <SPHINXOBJ value="#term-API">API</SPHINXOBJ>'],
            // Case sensitive
            ['test api', 'test api'],
            // Only first should match
            ['an API is an API', 'an <SPHINXOBJ value="#term-API">API</SPHINXOBJ> is an API'],
            // Multiple terms
            ['a CLI is an API', 'a <SPHINXOBJ value="#term-CLI">CLI</SPHINXOBJ> is an <SPHINXOBJ value="#term-API">API</SPHINXOBJ>'],

        ];
    }
}
