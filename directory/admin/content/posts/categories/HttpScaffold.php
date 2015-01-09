<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\categories;

use df;
use df\core;
use df\apex;
use df\arch;
use df\opal;

class HttpScaffold extends arch\scaffold\template\RecordAdmin {
    
    const DIRECTORY_TITLE = 'Categories';
    const DIRECTORY_ICON = 'category';
    const RECORD_ADAPTER = 'axis://touchstone/Category';

    protected $_sections = [
        'details',
        'posts' => [
            'icon' => 'post'
        ]
    ];

    protected $_recordListFields = [
        'name', 'posts', 'image', 'color', 'actions'
    ];

    protected $_recordDetailsFields = [
        'name', 'slug', 'image', 'color', 'description'
    ];


// Record data
    protected function _prepareRecordListQuery(opal\query\ISelectQuery $query, $mode) {
        $query
            ->countRelation('posts')
            ->importRelationBlock('image', 'link');
    }

    protected function _fetchSectionItemCounts() {
        $category = $this->getRecord();

        return [
            'posts' => $category->posts->select()->count()
        ];
    }


// Sections
    public function renderPostsSectionBody($category) {
        return $this->apex->scaffold('../')
            ->renderRecordList(
                $category->posts->select(),
                ['category' => false]
            );
    }


// Components
    public function addPostsSectionSubOperativeLinks($menu, $bar) {
        $category = $this->getRecord();

        $menu->addLinks(
            $this->html->link(
                    $this->uri('../add?category='.$category['id'], true), 
                    $this->_('Add post')
                )
                ->setIcon('add')
        );
    }


// Fields
    public function defineImageField($list, $mode) {
        $list->addField('image', function($category) {
            return $this->apex->component('~admin/media/FileLink', $category['image'])
                ->isNullable(true)
                ->setDisposition('transitive');
        });
    }

    public function defineDescriptionField($list, $mode) {
        $list->addField('description', function($category) {
            return $this->html->simpleTags($category['description']);
        });
    }
}