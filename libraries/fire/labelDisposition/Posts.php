<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\fire\labelDisposition;

use df;
use df\core;
use df\fire;
use df\apex;

class Posts extends Base {
    
    const DEFAULT_SLUG = 'posts';

    public function getItemCounts(core\IContext $context, apex\models\nightfire\label\Record $label) {
        return [
            'posts' => $context->data->touchstone->post->select()
                ->where('labels', 'in', $label)
                ->count()
        ];
    }
}