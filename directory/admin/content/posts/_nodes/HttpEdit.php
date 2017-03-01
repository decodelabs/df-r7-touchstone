<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends HttpAdd {

    protected function init() {
        $this->_post = $this->scaffold->getRecord();

        $versionId = null;

        if(isset($this->request['version'])) {
            $this->_keepVersion = true;
            $versionId = $this->request['version'];
        } else if(isset($this->request['rebase'])) {
            $versionId = $this->request['rebase'];
        }

        if($versionId) {
            $this->_version = $this->_post->versions->fetch()
                ->where('id', '=', $versionId)
                ->toRow();
        } else {
            $this->_version = $this->_post['activeVersion'];
        }

        if(!$this->_version) {
            throw core\Error::{'fire/type/EVersion,ENotFound'}([
                'message' => 'Version not found',
                'http' => 404
            ]);
        }
    }

    protected function getInstanceId() {
        return $this->_post['id'].':'.$this->_version['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_post, [
            'slug', 'postDate', 'archiveDate', 'isLive', 'allowComments'
        ]);

        $this->values->importFrom($this->_version, [
            'title', 'displayIntro'
        ]);

        $this['category']->setSelected($this->_post['#category']);
        $this['tags']->setSelected($this->_post['#tags']);

        $this['headerImage']->setSelected($this->_version['#headerImage']);
        $this['intro']->setBlock($this->_version['intro']);
        $this['body']->setSlotContent($this->_version['body']);
    }
}