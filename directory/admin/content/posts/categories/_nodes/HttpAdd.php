<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\categories\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\node\Form {

    protected $_category;

    protected function init() {
        $this->_category = $this->scaffold->newRecord();
    }

    protected function loadDelegates() {
        $this->loadDelegate('image', '~admin/media/FileSelector')
            ->setAcceptTypes('image/*')
            ->setBucket('posts')
            ->isForOne(true)
            ->isRequired(false);
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Category details'));

        // Name
        $fs->addField($this->_('Name'))->push(
            $this->html->textbox('name', $this->values->name)
                ->setMaxLength(128)
                ->isRequired(true)
        );

        // Slug
        $fs->addField($this->_('Slug'))->push(
            $this->html->textbox('slug', $this->values->slug)
                ->setPlaceholder($this->_('Auto-generate from name'))
        );

        // Color
        $fs->addField($this->_('Color'))->push(
            $this->html->colorPicker('color', $this->values->color)
        );

        // Image
        $fs->addField($this->_('Image'))->push($this['image']);

        // Description
        $fs->addField($this->_('Description'))->push(
            $this->html->textarea('description', $this->values->description)
        );

        // Butons
        $fs->addDefaultButtonGroup();
    }

    protected function onSaveEvent() {
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

        return $this->complete(function() {
            $this->_category->save();
            $this->comms->flashSaveSuccess('category');
        });
    }
}