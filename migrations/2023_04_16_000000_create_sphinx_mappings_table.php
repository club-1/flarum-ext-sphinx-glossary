<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable('sphinx_mappings', function (Blueprint $table) {
    $table->string('id', 64);
    $table->primary('id');
    $table->string('base_url', 500);
    $table->string('inventory_url', 512);
});
