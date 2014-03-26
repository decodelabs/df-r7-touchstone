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

    public function indexHtmlAction() {
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

    public function detailsHtmlAction() {
        $view = $this->aura->getView('Details.html');
        $this->fetchPost($view);

        return $view;
    }

    public function commentsHtmlAction() {
        $view = $this->aura->getView('Comments.html');
        $this->fetchPost($view);

        return $view;
    }

    public function versionsHtmlAction() {
        $view = $this->aura->getView('Versions.html');
        $this->fetchPost($view);

        $view['versionList'] = $view['post']->versions->fetch()
            ->orderBy('creationDate DESC');

        return $view;
    }

    public function fetchPost($view) {
        $view['post'] = $this->data->fetchForAction(
            'axis://touchstone/Post',
            $this->request->query['post']
        );
    }
}