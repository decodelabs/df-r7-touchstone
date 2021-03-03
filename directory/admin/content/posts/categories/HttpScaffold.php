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

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    const TITLE = 'Categories';
    const ICON = 'category';
    const ADAPTER = 'axis://touchstone/Category';

    const SECTIONS = [
        'details',
        'posts' => 'post'
    ];

    const LIST_FIELDS = [
        'name', 'posts', 'image', 'color'
    ];

    const DETAILS_FIELDS = [
        'name', 'slug', 'image', 'color', 'description'
    ];


    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->countRelation('posts')
            ->importRelationBlock('image', 'link');
    }


    // Sections
    public function renderPostsSectionBody($category)
    {
        return $this->apex->scaffold('../')
            ->renderRecordList(function ($query) use ($category) {
                $query->where('category', '=', $category['id']);
            }, [
                'category' => false
            ]);
    }


    // Components
    public function generatePostsSectionSubOperativeLinks(): iterable
    {
        yield 'add' => $this->html->link(
                $this->uri('../add?category='.$this->getRecordId(), true),
                $this->_('Add post')
            )
            ->setIcon('add');
    }

    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'tags' => $this->html->link('../tags/', $this->_('Tags'))
            ->setIcon('tag')
            ->setDisposition('transitive');
    }


    // Fields
    public function defineImageField($list, $mode)
    {
        $list->addField('image', function ($category) {
            return $this->apex->component('~admin/media/files/FileLink', $category['image'])
                ->isNullable(true);
        });
    }

    public function defineDescriptionField($list, $mode)
    {
        $list->addField('description', function ($category) {
            return $this->html->simpleTags($category['description']);
        });
    }
}
