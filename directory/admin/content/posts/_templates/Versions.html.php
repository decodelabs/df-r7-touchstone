<?php

use DecodeLabs\Tagged as Html;

echo $this->html->collectionList($versionList)

    // Title
    ->addField('title')

    // Created
    ->addField('creationDate', $this->_('Created'), function ($version) {
        return Html::$time->since($version['creationDate']);
    })

    // Last edited
    ->addField('lastEditDate', $this->_('Edited'), function ($version) {
        return Html::$time->since($version['lastEditDate']);
    })

    // Owner
    ->addField('owner', function ($version) {
        return $this->apex->component('~admin/users/clients/UserLink', $version['owner']);
    })

    // Actions
    ->addField('actions', function ($version, $context) use ($post) {
        $isActive = false;

        if ($version['id'] == $post['#activeVersion']) {
            $isActive = true;
            $context->getRowTag()->addClass('active');
        }

        return [
            $this->html->link(
                $this->uri('./activate-version?version=' . $version['id'], true),
                $this->_('Activate')
            )
                ->setIcon('accept')
                ->isDisabled($isActive),

            $this->html->link(
                $this->uri('./edit?post=' . $post['id'] . '&rebase=' . $version['id'], true),
                $this->_('Rebase')
            )
                ->setIcon('add'),

            $this->html->link(
                $this->uri('./edit?post=' . $post['id'] . '&version=' . $version['id'], true),
                $this->_('Edit')
            )
                ->setIcon('edit'),

            $this->html->link(
                $this->uri('./delete-version?version=' . $version['id'], true),
                $this->_('Purge')
            )
                ->setIcon('delete')
                ->isDisabled($isActive)
        ];
    })
;
