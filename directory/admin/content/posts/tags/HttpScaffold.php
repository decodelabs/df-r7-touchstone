<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\tags;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Tags';
    const DIRECTORY_ICON = 'tag';
    const RECORD_ADAPTER = 'axis://touchstone/Tag';
    const RECORD_NAME_FIELD = 'slug';

    protected $_sections = [
        'details',
        'posts' => [
            'icon' => 'post'
        ]
    ];

    protected $_recordListFields = [
        'slug', 'name', 'posts', 'actions'
    ];

// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query
            ->countRelation('posts');
    }

    protected function _fetchSectionItemCounts() {
        $tag = $this->getRecord();

        return [
            'posts' => $tag->posts->select()->count()
        ];
    }

// Sections
    public function renderPostsSectionBody($tag) {
        return $this->apex->scaffold('../')
            ->renderRecordList(
                $tag->posts->select()
            );
    }

// Components
    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('../categories/', $this->_('Categories'))
                ->setIcon('category')
                ->setDisposition('transitive')
        );  
    }
}