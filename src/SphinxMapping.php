<?php

namespace Club1\SphinxGlossary;

use Flarum\Database\AbstractModel;

/**
 * @property string $id
 * @property string $base_url
 * @property string $inventory_url
 */
class SphinxMapping extends AbstractModel
{
    protected $table = 'sphinx_mappings';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Get the objects of this mapping.
     */
    public function objects()
    {
        return $this->hasMany(SphinxObject::class);
    }
}
