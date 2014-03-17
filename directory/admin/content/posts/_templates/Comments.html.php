<?php

echo $this->import->component('DetailHeaderBar', '~admin/content/posts/', $this['post']);

if(!$this['post']['allowComments']) {
    echo $this->html->flashMessage($this->_(
        'Comments are currently disabled for this post'
    ), 'warning');
}

echo $this->import->component('Comment', '~/comments/', $this['post']->getEntityLocator())
    ->shouldDisplayAsTree(true)
    ->shouldShowForm($this['post']['allowComments'])
    ->shouldShowInactive(true);