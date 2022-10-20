<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace DecodeLabs\R7\Nightfire\Category;

use DecodeLabs\R7\Nightfire\CategoryAbstract;

class Article extends CategoryAbstract
{
    public const DEFAULT_BLOCKS = [
        'SimpleTags', 'RawHtml', 'Markdown',
        'Element', 'Heading',
        'LibraryImage', 'AudioEmbed', 'VideoEmbed'
    ];
}
