<?php

echo $this->import->component('DetailHeaderBar', '~admin/posts/', $this['post']);

echo $this->html->attributeList($this['post'])

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

    // Is personal
    ->addField('isPersonal', function($post) {
        return $this->html->booleanIcon($post['isPersonal']);
    })

    // Is live
    ->addField('isLive', function($post) {
        return $this->html->booleanIcon($post['isLive']);
    })

    // Versions
    ->addField('versions', function($post) {
        return $post->versions->select()->count();
    })

    // Created
    ->addField('creationDate', $this->_('Created'), function($post) {
        return $this->html->timeSince($post['creationDate']);
    })

    // Edited
    ->addField('lastEditDate', $this->_('Last edited'), function($post) {
        return $this->html->timeSince($post['lastEditDate']);
    })

    // Archive
    ->addField('archiveDate', $this->_('Archive after'), function($post) {
        if($post['archiveDate']) {
            $output = $this->html->date($post['archiveDate'], 'short');

            if($post['archiveDate']->isPast()) {
                $output->addClass('disposition-negative');
            }

            return $output;
        }
    })

    // Labels
    ->addField('labels', function($post) {
        return $this->html->bulletList($post->labels->fetch(), function($label) {
            return $this->import->component('LabelLink', '~admin/navigation/labels/', $label);
        });
    })

    // Header Image
    ->addField('headerImage', function($post) {
        return $this->import->component('FileLink', '~admin/media/', $post['activeVersion']['headerImage'])
            ->isNullable(true);
    })

    // Intro
    ->addField('intro', function($post) {
        return $this->nightfire->renderBlock($post['activeVersion']['intro'], 'Description');
    })

    // Body
    ->addField('body', function($post) {
        return $this->nightfire->renderSlot($post['activeVersion']['body'], 'Article');
    })
    ;