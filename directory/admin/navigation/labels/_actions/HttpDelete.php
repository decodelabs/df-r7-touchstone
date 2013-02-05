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
    
class HttpDelete extends arch\form\template\Delete {

    const ITEM_NAME = 'label';

    protected $_label;

    protected function _init() {
        $this->_label = $this->data->fetchForAction(
            'axis://touchstone/Label',
            $this->request->query['label'],
            'delete'
        );
    }

    protected function _getDataId() {
        return $this->_label['id'];
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_label)
            // Name
            ->addField('name')

            // Slug
            ->addField('slug', function($label) {
                return $this->html->element('samp', $label['slug']);
            })

            // Context
            ->addField('context')

            // Shared
            ->addField('isShared', $this->_('Shared'), function($label) {
                return $this->html->booleanIcon($label['isShared']);
            })

            // Description
            ->addField('description', function($label) {
                return $this->html->plainText($label['description']);
            });
    }

    protected function _deleteItem() {
        $this->_label->delete();
    }
}