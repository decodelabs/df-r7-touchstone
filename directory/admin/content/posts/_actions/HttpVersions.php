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

class HttpVersions extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Versions.html');
        $this->controller->fetchPost($view);

        $view['versionList'] = $view['post']->versions->fetch()
            ->orderBy('creationDate DESC');

        return $view;
    }
}