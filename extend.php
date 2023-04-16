<?php

/*
 * This file is part of club-1/flarum-ext-sphinx-glossary.
 *
 * Copyright (c) 2023 Nicolas Peugnet.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use Club1\SphinxGlossary\Console\SphinxAddCommand;
use Club1\SphinxGlossary\Console\SphinxUpdateCommand;
use Flarum\Extend;
use Flarum\Foundation\Paths;

return [
    (new Extend\Console())
        ->command(SphinxAddCommand::class)
        ->command(SphinxUpdateCommand::class),

    (new Extend\Filesystem)
        ->disk('club-1-sphinx-glossary', function (Paths $paths) {
            return [
                'root' => "$paths->storage/cache/club-1-sphinx-glossary",
            ];
        }),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),
];
