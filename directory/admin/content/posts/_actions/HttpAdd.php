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
        $this->_post = $this->data->newRecord('axis://touchstone/Post');
        $this->_version = $this->data->newRecord('axis://touchstone/PostVersion');
    }

    protected function _setupDelegates() {
        $this->loadDelegate('labels', 'LabelSelector', '~/navigation/labels/')
            ->setDisposition('Posts')
            ->shouldAllowShared(false)
            ->shouldSelectPrimary(true)
            ->isForMany(true)
            ->isRequired(true);

        $this->loadDelegate('headerImage', 'FileSelector', '~/media/')
            ->setAcceptTypes('image/*')
            ->isForOne(true)
            ->isRequired(false);

        $this->loadDelegate('intro', 'ContentBlock', '~/nightfire/')
            ->setCategory('Description')
            ->isRequired(true);

        $this->loadDelegate('body', 'ContentSlot', '~/nightfire/')
            ->isRequired(true)
            ->setSlotDefinition($this->_post->getBodySlotDefinition());
    }

    protected function _setDefaultValues() {
        $this->values->isLive = true;
        $this->values->displayIntro = true;
        $this->values->allowComments = true;
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
        $fs->addFieldArea($this->_('Slug'))->setDescription($this->_(
            'Leave empty to generate from title'
        ))->push(
            $this->html->textbox('slug', $this->values->slug)
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

        // Labels
        $fs->push($this->getDelegate('labels')->renderFieldArea($this->_('Labels')));

        // Image
        $fs->push($this->getDelegate('headerImage')->renderFieldArea($this->_('Header image')));

        // Intro
        $fs->push($this->getDelegate('intro')->renderFieldArea($this->_('Intro')));

        // Display intro
        $fs->addFieldArea()->push(
            $this->html->checkbox('displayIntro', $this->values->displayIntro, $this->_(
                'Display the intro as part of the full body content'
            ))
        );

        // Body
        $form->push($this->getDelegate('body')->renderFieldSet($this->_('Body')));


        // Buttons
        $form->push($this->html->defaultButtonGroup());
    }

    protected function _onSaveEvent() {
        $validator = $this->data->newValidator()

            // Title
            ->addField('title', 'text')
                ->isRequired(true)
                ->end()

            // Slug
            ->addField('slug', 'slug')
                ->setDefaultValueField('title')
                ->isRequired(true)
                ->setStorageAdapter($this->data->touchstone->post)
                ->setUniqueFilterId($this->_post['id'])
                ->end()

            // Archive date
            ->addField('archiveDate', 'Date')
                ->end()

            // Is live
            ->addField('isLive', 'boolean')
                ->end()

            // Is personal
            ->addField('isPersonal', 'boolean')
                ->end()

            // Allow comments
            ->addField('allowComments', 'boolean')
                ->end()

            // Display intro
            ->addField('displayIntro', 'boolean')
                ->end()

            ->validate($this->values)
            ->applyTo($this->_post, [
                'slug', 'archiveDate', 'isLive', 'isPersonal', 'allowComments'
            ])
            ->applyTo($this->_version, [
                'title', 'displayIntro'
            ]);


        $this->_post->labels = $this->getDelegate('labels')->apply();
        $this->_version->headerImage = $this->getDelegate('headerImage')->apply();
        $this->_version->intro = $this->getDelegate('intro')->apply();
        $this->_version->body = $this->getDelegate('body')->apply();


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
            
            $this->comms->flash(
                'post.save',
                $this->_('The post has been successfully saved'),
                'success'
            );

            return $this->complete();
        }
    }
}