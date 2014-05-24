<?php

echo $this->html->collectionList($this['versionList'])
    
    // Title
    ->addField('title')

    // Created
    ->addField('creationDate', $this->_('Created'), function($version) {
        return $this->html->date($version['creationDate']);
    })

    // Last edited
    ->addField('lastEditDate', $this->_('Edited'), function($version) {
        return $this->html->timeSince($version['lastEditDate']);
    })

    // Owner
    ->addField('owner', function($version) {
        return $this->import->component('UserLink', '~admin/users/clients/', $version['owner']);
    })

    // Actions
    ->addField('actions', function($version, $context) {
        $isActive = false;

        if($version['id'] == $this['post']->getRawId('activeVersion')) {
            $isActive = true;
            $context->getRowTag()->addClass('state-active');
        }

        return [
            $this->html->link(
                    $this->uri->request('~admin/content/posts/activate-version?version='.$version['id'], true),
                    $this->_('Activate')
                )
                ->setIcon('accept')
                ->isDisabled($isActive),

            $this->html->link(
                    $this->uri->request('~admin/content/posts/edit?post='.$this['post']['id'].'&rebase='.$version['id'], true),
                    $this->_('Rebase')
                )
                ->setIcon('add'),

            $this->html->link(
                    $this->uri->request('~admin/content/posts/edit?post='.$this['post']['id'].'&version='.$version['id'], true),
                    $this->_('Edit')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri->request('~admin/content/posts/delete-version?version='.$version['id'], true),
                    $this->_('Purge')
                )
                ->setIcon('delete')
                ->isDisabled($isActive)
        ];
    })
    ;