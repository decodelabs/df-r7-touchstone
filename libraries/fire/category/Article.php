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

    const DEFAULT_BLOCKS = [
        'SimpleTags', 'RawHtml', 'Markdown',
        'Element', 'Heading',
        'LibraryImage', 'AudioEmbed', 'VideoEmbed'
    ];
}
