<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\touchstone\tag;

use df;
use df\core;
use df\apex;
use df\axis;
use df\opal;

class Unit extends axis\unit\Table
{
    const ORDERABLE_FIELDS = [
        'slug', 'name'
    ];

    const DEFAULT_ORDER = 'name ASC';

    protected function createSchema($schema)
    {
        $schema->addField('id', 'AutoId');
        $schema->addField('slug', 'Slug');
        $schema->addField('name', 'Text', 128);
        $schema->addField('posts', 'ManyToMany', 'post', 'tags');
    }

    public function ensureTagExists($slug, $name=null)
    {
        if ($name === null) {
            $name = $this->context->format->name($slug);
        }

        $slug = $this->context->format->slug($slug);

        if (!$output = $this->fetch()->where('slug', '=', $slug)->toRow()) {
            $output = $this->newRecord([
                    'slug' => $slug,
                    'name' => $name
                ])
                ->save();
        }

        return $output;
    }

    // Query blocks
    public function applyLinkRelationQueryBlock(opal\query\IReadQuery $query, opal\query\IField $relationField, array $extraFields=null)
    {
        if (!$query instanceof opal\query\IPopulatableQuery) {
            return;
        }

        $query->populateSelect($relationField, 'id', 'name', 'slug', $extraFields);
    }
}
