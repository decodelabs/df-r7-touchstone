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

        $view['postList'] = $model->post->select()
            ->countRelation('versions')
            ->populate('owner')
            ->populate('labels')
            ->paginateWith($this->request->query);

        return $view;
    }

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $this->_fetchPost($view);

        return $view;
    }

    public function versionsHtmlAction() {
        $view = $this->aura->getView('Versions.html');
        $this->_fetchPost($view);

        $view['versionList'] = $view['post']->versions->fetch()
            ->orderBy('creationDate DESC');

        return $view;
    }

    protected function _fetchPost($view) {
        $view['post'] = $this->data->fetchForAction(
            'axis://touchstone/Post',
            $this->request->query['post']
        );
    }
}