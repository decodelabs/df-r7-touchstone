<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\fire\Category;

class Article extends Base
{
    public const DEFAULT_BLOCKS = [
        'SimpleTags', 'RawHtml', 'Markdown',
        'Element', 'Heading',
        'LibraryImage', 'AudioEmbed', 'VideoEmbed'
    ];
}
