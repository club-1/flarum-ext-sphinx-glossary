<?php

namespace Club1\SphinxGlossary\Console;

use Club1\SphinxInventoryParser\SphinxInventoryParser;
use Flarum\Console\AbstractCommand;

class SphinxUpdateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('sphinx:update')
            ->setDescription('Update Sphinx glossary entries by downloading the latest inventories');
    }

    protected function fire()
    {
        $inventory = 'https://club-1.github.io/sphinx-inventory-parser/objects.inv';
        $stream = fopen($inventory, 'r');
        $parser = new SphinxInventoryParser($stream);
        $header = $parser->parseHeader();
        $objects = $parser->parseObjects($header);
        foreach ($objects as $object) {
            $this->info($object->displayName);
        }
        fclose($stream);
    }
}
