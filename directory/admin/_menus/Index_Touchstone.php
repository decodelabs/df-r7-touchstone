<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_Touchstone extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
             $entryList->newLink('~admin/posts/', 'Posts')
                ->setId('posts')
                ->setDescription('Write and publish news and blog posts')
                ->setIcon('post')
                ->setWeight(6)
        );
    }
}