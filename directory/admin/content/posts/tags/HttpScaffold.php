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

class HttpScaffold extends arch\scaffold\RecordAdmin {

    const TITLE = 'Tags';
    const ICON = 'tag';
    const ADAPTER = 'axis://touchstone/Tag';
    const NAME_FIELD = 'slug';

    const SECTIONS = [
        'details',
        'posts' => 'post'
    ];

    const LIST_FIELDS = [
        'slug', 'name', 'posts'
    ];

// Record data
    protected function prepareRecordList($query, $mode) {
        $query
            ->countRelation('posts');
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