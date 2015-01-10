<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\categories\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\form\Action {
    
    protected $_category;

    protected function _init() {
        $this->_category = $this->data->newRecord('axis://touchstone/Category');
    }

    protected function _setupDelegates() {
        $this->loadDelegate('image', '~admin/media/FileSelector')
            ->setAcceptTypes('image/*')
            ->isForOne(true)
            ->isRequired(false);
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Category details'));

        // Name
        $fs->addFieldArea($this->_('Name'))->push(
            $this->html->textbox('name', $this->values->name)
                ->setMaxLength(128)
                ->isRequired(true)
        );

        // Slug
        $fs->addFieldArea($this->_('Slug'))->setDescription($this->_(
            'Leave empty to generate from name'
        ))->push(
            $this->html->textbox('slug', $this->values->slug)
        );

        // Color
        $fs->addFieldArea($this->_('Color'))->push(
            $this->html->colorPicker('color', $this->values->color)
        );

        // Image
        $fs->push($this->getDelegate('image')->renderFieldArea($this->_('Image')));

        // Description
        $fs->addFieldArea($this->_('Description'))->push(
            $this->html->textarea('description', $this->values->description)
        );

        // Butons
        $fs->addDefaultButtonGroup();
    }

    protected function _onSaveEvent() {
        $this->data->newValidator()

            // Name
            ->addRequiredField('name', 'text')
                ->setMaxLength(128)

            // Slug
            ->addRequiredField('slug', 'slug')
                ->setDefaultValueField('name')
                ->setStorageAdapter($this->data->touchstone->category)
                ->setUniqueFilterId($this->_category['id'])

            // Color
            ->addField('color', 'color')

            // Image
            ->addField('image', 'delegate')
                ->fromForm($this)

            // Description
            ->addField('description', 'text')

            ->validate($this->values)
            ->applyTo($this->_category);

        if($this->isValid()) {
            $this->_category->save();

            $this->comms->flash(
                'category.save',
                $this->_('The category has been successfully saved'),
                'success'
            );

            return $this->complete();
        }
    }
}