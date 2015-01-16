<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\touchstone\postVersion;

use df;
use df\core;
use df\apex;
use df\axis;
use df\opal;
    
class Unit extends axis\unit\table\Base {

    protected function _onCreate(axis\schema\ISchema $schema) {
        // Id
        $schema->addField('id', 'AutoId');

        // Post
        $schema->addField('post', 'ManyToOne', 'touchstone/post', 'versions');

        // Created
        $schema->addIndexedField('creationDate', 'Timestamp');

        // Last edited
        $schema->addField('lastEditDate', 'Timestamp')
            ->shouldTimestampAsDefault(false)
            ->isNullable(true);

        // Owner
        $schema->addField('owner', 'One', 'user/client');

        // Title
        $schema->addField('title', 'String', 128);

        // Image
        $schema->addField('headerImage', 'One', 'media/file')
            ->isNullable(true);

        // Intro
        $schema->addField('intro', 'ContentBlock', 'Description');

        // Display intro
        $schema->addField('displayIntro', 'Boolean')
            ->setDefaultValue(true);

        // Body
        $schema->addField('body', 'ContentSlot', 'Article');
    }

    public function applyPagination(opal\query\IPaginator $paginator) {
        $paginator->setOrderableFields('title', 'owner', 'post', 'creationDate', 'lastEditDate', 'displayIntro')
            ->setDefaultOrder('creationDate DESC');
            
        return $this;
    }
}