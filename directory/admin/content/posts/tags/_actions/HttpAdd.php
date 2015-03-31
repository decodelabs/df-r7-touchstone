<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\tags\_actions;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\form\Action {
    
    protected $_tag;

    protected function _init() {
        $this->_tag = $this->data->newRecord('axis://touchstone/Tag');
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Tag details'));

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

        // Buttons
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
                ->setStorageAdapter($this->data->touchstone->tag)
                ->setUniqueFilterId($this->_tag['id'])

            ->validate($this->values)
            ->applyTo($this->_tag);

        if($this->isValid()) {
            $this->_tag->save();
            $this->comms->flashSaveSuccess('tag');

            return $this->complete();
        }
    }
}