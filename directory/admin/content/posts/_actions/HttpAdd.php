<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\_actions;

use df;
use df\core;
use df\apex;
use df\arch;
    
class HttpAdd extends arch\form\Action {

    protected $_post;
    protected $_version;
    protected $_keepVersion = false;

    protected function _init() {
        $this->_post = $this->scaffold->newRecord();
        $this->_version = $this->data->newRecord('axis://touchstone/PostVersion');
    }

    protected function _setupDelegates() {
        $this->loadDelegate('category', './categories/CategorySelector')
            ->isForOne(true)
            ->isRequired(false)
            ->setDefaultSearchString('*');

        $this->loadDelegate('tags', './tags/TagSelector')
            ->isForMany(true)
            ->isRequired(false);

        $this->loadDelegate('headerImage', '~admin/media/FileSelector')
            ->setBucket('posts')
            ->setAcceptTypes('image/*')
            ->isForOne(true)
            ->isRequired(false);

        $this->loadDelegate('intro', '~admin/nightfire/ContentBlock')
            ->setCategory('Description')
            ->isRequired(true);

        $this->loadDelegate('body', '~admin/nightfire/ContentSlot')
            ->isRequired(true)
            ->setSlotDefinition($this->_post->getBodySlotDefinition());
    }

    protected function _setDefaultValues() {
        $this->values->isLive = true;
        $this->values->displayIntro = true;
        $this->values->allowComments = true;

        if(isset($this->request->query->category)) {
            $this->getDelegate('category')->setSelected($this->request->query['category']);
        }
    }

    protected function _createUi() {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Post details'));

        // Title
        $fs->addFieldArea($this->_('Title'))->push(
            $this->html->textbox('title', $this->values->title)
                ->isRequired(true)
        );

        // Slug
        $fs->addFieldArea($this->_('Slug'))->push(
            $this->html->textbox('slug', $this->values->slug)
                ->setPlaceholder($this->_('Auto-generate from title'))
        );

        // Archive date
        $fs->addFieldArea($this->_('Archive after'))->push(
            $this->html->datePicker('archiveDate', $this->values->archiveDate)
        );

        // Is live
        $fs->addFieldArea()->push(
            $this->html->checkbox('isLive', $this->values->isLive, $this->_(
                'This post should be live and accessible from the front end'
            ))
        );

        // Is personal
        $fs->addFieldArea()->push(
            $this->html->checkbox('isPersonal', $this->values->isPersonal, $this->_(
                'This post should only be displayed in the owner\'s own personal list'
            ))
        );

        // Allow comments
        $fs->addFieldArea()->push(
            $this->html->checkbox('allowComments', $this->values->allowComments, $this->_(
                'Allow comments on this post'
            ))
        );


        $fs = $form->addFieldSet($this->_('Location'));


        // Category
        $fs->addFieldArea($this->_('Category'))->push(
            $this->getDelegate('category')
        );

        // Tags
        $fs->addFieldArea($this->_('Tags'))->push(
            $this->getDelegate('tags')
        );


        $fs = $form->addFieldSet($this->_('Intro'));

        // Image
        $fs->addFieldArea($this->_('Header image'))->push(
            $this->getDelegate('headerImage')
        );

        // Intro
        $fs->addFieldArea($this->_('Intro'))->push(
            $this->getDelegate('intro')
        );

        // Display intro
        $fs->addFieldArea()->push(
            $this->html->checkbox('displayIntro', $this->values->displayIntro, $this->_(
                'Display the intro as part of the full body content'
            ))
        );

        // Body
        $form->addFieldSet($this->_('Body'))->push(
            $this->getDelegate('body')
        );


        // Buttons
        $form->addDefaultButtonGroup();
    }

    protected function _onSaveEvent() {
        $validator = $this->data->newValidator()

            // Title
            ->addRequiredField('title', 'text')

            // Slug
            ->addRequiredField('slug')
                ->setDefaultValueField('title')
                ->setRecord($this->_post)

            // Category
            ->addField('category', 'delegate')
                ->fromForm($this)

            // Tags
            ->addField('tags', 'delegate')
                ->fromForm($this)

            // Header image
            ->addField('headerImage', 'delegate')
                ->fromForm($this)

            // Intro
            ->addField('intro', 'delegate')
                ->fromForm($this)

            // Body
            ->addField('body', 'delegate')
                ->fromForm($this)

            // Archive date
            ->addField('archiveDate', 'Date')

            // Is live
            ->addField('isLive', 'boolean')

            // Is personal
            ->addField('isPersonal', 'boolean')

            // Allow comments
            ->addField('allowComments', 'boolean')

            // Display intro
            ->addField('displayIntro', 'boolean')

            ->validate($this->values)
            ->applyTo($this->_post, [
                'slug', 'archiveDate', 'category', 'tags', 'isLive', 'isPersonal', 'allowComments'
            ])
            ->applyTo($this->_version, [
                'title', 'headerImage', 'intro', 'displayIntro', 'body'
            ]);



        if($this->isValid()) {
            if($this->_version->hasChanged()) {
                if(!$this->_keepVersion) {
                    $this->_version->makeNew();
                    $this->_version->creationDate = null;
                    $this->_version->lastEditDate = null;
                    $this->_post->activeVersion = $this->_version;
                } else {
                    $this->_version->lastEditDate = 'now';
                }

                if($this->_version->isNew()) {
                    $this->_version->post = $this->_post;
                    $this->_version->owner = $this->user->client->getId();
                }
            }

            if($this->_post->isNew()) {
                $this->_post->owner = $this->user->client->getId();
            } else {
                $this->_post->lastEditDate = 'now';
            }

            $this->_post->save();
            $this->_version->save();
            
            $this->comms->flashSaveSuccess('post');
            return $this->complete();
        }
    }
}