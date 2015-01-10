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

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Posts';
    const DIRECTORY_ICON = 'post';
    const RECORD_ADAPTER = 'axis://touchstone/Post';
    const RECORD_NAME_FIELD = 'title';

    protected $_sections = [
        'details',
        'comments' => [
            'icon' => 'comment'
        ],
        'versions' => [
            'icon' => 'list'
        ]
    ];

    protected $_recordListFields = [
        'title', 'category', 'tags', 'labels', 'owner', 'creationDate', 'lastEditDate',
        'archiveDate', 'versions', 'isLive', 'actions'
    ];

    protected $_recordDetailsFields = [
        'title', 'slug', 'owner', 'isPersonal', 'isLive', 'category', 'tags',
        'versions', 'creationDate', 'lastEditDate', 'archiveDate',
        'labels', 'headerImage', 'intro', 'body'
    ];

// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query->countRelation('versions')
            ->importRelationBlock('category', 'link')
            ->importRelationBlock('tags', 'link')
            ->importRelationBlock('labels', 'link')
            ->importRelationBlock('owner', 'link')
            ->importBlock('title')
            ->paginate()
                ->addOrderableFields('title');
    }

    public function applyRecordQuerySearch(opal\query\ISelectQuery $query, $search, $mode) {
        $query->searchFor($search, [
            'title' => 10,
            'jrl_activeVersion.body' => 0.5
        ]);
    }

    protected function _fetchSectionItemCounts() {
        $post = $this->getRecord();

        return [
            'versions' => $post->versions->select()->count(),
            'comments' => $this->data->interact->comment->countFor($post)
        ];
    }

    protected function _getRecordName($record) {
        if(isset($record['title'])) {
            return $record['title'];
        } else {
            return $record['slug'];
        }
    }

// Components
    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('./categories/', $this->_('Categories'))
                ->setIcon('category')
                ->setDisposition('transitive'),

            $this->html->link('./tags/', $this->_('Tags'))
                ->setIcon('tag')
                ->setDisposition('transitive'),

            $this->html->link(
                    '~admin/navigation/labels/',
                    $this->_('Labels')
                )
                ->setIcon('label')
                ->setDisposition('transitive')
        );  
    }


// Sections
    public function renderCommentsSectionBody($post) {
        $output = [];

        if(!$post['allowComments']) {
            $output[] = $this->html->flashMessage($this->_(
                'Comments are currently disabled for this post'
            ), 'warning');
        }

        $output[] = $this->apex->component('~/comments/Comment', $post->getEntityLocator())
            ->shouldDisplayAsTree(true)
            ->shouldShowForm($post['allowComments'])
            ->shouldShowInactive(true);

        return $output;
    }

    public function renderVersionsSectionBody($post) {
        $template = $this->apex->template('Versions.html');
        $template['post'] = $post;
        $template['versionList'] = $post->versions->fetch()
            ->orderBy('creationDate DESC');

        return $template;
    }

// Fields
    public function defineTitleField($list, $mode) {
        if($mode == 'list') {
            return false;
        }

        $list->addField('title', function($post) {
            if($post['title']) {
                return $post['title'];
            }

            return $post['activeVersion']['title'];
        });
    }

    public function defineIsPersonalField($list, $mode) {
        $list->addField('isPersonal', function($post) {
            return $this->html->booleanIcon($post['isPersonal']);
        });
    }

    public function defineCategoryField($list, $mode) {
        $list->addField('category', function($post) {
            return $this->apex->component('./categories/CategoryLink', $post['category'])
                ->isNullable(true)
                ->setDisposition('transitive');
        });
    }

    public function defineTagsField($list, $mode) {
        $list->addField('tags', function($post) use($mode) {
            if($mode == 'list') {
                $tags = $post['tags'];
            } else {
                $tags = $post->tags->select();
            }

            return $this->html->commaList($tags, function($tag) {
                return $this->apex->component('./tags/TagLink', $tag)
                    ->setDisposition('transitive');
            });
        });
    }

    public function defineLabelsField($list, $mode) {
        $list->addField('labels', function($post) use($mode) {
            if($mode == 'list') {
                $labels = $post['labels'];
            } else {
                $labels = $post->labels->select();
            }

            return $this->html->bulletList($labels, function($label) {
                return $this->apex->component('~admin/navigation/labels/LabelLink', $label)
                    ->setDisposition('transitive');
            });
        });
    }

    public function defineArchiveDateField($list, $mode) {
        $list->addField('archiveDate', $this->_('Archive'), function($post, $context) {
            if($post['archiveDate']) {
                $output = $this->html->date($post['archiveDate'], 'short');

                if($post['archiveDate']->isPast()) {
                    $output->addClass('negative');
                    $context->getRowTag()->addClass('inactive');
                }

                return $output;
            }
        });
    }

    public function defineVersionsField($list, $mode) {
        if($mode == 'list') {
            $list->addField('versions', $this->_('V.'));
        } else {
            return false;
        }
    }

    public function defineHeaderImageField($list, $mode) {
        $list->addField('headerImage', function($post) {
            return $this->apex->component('~admin/media/FileLink', $post['activeVersion']['headerImage'])
                ->isNullable(true);
        });
    }

    public function defineIntroField($list, $mode) {
        $list->addField('intro', function($post) {
            return $this->nightfire->renderBlock($post['activeVersion']['intro'], 'Description');
        });
    }

    public function defineBodyField($list, $mode) {
        $list->addField('body', function($post) {
            return $this->nightfire->renderSlot($post['activeVersion']['body'], 'Article');
        });
    }
}