<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class PostLink extends arch\component\template\RecordLink {

    protected $_icon = 'post';

// Url
    protected function _getRecordUrl($id) {
        return '~admin/content/posts/details?post='.$id;
    }

    protected function _getRecordName() {
        if(isset($this->_record['title'])) {
            return $this->_record['title'];
        }
        
        return $this->_record['slug'];
    }
}