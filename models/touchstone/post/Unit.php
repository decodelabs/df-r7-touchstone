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
        $schema->addField('labels', 'Many', 'nightfire/label');

        // Created
        $schema->addIndexedField('creationDate', 'Timestamp');

        // Last edited
        $schema->addField('lastEditDate', 'Timestamp')
            ->shouldTimestampAsDefault(false)
            ->isNullable(true);

        // Archive date
        $schema->addField('archiveDate', 'Date')
            ->isNullable(true);

        // Owner
        $schema->addField('owner', 'One', 'user/client');

        // Is personal
        $schema->addField('isPersonal', 'Boolean');

        // Versions
        $schema->addField('versions', 'OneToMany', 'touchstone/postVersion', 'post');

        // Active version
        $schema->addField('activeVersion', 'One', 'touchstone/postVersion');

        // Comments
        $schema->addField('allowComments', 'Boolean')
            ->setDefaultValue(true);

        // Is live
        $schema->addIndexedField('isLive', 'Boolean');
    }

    public function applyPagination(opal\query\IPaginator $paginator) {
        $paginator->setOrderableFields('slug', 'owner', 'isPersonal', 'creationDate', 'lastEditDate', 'archiveDate', 'isLive')
            ->setDefaultOrder('creationDate DESC');
            
        return $this;
    }
}