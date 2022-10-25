<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\content\posts\_nodes;

use df\arch\node\form\SelectorDelegate;
use df\apex\directory\shared\media\_formDelegates\FileSelector;
use df\apex\directory\shared\nightfire\_formDelegates\ContentBlock;
use df\apex\directory\shared\nightfire\_formDelegates\ContentSlot;

use DecodeLabs\Exceptional;

class HttpEdit extends HttpAdd
{
    protected function init(): void
    {
        $this->_post = $this->scaffold->getRecord();

        $versionId = null;

        if (isset($this->request['version'])) {
            $this->_keepVersion = true;
            $versionId = $this->request['version'];
        } elseif (isset($this->request['rebase'])) {
            $versionId = $this->request['rebase'];
        }

        if ($versionId) {
            $this->_version = $this->_post->versions->fetch()
                ->where('id', '=', $versionId)
                ->toRow();
        } else {
            $this->_version = $this->_post['activeVersion'];
        }

        if (!$this->_version) {
            throw Exceptional::{'df/opal/record/NotFound'}([
                'message' => 'Version not found',
                'http' => 404
            ]);
        }
    }

    protected function getInstanceId(): ?string
    {
        return $this->_post['id'].':'.$this->_version['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_post, [
            'slug', 'postDate', 'archiveDate', 'isLive', 'allowComments'
        ]);

        $this->values->importFrom($this->_version, [
            'title', 'displayIntro'
        ]);

        $this['category']->as(SelectorDelegate::class)
            ->setSelected($this->_post['#category']);
        $this['tags']->as(SelectorDelegate::class)
            ->setSelected($this->_post['#tags']);

        $this['headerImage']->as(FileSelector::class)
            ->setSelected($this->_version['#headerImage']);
        $this['intro']->as(ContentBlock::class)
            ->setBlock($this->_version['intro']);
        $this['body']->as(ContentSlot::class)
            ->setSlotContent($this->_version['body']);
    }
}
