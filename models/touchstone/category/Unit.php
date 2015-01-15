<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\touchstone\category;

use df;
use df\core;
use df\apex;
use df\axis;
use df\opal;

class Unit extends axis\unit\table\Base {
    
    protected static $_defaultSearchFields = [
        'name' => 5,
        'description' => 1
    ];

    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addField('id', 'AutoId');
        $schema->addField('name', 'String', 128);
        $schema->addField('slug', 'Slug');

        $schema->addField('description', 'BigString');
        $schema->addField('posts', 'OneToMany', 'post', 'category');

        $schema->addField('legacy_image_id', 'Integer')
            ->isNullable(true);
        $schema->addField('image', 'One', 'media/file')
            ->isNullable(true);

        $schema->addField('color', 'String', 7)
            ->isNullable(true);
    }

    public function applyPagination(opal\query\IPaginator $paginator) {
        $paginator->setOrderableFields('slug', 'name')
            ->setDefaultOrder('name ASC');
            
        return $this;
    }
}