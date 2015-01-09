<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\categories\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends HttpAdd {
    
    protected function _init() {
        $this->_category = $this->scaffold->getRecord();
    }

    protected function _getDataId() {
        return $this->_category['id'];
    }

    protected function _setDefaultValues() {
        $this->values->importForm($this->_category, [
            'name', 'slug', 'color', 'description'
        ]);

        $this->getDelegate('image')->setSelected($this->_category->getRawId('image'));
    }
}