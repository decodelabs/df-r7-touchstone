<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\_menus;

use df;
use df\core;
use df\apex;
use df\arch;
    
class SectionLinks_Touchstone extends arch\navigation\menu\Base {

    protected function _createEntries(arch\navigation\IEntryList $entryList) {
        $context = $this->getContext();
        
        $post = $context->data->fetchForAction(
            'axis://touchstone/Post',
            $context->request->query['post']
        );

        $versionCount = $post->versions->select()->count();
        $commentCount = $context->data->interact->comment->countFor($post);

        $entryList->addEntries(
            $entryList->newLink('~admin/content/posts/details?post='.$post['id'], 'Details')
                ->setId('details')
                ->setIcon('details')
                ->setWeight(1)
                ->setDisposition('informative'),

            $entryList->newLink('~admin/content/posts/comments?post='.$post['id'], 'Comments')
                ->setId('comments')
                ->setIcon('comment')
                ->setWeight(20)
                ->setNote($context->format->counterNote($commentCount))
                ->setDisposition('informative'),

            $entryList->newLink('~admin/content/posts/versions?post='.$post['id'], 'Versions')
                ->setId('versions')
                ->setIcon('list')
                ->setWeight(30)
                ->setNote($context->format->counterNote($versionCount))
                ->setDisposition('informative')
        );
    }
}