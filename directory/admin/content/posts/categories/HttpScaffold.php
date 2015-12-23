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

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const TITLE = 'Categories';
    const ICON = 'category';
    const ADAPTER = 'axis://touchstone/Category';

    protected $_sections = [
        'details',
        'posts' => 'post'
    ];

    protected $_recordListFields = [
        'name', 'posts', 'image', 'color'
    ];

    protected $_recordDetailsFields = [
        'name', 'slug', 'image', 'color', 'description'
    ];


// Record data
    protected function prepareRecordList($query, $mode) {
        $query
            ->countRelation('posts')
            ->importRelationBlock('image', 'link');
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

    public function addIndexTransitiveLinks($menu, $bar) {
        $menu->addLinks(
            $this->html->link('../tags/', $this->_('Tags'))
                ->setIcon('tag')
                ->setDisposition('transitive')
        );
    }


// Fields
    public function defineImageField($list, $mode) {
        $list->addField('image', function($category) {
            return $this->apex->component('~admin/media/files/FileLink', $category['image'])
                ->isNullable(true);
        });
    }

    public function defineDescriptionField($list, $mode) {
        $list->addField('description', function($category) {
            return $this->html->simpleTags($category['description']);
        });
    }
}