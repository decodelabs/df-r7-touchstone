<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\fire\category;

use df;
use df\core;
use df\fire;
    
class Article extends Base {

    protected static $_requiredOutputTypes = ['Html'];
    protected static $_defaultBlocks = ['SimpleTags', 'RawHtml', 'LibraryImage', 'Audioboo'];
}