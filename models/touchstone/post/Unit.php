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

    const ORDERABLE_FIELDS = [
        'slug', 'owner', 'creationDate', 'lastEditDate', 'archiveDate', 'isLive'
    ];

    const DEFAULT_ORDER = 'creationDate DESC';

    protected function createSchema($schema) {
        $schema->addField('id', 'AutoId');
        $schema->addField('slug', 'Slug');

        $schema->addField('category', 'ManyToOne', 'category', 'posts')
            ->isNullable(true);
        $schema->addField('tags', 'ManyToMany', 'tag', 'posts')
            ->isDominant(true);

        $schema->addIndexedField('creationDate', 'Timestamp');
        $schema->addField('lastEditDate', 'Timestamp')
            ->shouldTimestampAsDefault(false)
            ->isNullable(true);
        $schema->addField('archiveDate', 'Date')
            ->isNullable(true);

        $schema->addField('owner', 'One', 'user/client');

        $schema->addField('versions', 'OneToMany', 'touchstone/postVersion', 'post');
        $schema->addField('activeVersion', 'One', 'touchstone/postVersion');

        $schema->addField('allowComments', 'Boolean')
            ->setDefaultValue(true);

        $schema->addIndexedField('isLive', 'Boolean');
    }


// Query blocks
    public function applyTitleQueryBlock(opal\query\IReadQuery $query) {
        $query->leftJoinRelation('activeVersion', 'title');
    }

    public function applyActiveVersionQueryBlock(opal\query\IReadQuery $query, $body=false) {
        $fields = ['title', 'headerImage', 'intro', 'displayIntro'];

        if($body) {
            $fields[] = 'body';
        }

        $query->leftJoinRelation('activeVersion', $fields);
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