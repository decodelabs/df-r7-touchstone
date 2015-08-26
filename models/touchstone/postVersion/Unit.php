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

    protected $_defaultOrderableFields = [
        'title', 'owner', 'post', 'creationDate', 'lastEditDate', 'displayIntro'
    ];

    protected $_defaultOrder = 'creationDate DESC';

    protected function createSchema($schema) {
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
}