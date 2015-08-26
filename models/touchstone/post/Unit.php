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

    protected $_defaultOrderableFields = [
        'slug', 'owner', 'isPersonal', 'creationDate', 'lastEditDate', 'archiveDate', 'isLive'
    ];

    protected $_defaultOrder = 'creationDate DESC';

    protected function createSchema($schema) {
        // Id
        $schema->addField('id', 'AutoId');

        // Slug
        $schema->addField('slug', 'Slug');

        // Category
        $schema->addField('category', 'ManyToOne', 'category', 'posts')
            ->isNullable(true);

        // Tags
        $schema->addField('tags', 'ManyToMany', 'tag', 'posts')
            ->isDominant(true);

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


// Query blocks
    public function applyTitleQueryBlock(opal\query\IReadQuery $query) {
        $query->leftJoinRelation('activeVersion', 'title');
    }

    public function applyTagSlugClauseQueryBlock(opal\query\IReadQuery $query, array $slugs) {
        $query
            ->whereCorrelation('id', 'in', 'post')
                ->from($this->getBridgeUnit('tags'), 'bridge')
                ->whereCorrelation('tag', 'in', 'id')
                    ->from('axis://touchstone/Tag', 'tags')
                    ->where('tags.slug', 'in', $slugs)
                    ->endCorrelation()
                ->endCorrelation();
    }
}