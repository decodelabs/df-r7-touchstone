<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpDeleteVersion extends arch\form\template\Delete {

    protected $_version;

    protected function _init() {
        $this->_version = $this->data->fetchForAction(
            'axis://touchstone/PostVersion',
            $this->request->query['version'],
            'edit'
        );
    }    

    protected function _getDataId() {
        return $this->_version['id'];
    }

    protected function _onSessionReady() {
        if($this->_version['id'] == $this->_version['post']['#activeVersion']) {
            $this->values->addError('active', $this->_(
                'This version is currently active and cannot be deleted'
            ));
        }
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_version)
            // Title
            ->addField('title')

            // Created
            ->addField('creationDate', $this->_('Created'), function($version) {
                return $this->html->date($version['creationDate']);
            })

            // Last edited
            ->addField('lastEditDate', $this->_('Edited'), function($version) {
                return $this->html->timeSince($version['lastEditDate']);
            })

            // Owner
            ->addField('owner', function($version) {
                return $this->apex->component('~admin/users/clients/UserLink', $version['owner']);
            });
    }



    protected function _deleteItem() {
        $this->_version->delete();
    }
}