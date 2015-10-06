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

class Unit extends axis\unit\table\Base {

    protected $_defaultSearchFields = [
        'name' => 5,
        'description' => 1
    ];

    protected $_defaultOrderableFields = [
        'slug', 'name'
    ];

    protected $_defaultOrder = 'name ASC';

    protected function createSchema($schema) {
        $schema->addField('id', 'AutoId');
        $schema->addField('name', 'Text', 128);
        $schema->addField('slug', 'Slug');

        $schema->addField('description', 'Text');
        $schema->addField('posts', 'OneToMany', 'post', 'category');

        $schema->addField('image', 'One', 'media/file')
            ->isNullable(true);

        $schema->addField('color', 'Text', 7)
            ->isNullable(true);
    }
}