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
    
class HttpDelete extends arch\form\template\Delete {

    const ITEM_NAME = 'post';

    protected $_post;

    protected function _init() {
        $this->_post = $this->data->fetchForAction(
            'axis://touchstone/Post',
            $this->request->query['post'],
            'delete'
        );
    }

    protected function _getDataId() {
        return $this->_post['id'];
    }

    protected function _renderItemDetails($container) {
        $container->addAttributeList($this->_post)

            // Title
            ->addField('title', function($post) {
                return $post['activeVersion']['title'];
            })

            // Slug
            ->addField('slug')

            // Owner
            ->addField('owner', function($post) {
                return $this->import->component('UserLink', '~admin/users/clients/', $post['owner']);
            })

            // Versions
            ->addField('versions', function($post) {
                return $post->versions->select()->count();
            });
    }

    protected function _deleteItem() {
        foreach($this->_post->versions->fetch() as $version) {
            $version->delete();
        }

        $this->_post->delete();
    }
}