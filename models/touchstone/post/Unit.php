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

use DecodeLabs\Disciple;

class Unit extends axis\unit\Table
{
    const ORDERABLE_FIELDS = [
        'slug', 'owner', 'creationDate', 'postDate', 'lastEditDate', 'archiveDate', 'isLive'
    ];

    const DEFAULT_ORDER = ['postDate DESC', 'creationDate DESC'];

    protected function createSchema($schema)
    {
        $schema->addField('id', 'AutoId');
        $schema->addField('slug', 'Slug');

        $schema->addField('category', 'ManyToOne', 'category', 'posts')
            ->isNullable(true);
        $schema->addField('tags', 'ManyToMany', 'tag', 'posts')
            ->isDominant(true);

        $schema->addIndexedField('creationDate', 'Timestamp');
        $schema->addField('postDate', 'Date');
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


    public function selectForIndex(string $categorySlug=null, ?string ...$tagSlugs)
    {
        if (empty($tagSlugs[0] ?? null)) {
            unset($tagSlugs[0]);
        }

        return $this->select('id', 'slug', 'creationDate', 'postDate')
            ->joinRelation('activeVersion', 'title', 'intro', 'displayIntro', 'body')
            ->importRelationBlock('activeVersion.headerImage', 'link')
            ->importRelationBlock('owner', 'link')
            ->selectAttachRelation('tags', 'id', 'slug', 'name')
                ->orderBy('name ASC')
                ->asMany('tags')

            ->chainIf(!empty($categorySlug), function ($query) use ($categorySlug) {
                $query->whereCorrelation('category', 'in', 'id')
                    ->from('axis://touchstone/Category', 'category')
                    ->where('category.slug', '=', $categorySlug)
                    ->endCorrelation();
            })

            ->chainIf(!empty($tagSlugs), function ($query) use ($tagSlugs) {
                $query->whereCorrelation('id', 'in', 'post')
                    ->from($this->getBridgeUnit('tags'), 'bridge')
                    ->whereCorrelation('bridge.tag', 'in', 'id')
                        ->from('axis://touchstone/Tag', 'tag')
                        ->where('tag.slug', 'in', $tagSlugs)
                        ->endCorrelation()
                    ->endCorrelation();
            })

            ->where('isLive', '=', true)
            ;
    }

    public function selectForCategoryList(string ...$categories)
    {
        return $this->select('slug')
            ->joinRelation('activeVersion', 'title')
            ->whereCorrelation('category', 'in', 'id')
                ->from('axis://touchstone/Category')
                ->where('slug', 'in', $categories)
                ->endCorrelation()
            ->where('isLive', '=', true)
            ->orderBy('postDate DESC', 'creationDate DESC');
    }

    public function selectForReading(?string $slug)
    {
        return $this->context->data->selectForAction(
            $this, ['*'],
            ['slug' => $slug],
            function ($query) {
                $query
                    ->joinRelation('activeVersion', 'title', 'intro', 'displayIntro', 'body')
                    ->importRelationBlock('activeVersion.headerImage', 'link')
                    ->importRelationBlock('owner', 'link')
                    ->importRelationBlock('category', 'link', ['slug', 'color'])
                    ->selectAttachRelation('tags', 'slug', 'name')
                        ->orderBy('name ASC')
                        ->asList('tags', 'slug', 'name')

                    ->chainIf(!Disciple::isA('admin', 'developer'), function ($query) {
                        $query->where('isLive', '=', true);
                    })
                    ;
            }
        );
    }

    public function getPrevNext($post, string $categorySlug=null, ?string ...$tagSlugs): array
    {
        return [
            'prev' => $this->_prevNextQuery('DESC', $post, $categorySlug, ...$tagSlugs)->toRow(),
            'next' => $this->_prevNextQuery('ASC', $post, $categorySlug, ...$tagSlugs)->toRow()
        ];
    }

    protected function _prevNextQuery(string $direction, $post, string $categorySlug=null, ?string ...$tagSlugs)
    {
        return $this->select('id', 'slug')
            ->joinRelation('activeVersion', 'title')
            ->chainIf(!empty($categorySlug), function ($query) use ($categorySlug) {
                $query->whereCorrelation('category', 'in', 'id')
                    ->from('axis://touchstone/Category', 'category')
                    ->where('category.slug', '=', $categorySlug)
                    ->endCorrelation();
            })

            ->chainIf(!empty($tagSlugs), function ($query) use ($tagSlugs) {
                $query->whereCorrelation('id', 'in', 'post')
                    ->from($this->getBridgeUnit('tags'), 'bridge')
                    ->whereCorrelation('bridge.tag', 'in', 'id')
                        ->from('axis://touchstone/Tag', 'tag')
                        ->where('tag.slug', 'in', $tagSlugs)
                        ->endCorrelation()
                    ->endCorrelation();
            })

            ->where('postDate', $direction == 'ASC' ? '>=' : '<=', $post['postDate'])
            ->where('creationDate', $direction == 'ASC' ? '>=' : '<=', $post['creationDate'])
            ->where('isLive', '=', true)
            ->where('id', '!=', $post['id'])
            ->orderBy('postDate '.$direction, 'creationDate '.$direction);
    }


    // Query blocks
    public function applyTitleQueryBlock(opal\query\IReadQuery $query)
    {
        if (!$query instanceof opal\query\IJoinableQuery) {
            return;
        }

        $query->leftJoinRelation('activeVersion', 'title');
    }

    public function applyActiveVersionQueryBlock(opal\query\IReadQuery $query, $body=false)
    {
        if (!$query instanceof opal\query\IJoinableQuery) {
            return;
        }

        $fields = ['title', 'headerImage', 'intro', 'displayIntro'];

        if ($body) {
            $fields[] = 'body';
        }

        $query->leftJoinRelation('activeVersion', $fields);
    }

    public function applyTagSlugClauseQueryBlock(opal\query\IReadQuery $query, array $slugs)
    {
        if (!$query instanceof opal\query\IWhereClauseQuery) {
            return;
        }

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
