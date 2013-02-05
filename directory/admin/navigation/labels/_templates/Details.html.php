<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/navigation/labels/edit?label='.$this['label']['id'], true),
                $this->_('Edit label')
            )
            ->setIcon('edit')
            ->addAccessLock($this['label']->getActionLock('edit')),

        $this->html->link(
                $this->uri->request(
                    '~admin/navigation/labels/delete?label='.$this['label']['id'], true,
                    '~admin/navigation/labels/'
                ),
                $this->_('Delete label')
            )
            ->setIcon('delete')
            ->addAccessLock($this['label']->getActionLock('delete')),

        '|', 

        $this->html->backLink()
    );


echo $this->html->attributeList($this['label'])
    
    // Name
    ->addField('name')

    // Slug
    ->addField('slug', function($label) {
        return $this->html->element('samp', $label['slug']);
    })

    // Context
    ->addField('context')

    // Shared
    ->addField('isShared', $this->_('Shared'), function($label) {
        return $this->html->booleanIcon($label['isShared']);
    })

    // Description
    ->addField('description', function($label) {
        return $this->html->plainText($label['description']);
    });