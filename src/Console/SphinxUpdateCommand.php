<?php

namespace Club1\SphinxGlossary\Console;

use Club1\SphinxInventoryParser\SphinxInventoryParser;
use Flarum\Console\AbstractCommand;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use UnexpectedValueException;

class SphinxUpdateCommand extends AbstractCommand
{
    /** @var Filesystem $cacheDir */
    protected $cacheDir;

    public function __construct(Factory $filesystemFactory)
    {
        parent::__construct();
        $this->cacheDir = $filesystemFactory->disk('club-1-sphinx-glossary');
    }

    protected function configure()
    {
        $this
            ->setName('sphinx:update')
            ->setDescription('Update Sphinx glossary entries by downloading the latest inventories');
    }

    protected function fire()
    {
        $inventory = 'https://club-1.github.io/sphinx-inventory-parser/objects.inv';
        $cacheKey = hash('crc32b', $inventory);

        $tmp = tmpfile();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FILE, $tmp);
        curl_setopt($ch, CURLOPT_URL, $inventory);
        if ($this->cacheDir->exists($cacheKey)) {
            $lastModified = $this->cacheDir->lastModified($cacheKey);
            curl_setopt($ch, CURLOPT_TIMEVALUE, $lastModified);
            curl_setopt($ch, CURLOPT_TIMECONDITION, CURL_TIMECOND_IFMODSINCE);
        }
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($code == 200) {
            $this->cacheDir->writeStream($cacheKey, $tmp);
        } elseif ($code == 304) {
            $this->info("Received '304 Not Modified' for inventory '$inventory': Skipping update.");
            return;
        } else {
            throw new UnexpectedValueException("could not fetch inventory '$inventory': code $code");
        }
        fclose($tmp);

        $stream = $this->cacheDir->readStream($cacheKey);
        $parser = new SphinxInventoryParser($stream);
        $header = $parser->parseHeader();
        $objects = $parser->parseObjects($header);
        foreach ($objects as $object) {
            $this->info($object->displayName);
        }
        fclose($stream);
    }
}
