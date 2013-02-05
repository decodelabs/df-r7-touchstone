<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/navigation/labels/add', true),
                $this->_('Add new label')
            )
            ->setIcon('add')
            ->addAccessLock('axis://touchstone/Label#add'),

        '|',

        $this->html->link(
                '~admin/navigation/labels/',
                $this->_('View as tree')
            )
            ->setIcon('node')
            ->setDisposition('informative'),

        '|',

        $this->html->backLink()
    );


echo $this->html->collectionList($this['labelList'])
    ->setErrorMessage($this->_('There are no labels to display'))

    // Slug
    ->addField('slug', function($label) {
        return $this->html->element('samp', 
            $this->html->link(
                '~admin/navigation/labels/details?label='.$label['id'],
                $label['slug']
            )
            ->setIcon('label')
            ->setDisposition('informative')
        );
    })
    
    // Name
    ->addField('name')

    // Context
    ->addField('context')

    // Shared
    ->addField('isShared', $this->_('Shared'), function($label) {
        return $this->html->booleanIcon($label['isShared']);
    })

    // Description
    ->addField('description', function($label) {
        return $this->format->shorten($label['description'], 40);
    })

    // Actions
    ->addField('actions', function($label) {
        return [
            $this->html->link(
                    $this->uri->request('~admin/navigation/labels/edit?label='.$label['id'], true),
                    $this->_('Edit')
                )
                ->setIcon('edit'),

            $this->html->link(
                    $this->uri->request('~admin/navigation/labels/delete?label='.$label['id'], true),
                    $this->_('Delete')
                )
                ->setIcon('delete')
        ];
    })
    ;