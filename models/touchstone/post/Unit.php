<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\touchstone\post;

use df;
use df\core;
use df\apex;
use df\axis;
use df\opal;
    
class Unit extends axis\unit\table\Base {

    protected function _onCreate(axis\schema\ISchema $schema) {
        // Id
        $schema->addField('id', 'AutoId');

        // Slug
        $schema->addField('slug', 'Slug');

        // Labels
        $schema->addField('label', 'Many', 'nightfire/label');

        // Created
        $schema->addIndexedField('creationDate', 'Timestamp');

        // Last edited
        $schema->addField('lastEditDate', 'Timestamp')
            ->shouldTimestampAsDefault(false)
            ->isNullable(true);

        // Archive date
        $schema->addField('archiveDate', 'Date');

        // Owner
        $schema->addField('owner', 'One', 'user/client');

        // Versions
        $schema->addField('verions', 'OneToMany', 'touchstone/postVersion', 'post');

        // Active version
        $schema->addField('activeVersion', 'One', 'touchstone/postVersion');

        // Is live
        $schema->addIndexedField('isLive', 'Boolean');
    }

    public function applyPagination(opal\query\IPaginator $paginator) {
        $paginator->setOrderableFields('slug', 'primaryLabel', 'creationDate', 'lastEditDate', 'archiveDate');
        return $this;
    }
}