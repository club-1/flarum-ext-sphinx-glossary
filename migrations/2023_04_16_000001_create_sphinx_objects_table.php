<?php

use Illuminate\Database\Schema\Blueprint;
use Flarum\Database\Migration;

return Migration::createTable('sphinx_objects', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name', 256);
        $table->index('name');
        $table->string('domain');
        $table->index('domain');
        $table->string('role');
        $table->index('role');
        $table->integer('priority');
        $table->string('uri', 512);
        $table->string('display_name', 512);
        $table->string('sphinx_mapping_id', 64);
    }
);

