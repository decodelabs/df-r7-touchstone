<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\tags;

use df\arch;

class HttpScaffold extends arch\scaffold\RecordAdmin
{
    public const TITLE = 'Tags';
    public const ICON = 'tag';
    public const ADAPTER = 'axis://touchstone/Tag';
    public const NAME_FIELD = 'slug';

    public const SECTIONS = [
        'details',
        'posts' => 'post'
    ];

    public const LIST_FIELDS = [
        'slug', 'name', 'posts'
    ];

    // Record data
    protected function prepareRecordList($query, $mode)
    {
        $query
            ->countRelation('posts');
    }


    // Sections
    public function renderPostsSectionBody($tag)
    {
        return $this->apex->scaffold('../')
            ->renderRecordList(function ($query) use ($tag) {
                $query->whereCorrelation('id', 'in', 'post')
                    ->from($this->data->touchstone->post->getBridgeUnit('tags'), 'bridge')
                    ->where('bridge.tag', '=', $tag['id'])
                    ->endCorrelation();
            });
    }

    // Components
    public function generateIndexTransitiveLinks(): iterable
    {
        yield 'categories' => $this->html->link('../categories/', $this->_('Categories'))
            ->setIcon('category')
            ->setDisposition('transitive');
    }
}
