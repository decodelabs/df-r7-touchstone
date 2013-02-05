<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\touchstone\label;

use df;
use df\core;
use df\apex;
use df\axis;
    
class Unit extends axis\unit\table\SlugTree {

    protected function _onCreate(axis\schema\ISchema $schema) {
        $schema->addField('name', 'String', 128);

        $contextField = $schema->addField('context', 'String', 128);
        $schema->getIndex('slug')->addField($contextField);

        $schema->addField('isShared', 'Boolean');

        $schema->addField('description', 'String', 255);

        $schema->addField('owner', 'One', 'user/client');
    }

    public function fetchBySlug($slug, $context=null) {
        $query = $this->fetch()
            ->where('slug', '=', $slug);

        if($context !== null) {
            $query->where('context', '=', $context);
        }
        
        return $query
            ->orderBy('isShared DESC', 'context ASC')
            ->toRow();
    }
}