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

class Unit extends axis\unit\Table {

    const ORDERABLE_FIELDS = [
        'title', 'owner', 'post', 'creationDate', 'lastEditDate', 'displayIntro'
    ];

    const DEFAULT_ORDER = 'creationDate DESC';

    protected function createSchema($schema) {
        $schema->addField('id', 'AutoId');

        $schema->addField('title', 'Text', 128);
        $schema->addField('post', 'ManyToOne', 'touchstone/post', 'versions');

        $schema->addIndexedField('creationDate', 'Timestamp');
        $schema->addField('lastEditDate', 'Timestamp')
            ->shouldTimestampAsDefault(false)
            ->isNullable(true);

        $schema->addField('owner', 'One', 'user/client');

        $schema->addField('headerImage', 'One', 'media/file')
            ->isNullable(true);

        $schema->addField('intro', 'ContentBlock');
        $schema->addField('displayIntro', 'Boolean')
            ->setDefaultValue(true);

        $schema->addField('body', 'ContentSlot')
            ->isNullable(true);
    }
}