<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\labels;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');
        $model = $this->data->touchstone;

        $view['node'] = $model->label->fetchNodeBySlug($this->request->query['slug']);
        $view['labelList'] = $view['node']->fetchNodeList();

        return $view;
    }

    public function listHtmlAction() {
        $view = $this->aura->getView('List-Index.html');
        $model = $this->data->touchstone;

        $view['labelList'] = $model->label->fetch()
            ->paginate()
                ->setOrderableFields('name', 'slug', 'context', 'isShared')
                ->applyWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $view['label'] = $this->data->fetchForAction(
            'axis://touchstone/Label',
            $this->request->query['label']
        );

        return $view;
    }
}