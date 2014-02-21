<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\posts\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'post';
    
    protected function _getDefaultTitle() {
        return $this->_('Post: %t%', ['%t%' => $this->_record['slug']]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Edit
            $this->import->component('PostLink', '~admin/posts/', $this->_record, $this->_('Edit post'))
                ->setAction('edit'),

            // Delete
            $this->import->component('PostLink', '~admin/posts/', $this->_record, $this->_('Delete post'))
                ->setAction('delete')
                ->setRedirectTo('~admin/posts/')
        );  
    }

    protected function _addSectionLinks($menu) {
        $versionCount = $this->_record->versions->select()->count();
        $commentCount = $this->data->interact->comment->countFor($this->_record);

        $menu->addLinks(
            // Details
            $this->import->component('PostLink', '~admin/posts/', $this->_record, $this->_('Details'), true)
                ->setAction('details')
                ->setIcon('details'),

            // Comments
            $this->import->component('PostLink', '~admin/posts/', $this->_record, $this->_('Comments'), true)
                ->setAction('comments')
                ->setIcon('comment')
                ->setNote($this->format->counterNote($commentCount)),

            // Versions
            $this->import->component('PostLink', '~admin/posts/', $this->_record, $this->_('Versions'), true)
                ->setAction('versions')
                ->setIcon('list')
                ->setNote($this->format->counterNote($versionCount))
        );
    }
}