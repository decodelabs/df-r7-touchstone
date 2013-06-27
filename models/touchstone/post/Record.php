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
    
    public function getBodySlotDefinition() {
        return $this->getRecordAdapter()
            ->getModel()
            ->getUnit('postVersion')
            ->getUnitSchema()
            ->getField('body')
            ->getSlotDefinition();
    }
}