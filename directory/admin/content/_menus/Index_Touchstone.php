<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\content\_menus;

use df\arch;

class Index_Touchstone extends arch\navigation\menu\Base
{
    protected function createEntries($entryList)
    {
        $entryList->addEntries(
            $entryList->newLink('./posts/', 'Posts')
                ->setId('posts')
                ->setDescription('Write and publish news and blog posts')
                ->setIcon('post')
                ->setWeight(6)
        );
    }
}
