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

namespace Club1\SphinxGlossary\Formatter;

use Club1\SphinxGlossary\SphinxObject;
use s9e\TextFormatter\Configurator;

class SphinxGlossaryConfigurator
{
    /**
     * Add a copy of the Keywords plugin of TextFormatter.
     *
     * Based on the technique described here:
     * <https://s9etextformatter.readthedocs.io/Plugins/Keywords/Synopsis/#how-to-use-multiple-keyword-lists>
     */
    public function __invoke(Configurator $config): void
    {
        $objects = [];
        $config->plugins->load('Keywords', ['tagName' => 'SPHINXOBJ']);
        $keywords = $config->Keywords;
        foreach (SphinxObject::all('name', 'uri') as $object) {
            $objects[$object->name] = $object->uri;
            $keywords->add($object->name);
        }
        $keywords->onlyFirst = true;
        $tag = $keywords->getTag();
        $tag->setTemplate('<a href="{@value}"><xsl:apply-templates/></a>');
        $tag->attributes['value']
            ->filterChain
            ->append($config->attributeFilters->get('#hashmap'))
            ->setMap($objects);
        $config->SphinxGlossary = $keywords;
        unset($config->Keywords);
    }
}
