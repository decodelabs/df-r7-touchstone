<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\categories\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpEdit extends HttpAdd {

    protected function init() {
        $this->_category = $this->scaffold->getRecord();
    }

    protected function getInstanceId() {
        return $this->_category['id'];
    }

    protected function setDefaultValues() {
        $this->values->importFrom($this->_category, [
            'name', 'slug', 'color', 'description'
        ]);

        $this['image']->setSelected($this->_category['#image']);
    }
}