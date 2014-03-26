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

class HttpIndex extends arch\Action {
    
    public function executeAsHtml() {
        $view = $this->aura->getView('Index.html');
        $model = $this->data->touchstone;

        $view['postList'] = $model->post->select()
            ->countRelation('versions')
            ->importRelationBlock('labels', 'link')
            ->importRelationBlock('owner', 'link')
            ->importBlock('title')
            ->paginate()
                ->addOrderableFields('title')
                ->applyWith($this->request->query);

        return $view;
    }
}