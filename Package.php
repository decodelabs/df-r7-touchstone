<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\packages\touchstone;

use df\core;

class Package extends core\Package {
    
    const PRIORITY = 20;

    public static $dependencies = [
        'webCore'
    ];
}