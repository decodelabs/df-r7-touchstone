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

    protected function _onPreDelete($taskSet, $task) {
        $id = $this['id'];

        $delTask = $taskSet->addRawQuery('deleteVersions',
            $this->getAdapter()->getModel()->postVersion->delete()
                ->where('post', '=', $this)
        );

        $task->addDependency($delTask);
    }

    public function getBodySlotDefinition() {
        return $this->getAdapter()
            ->getModel()
            ->getUnit('postVersion')
            ->getUnitSchema()
            ->getField('body')
            ->getSlotDefinition();
    }
}