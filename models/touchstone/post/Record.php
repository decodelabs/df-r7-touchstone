<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\models\touchstone\post;

use df;
use df\core;
use df\apex;
use df\opal;
use df\axis;

class Record extends opal\record\Base {

    const BROADCAST_HOOK_EVENTS = true;

    protected function onPreDelete($queue, $job) {
        $job->addDependency($queue->asap('deleteVersions',
            $this->getAdapter()->getModel()->postVersion->delete()
                ->where('post', '=', $this)
        ));
    }
}