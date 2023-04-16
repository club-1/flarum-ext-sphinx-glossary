<?php

namespace Club1\SphinxGlossary;

use Flarum\Database\AbstractModel;

/**
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $role
 * @property int $priority
 * @property string $uri
 * @property string $display_name
 * @property SphinxMapping $mapping
 */
class SphinxObject extends AbstractModel
{
    protected $table = 'sphinx_objects';

    /**
     * Get the mapping that owns the object.
     */
    public function mapping()
    {
        return $this->belongsTo(SphinxMapping::class);
    }
}
