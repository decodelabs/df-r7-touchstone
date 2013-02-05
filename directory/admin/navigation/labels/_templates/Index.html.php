<?php
echo $this->html->menuBar()
    ->addLinks(
        $this->html->link(
                $this->uri->request('~admin/navigation/labels/add?location='.$this['node']['slug'], true),
                $this->_('Add new label')
            )
            ->setIcon('add')
            ->addAccessLock('axis://touchstone/Label#add'),

        '|',

        $this->html->link(
                '~admin/navigation/labels/list',
                $this->_('View as list')
            )
            ->setIcon('list')
            ->setDisposition('informative'),

        '|',

        $this->html->backLink()
    );


echo $this->import->component('SlugTreeBreadcrumbs', '~shared/')
    ->setNode($this['node']);


echo $this->html->collectionList($this['labelList'])
    ->setErrorMessage($this->_('There are no labels to display'))

    // Name
    ->addField('name', function($label) {
        $isNew = $label->isNew();
        $hasChildren = $label['hasChildren'];

        if($isNew) {
            return $this->html->link(
                    $this->uri->query(['slug' => $label['slug']]),
                    $label['name']
                )
                ->setIcon('folder')
                ->setDisposition('transitive');
        }

        $output = $this->html->element('samp', 
            $this->html->link(
                    '~admin/navigation/labels/details?label='.$label['id'],
                    $label['name']
                )
                ->setIcon('label')
                ->setDisposition('informative')
                ->setTitle($label['slug'])
        );

        if($hasChildren) {
            $output = [
                $this->html->link(
                        $this->uri->query(['slug' => $label['slug']]),
                        ' >> '
                    )
                    ->setIcon('folder')
                    ->setDisposition('transitive'),

                $output
            ];
        }

        return $output;
    })
    
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
        if($label->isNew()) {
            return [
                    $this->html->link(
                    $this->uri->request('~admin/navigation/labels/add?location='.$label['slug'], true),
                    $this->_('Convert to real label')
                )
                ->setIcon('add')
                ->addAccessLock('axis://touchstone/Label#add')
            ];
        } else {
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
        }
    })
    ;