<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\posts;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpController extends arch\Controller {

    public function indexHtmlAction() {
        $view = $this->aura->getView('Index.html');
        $model = $this->data->touchstone;

        $view['postList'] = $model->post->fetch()

            ->paginate()
                ->setOrderableFields('slug', 'primaryLabel', 'creationDate', 'lastEditDate', 'archiveDate')
                ->applyWith($this->request->query);

        return $view;
    }
}