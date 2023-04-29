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

namespace Club1\SphinxGlossary;

use Flarum\Database\AbstractModel;

/**
 * @property string $id
 * @property string $base_url
 * @property string $inventory_url
 * @property string[] $roles
 * @property \Illuminate\Database\Eloquent\Collection $objects
 */
class SphinxMapping extends AbstractModel
{
    protected $table = 'sphinx_mappings';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'roles' => 'array',
    ];

    /**
     * Get the objects of this mapping.
     */
    public function objects()
    {
        return $this->hasMany(SphinxObject::class);
    }
}
