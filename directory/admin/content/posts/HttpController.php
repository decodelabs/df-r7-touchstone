<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function fetchPost($view) {
        $view['post'] = $this->data->fetchForAction(
            'axis://touchstone/Post',
            $this->request->query['post']
        );
    }
}