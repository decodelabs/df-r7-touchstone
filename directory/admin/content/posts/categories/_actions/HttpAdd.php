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
        $this->_category = $this->scaffold->newRecord();
    }

    protected function _setupDelegates() {
        $this->loadDelegate('image', '~admin/media/FileSelector')
            ->setAcceptTypes('image/*')
            ->setBucket('posts')
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
        $fs->addFieldArea($this->_('Slug'))->push(
            $this->html->textbox('slug', $this->values->slug)
                ->setPlaceholder($this->_('Auto-generate from name'))
        );

        // Color
        $fs->addFieldArea($this->_('Color'))->push(
            $this->html->colorPicker('color', $this->values->color)
        );

        // Image
        $fs->addFieldArea($this->_('Image'))->push(
            $this->getDelegate('image')
        );

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
            ->addRequiredField('slug')
                ->setDefaultValueField('name')
                ->setRecord($this->_category)

            // Color
            ->addField('color')

            // Image
            ->addField('image', 'delegate')
                ->fromForm($this)

            // Description
            ->addField('description', 'text')

            ->validate($this->values)
            ->applyTo($this->_category);

        if($this->isValid()) {
            $this->_category->save();
            $this->comms->flashSaveSuccess('category');

            return $this->complete();
        }
    }
}