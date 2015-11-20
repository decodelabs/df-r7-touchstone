<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\tags\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

class HttpAdd extends arch\node\Form {

    protected $_tag;

    protected function init() {
        $this->_tag = $this->scaffold->newRecord();
    }

    protected function createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Tag details'));

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

        // Buttons
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
                ->setRecord($this->_tag)

            ->validate($this->values)
            ->applyTo($this->_tag);

        return $this->complete(function() {
            $this->_tag->save();
            $this->comms->flashSaveSuccess('tag');
        });
    }
}