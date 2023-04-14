<?php

/*
 * This file is part of club-1/flarum-ext-sphinx-glossary.
 *
 * Copyright (c) 2023 Nicolas Peugnet.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


use Club1\SphinxGlossary\Console\SphinxUpdateCommand;
use Flarum\Extend;

return [
    (new Extend\Console())->command(SphinxUpdateCommand::class),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),
];
