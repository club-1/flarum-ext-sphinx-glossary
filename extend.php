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
use Club1\SphinxGlossary\Console\SphinxListCommand;
use Club1\SphinxGlossary\Console\SphinxRemoveCommand;
use Club1\SphinxGlossary\Console\SphinxUpdateCommand;
use Club1\SphinxGlossary\Formatter\SphinxGlossaryConfigurator;
use Flarum\Extend;
use Flarum\Foundation\Paths;
use Illuminate\Console\Scheduling\Event;

return [
    (new Extend\Formatter)
        ->configure(SphinxGlossaryConfigurator::class),

    (new Extend\Console())
        ->command(SphinxAddCommand::class)
        ->command(SphinxRemoveCommand::class)
        ->command(SphinxListCommand::class)
        ->command(SphinxUpdateCommand::class)
        ->schedule('sphinx:update', function (Event $event) {
            $event->daily();
        }),

    (new Extend\Filesystem)
        ->disk('club-1-sphinx-glossary', function (Paths $paths) {
            return [
                'root' => "$paths->storage/cache/club-1-sphinx-glossary",
            ];
        }),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/css/forum.css'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),
];
