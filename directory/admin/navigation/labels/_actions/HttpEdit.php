<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\labels\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpEdit extends HttpAdd {

    protected function _init() {
        $this->_label = $this->data->fetchForAction(
            'axis://touchstone/Label',
            $this->request->query['label'],
            'edit'
        );
    }

    protected function _getDataId() {
        return $this->_label['id'];
    }

    protected function _setDefaultValues() {
        $this->values->name = $this->_label['name'];
        $this->values->slug = $this->_label['slug'];
        $this->values->description = $this->_label['description'];
        $this->values->isShared = $this->_label['isShared'];
        $this->values->context = $this->_label['context'];
    }
}