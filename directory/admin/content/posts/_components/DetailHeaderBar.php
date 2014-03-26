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
    
class DetailHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'post';
    
    protected function _getDefaultTitle() {
        return $this->_('Post: %t%', ['%t%' => $this->_record['slug']]);
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            // Edit
            $this->import->component('PostLink', '~admin/content/posts/', $this->_record, $this->_('Edit post'))
                ->setAction('edit'),

            // Delete
            $this->import->component('PostLink', '~admin/content/posts/', $this->_record, $this->_('Delete post'))
                ->setAction('delete')
                ->setRedirectTo('~admin/content/posts/')
        );  
    }

    protected function _addSubOperativeLinks($menu) {
        if($this->slot->has('subOperativeLinks')) {
            $menu->addLinks($this->slot->getValue('subOperativeLinks'));
        }
    }

    protected function _addSectionLinks($menu) {
        $menu->addLinks('directory://~admin/content/posts/SectionLinks');
    }
}