<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

use DecodeLabs\Tagged\Html;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Posts';
    const ICON = 'post';
    const ADAPTER = 'axis://touchstone/Post';
    const NAME_FIELD = 'title';

    const SECTIONS = [
        'details',
        'comments' => 'comment',
        'versions' => 'list'
    ];

    const LIST_FIELDS = [
        'title', 'category', 'tags', 'owner', 'postDate', 'lastEditDate',
        'archiveDate', 'versions', 'isLive'
    ];

    const DETAILS_FIELDS = [
        'title', 'slug', 'owner', 'isLive', 'category', 'tags',
        'versions', 'creationDate', 'postDate', 'lastEditDate', 'archiveDate',
        'headerImage', 'intro', 'body'
    ];

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query->countRelation('versions')
            ->importRelationBlock('category', 'link')
            ->importRelationBlock('tags', 'link')
            ->importRelationBlock('owner', 'link')
            ->importBlock('title')
            ->paginate()
                ->addOrderableFields('title');
    }

    protected function searchRecordList($query, $search)
    {
        $query->searchFor($search, [
            'title' => 10,
            'activeVersion.intro' => 0.7,
            'activeVersion.body' => 0.5
        ]);
    }

    protected function countSectionItems($post)
    {
        return [
            'versions' => $post->versions->countAll(),
            'comments' => $this->data->content->comment->countFor($post)
        ];
    }

    protected function nameRecord($record)
    {
        if (isset($record['title'])) {
            return $record['title'];
        } else {
            return $record['slug'];
        }
    }

    // Components
    public function addIndexTransitiveLinks($menu, $bar)
    {
        $menu->addLinks(
            $this->html->link('./categories/', $this->_('Categories'))
                ->setIcon('category')
                ->setDisposition('transitive'),

            $this->html->link('./tags/', $this->_('Tags'))
                ->setIcon('tag')
                ->setDisposition('transitive')
        );
    }


    // Sections
    public function renderCommentsSectionBody($post)
    {
        if (!$post['allowComments']) {
            yield $this->html->flashMessage($this->_(
                'Comments are currently disabled for this post'
            ), 'warning');
        }

        yield $this->apex->component('~/comments/Comment', $post->getEntityLocator())
            ->shouldDisplayAsTree(true)
            ->shouldShowForm($post['allowComments'])
            ->shouldShowInactive(true);
    }

    public function renderVersionsSectionBody($post)
    {
        $template = $this->apex->template('Versions.html');
        $template['post'] = $post;
        $template['versionList'] = $post->versions->fetch()
            ->orderBy('creationDate DESC');

        return $template;
    }

    // Fields
    public function defineTitleField($list, $mode)
    {
        if ($mode == 'list') {
            return false;
        }

        $list->addField('title', function ($post) {
            if ($post['title']) {
                return $post['title'];
            }

            return $post['activeVersion']['title'];
        });
    }

    public function defineCategoryField($list, $mode)
    {
        $list->addField('category', function ($post) {
            return $this->apex->component('./categories/CategoryLink', $post['category'])
                ->isNullable(true);
        });
    }

    public function defineTagsField($list, $mode)
    {
        $list->addField('tags', function ($post, $context) use ($mode) {
            if ($mode == 'list') {
                $tags = $post['tags'];
                $context->cellTag['width'] = '20%';
            } else {
                $tags = $post->tags->select();
            }

            return Html::iList($tags, function ($tag) {
                return $this->apex->component('./tags/TagLink', $tag);
            }, null, null, 9);
        });
    }

    public function definePostDateField($list, $mode)
    {
        $list->addField('postDate', $this->_('Posted'), function ($post) {
            return Html::$time->date($post['postDate']);
        });
    }

    public function defineArchiveDateField($list, $mode)
    {
        $list->addField('archiveDate', $this->_('Archive'), function ($post, $context) {
            if ($post['archiveDate']) {
                $output = Html::$time->shortDate($post['archiveDate']);

                if ($post['archiveDate']->isPast()) {
                    $output->addClass('negative');
                    $context->getRowTag()->addClass('inactive');
                }

                return $output;
            }
        });
    }

    public function defineVersionsField($list, $mode)
    {
        if ($mode == 'list') {
            $list->addField('versions', $this->_('V.'));
        } else {
            return false;
        }
    }

    public function defineHeaderImageField($list, $mode)
    {
        $list->addField('headerImage', function ($post) {
            return $this->apex->component('~admin/media/files/FileLink', $post['activeVersion']['headerImage'])
                ->isNullable(true);
        });
    }

    public function defineIntroField($list, $mode)
    {
        $list->addField('intro', function ($post) {
            return $this->nightfire->renderBlock($post['activeVersion']['intro']);
        });
    }

    public function defineBodyField($list, $mode)
    {
        $list->addField('body', function ($post) {
            return $this->nightfire->renderSlot($post['activeVersion']['body']);
        });
    }
}
