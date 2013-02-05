<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class Index_TouchstoneCore extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $entryList->addEntries(
            $entryList->newLink('~admin/navigation/labels/', 'Labels')
                ->setId('labels')
                ->setDescription('Browse and update the taxonomy of your published content')
                ->setIcon('label')
                ->setWeight(5)
        );
    }
}