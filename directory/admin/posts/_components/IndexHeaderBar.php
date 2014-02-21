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
    
class IndexHeaderBar extends arch\component\template\HeaderBar {

    protected $_icon = 'post';

    protected function _getDefaultTitle() {
        return $this->_('Posts');
    }

    protected function _addOperativeLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    $this->uri->request('~admin/posts/add', true),
                    $this->_('Add new post')
                )
                ->setIcon('add')
                ->addAccessLock('axis://touchstone/Post#add')
        );
    }

    protected function _addTransitiveLinks($menu) {
        $menu->addLinks(
            $this->html->link(
                    '~admin/navigation/labels/',
                    $this->_('Labels')
                )
                ->setIcon('label')
                ->setDisposition('transitive')
        );  
    }
}