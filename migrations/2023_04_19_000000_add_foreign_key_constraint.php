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

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        // Manually delete the invalid rows that should have been deleted by the
        // foreign key contraints and that would prevent us from adding them.
        $connection = $schema->getConnection();
        $connection->table('sphinx_objects')->whereNotIn('sphinx_mapping_id', function (QueryBuilder $query) {
            $query->select('id')->from('sphinx_mappings');
        })->delete();

        // Add the foreign key constraints.
        $schema->table('sphinx_objects', function (Blueprint $table) {
            $table->foreign('sphinx_mapping_id')->references('id')->on('sphinx_mappings')->cascadeOnDelete();
        });
    },
    'down' => function (Builder $schema) {
        $schema->table('sphinx_objects', function (Blueprint $table) {
            $table->dropForeign(['sphinx_mapping_id']);
        });
    }
];
