<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpComments extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Comments.html');
        $this->controller->fetchPost($view);

        return $view;
    }
}