<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\navigation\labels\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpAdd extends arch\form\Action {

    const DEFAULT_EVENT = 'save';

    protected $_label;

    protected function _init() {
        $this->_label = $this->data->newRecord(
            'axis://touchstone/Label'
        );
    }

    protected function _setDefaultValues() {
        $this->values->context = 'shared';
        $this->values->isShared = true;
        $this->values->slug = $this->request->query['location'];
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Label details'));

        // Name
        $fs->addFieldArea($this->_('Name'))->push(
            $this->html->textbox(
                    'name',
                    $this->values->name
                )
                ->isRequired(true)
        );

        // Slug
        $pathList = $this->data->touchstone->label->selectDistinct('slug')
            ->orderBy('slug ASC')
            ->toList('slug');

        $fs->addFieldArea($this->_('Slug'))
            ->setDescription($this->_(
                'Leave empty to generate from name'
            ))
            ->push(
                $this->html->textbox(
                        'slug', 
                        $this->values->slug
                    )
                    ->setPlaceholder($this->_('path/to/label'))
                    ->setDataListId('form-path-list'),

                $this->html->dataList('form-path-list', $pathList)
            );


        // Description
        $fs->addFieldArea($this->_('Description'))->push(
            $this->html->textarea(
                    'description',
                    $this->values->description
                )
                ->setMaxLength(255)
        );


        // Shared
        $fs->addFieldArea()->push(
            $this->html->checkbox(
                    'isShared',
                    $this->values->isShared,
                    $this->_('This label is shared and will appear in all label lists')
                )
        );

        // Context
        $contextList = $this->data->touchstone->label->selectDistinct('context')
            ->orderBy('context ASC')
            ->toList('context');

        $fs->addFieldArea($this->_('Context'))->push(
            $this->html->textbox(
                    'context',
                    $this->values->context
                )
                ->isRequired(true)
                ->setDataListId('form-context-list')
                ->setPlaceholder($this->_('blog or news, etc')),

            $this->html->dataList('form-context-list', $contextList)
        );


        // Buttons
        $fs->push($this->html->defaultButtonGroup());
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Name
            ->addField('name', 'text')
                ->isRequired(true)
                ->end()

            // Slug
            ->addField('slug', 'slug')
                ->isRequired(true)
                ->setDefaultValueField('name')
                ->allowPathFormat(true)
                ->setStorageAdapter($this->data->touchstone->label)
                ->setUniqueFilterId($this->_label['id'])
                ->end()

            // Description
            ->addField('description', 'text')
                ->end()

            // Shared
            ->addField('isShared', 'boolean')
                ->end()

            // Context
            ->addField('context', 'text')
                ->isRequired(true)
                ->end()

            ->validate($this->values)
            ->applyTo($this->_label);


        if($this->isValid()) {
            if($this->_label->isNew()) {
                $this->_label->owner = $this->user->client->getId();
            }

            $this->_label->save();

            $this->arch->notify(
                'label.save',
                $this->_('The label has been successfully saved'),
                'success'
            );

            return $this->complete();
        }
    }
}