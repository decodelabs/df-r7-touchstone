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
    
class Unit extends axis\unit\table\Base {

    protected function _onCreate(axis\schema\ISchema $schema) {
        // Id
        $schema->addField('id', 'AutoId');

        // Post
        $schema->addField('post', 'ManyToOne', 'touchstone/post', 'versions');

        // Created
        $schema->addIndexedField('creationDate', 'Timestamp');

        // Last edited
        $schema->addField('lastEditDate', 'Timestamp');

        // Archive date
        $schema->addField('archiveDate', 'Date');

        // Owner
        $schema->addField('owner', 'One', 'user/client');

        // Title
        $schema->addField('title', 'String', 128);

        // Intro
        $schema->addField('intro', 'ContentBlock', 'Description');

        // Display intro
        $schema->addField('displayIntro', 'Boolean')
            ->setDefaultValue(true);

        // Body
        $schema->addField('body', 'ContentSlot', 'Article');
    }
}