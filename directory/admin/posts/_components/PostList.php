<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\posts\_components;

use df;
use df\core;
use df\apex;
use df\arch;
    
class PostList extends arch\component\template\CollectionList {

    protected $_fields = [
        'slug' => true,
        'labels' => true,
        'owner' => true,
        'creationDate' => true,
        'lastEditDate' => true,
        'archiveDate' => true,
        'versions' => true,
        'isLive' => true,
        'actions' => true
    ];


// Slug
    public function addSlugField($list) {
        $list->addField('slug', function($post) {
            return $this->import->component('PostLink', '~admin/posts/', $post)
                ->setRedirectFrom($this->_urlRedirect);
        });
    }

// Labels
    public function addLabelsField($list) {
        $list->addField('labels', function($post) {
            return $this->html->bulletList($post['labels'], function($label) {
                return $this->import->component('LabelLink', '~admin/navigation/labels/', $label)
                    ->setDisposition('transitive');
            });
        });
    }

// Owner
    public function addOwnerField($list) {
        $list->addField('owner', function($post) {
            return $this->import->component('UserLink', '~admin/users/clients/', $post['owner'])
                ->setDisposition('transitive');
        });
    }

// Created
    public function addCreationDateField($list) {
        $list->addField('creationDate', $this->_('Created'), function($post) {
            return $this->html->timeSince($post['creationDate']);
        });
    }

// Edited
    public function addLastEditDateField($list) {
        $list->addField('lastEditDate', $this->_('Edited'), function($post) {
            return $this->html->timeSince($post['lastEditDate']);
        });
    }

// Archive
    public function addArchiveDateField($list) {
        $list->addField('archiveDate', $this->_('Archive'), function($post, $context) {
            if($post['archiveDate']) {
                $output = $this->html->date($post['archiveDate'], 'short');

                if($post['archiveDate']->isPast()) {
                    $output->addClass('disposition-negative');
                    $context->getRowTag()->addClass('state-lowPriority');
                }

                return $output;
            }
        });
    }

// Versions
    public function addVersionsField($list) {
        $list->addField('versions', $this->_('V.'));
    }

// Live
    public function addIsLiveField($list) {
        $list->addField('isLive', $this->_('Live'), function($post, $context) {
            if(!$post['isLive']) {
                $context->getRowTag()->addClass('state-lowPriority');
            }
            
            return $this->html->booleanIcon($post['isLive']);
        });
    }

// Actions
    public function addActionsField($list) {
        $list->addField('actions', function($post) {
            return [
                // Edit
                $this->import->component('PostLink', '~admin/posts/', $post, $this->_('Edit'))
                    ->setAction('edit'),

                // Delete
                $this->import->component('PostLink', '~admin/posts/', $post, $this->_('Delete'))
                    ->setAction('delete')
            ];
        });
    }
}