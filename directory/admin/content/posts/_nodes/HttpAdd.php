<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\content\posts\_nodes;

use DecodeLabs\Disciple;

use df\apex\directory\shared\media\_formDelegates\FileSelector;
use df\apex\directory\shared\nightfire\_formDelegates\ContentBlock;
use df\apex\directory\shared\nightfire\_formDelegates\ContentSlot;
use df\arch;

use df\arch\node\form\SelectorDelegate;

class HttpAdd extends arch\node\Form
{
    protected $_post;
    protected $_version;
    protected $_keepVersion = false;

    protected function init(): void
    {
        $this->_post = $this->scaffold->newRecord();
        $this->_version = $this->data->newRecord('axis://touchstone/PostVersion');
    }

    protected function loadDelegates(): void
    {
        // Category
        $this->loadDelegate('category', './categories/CategorySelector')
            ->as(SelectorDelegate::class)
            ->isForOne(true)
            ->isRequired(false)
            ->setDefaultSearchString('*');


        // Tags
        $this->loadDelegate('tags', './tags/TagSelector')
            ->as(SelectorDelegate::class)
            ->isForMany(true)
            ->isRequired(false);


        // Header image
        $this->loadDelegate('headerImage', '~admin/media/FileSelector')
            ->as(FileSelector::class)
            ->setBucket('posts')
            ->setAcceptTypes('image/*')
            ->isForOne(true)
            ->isRequired(false);


        // Intro
        $this->loadDelegate('intro', '~admin/nightfire/ContentBlock')
            ->as(ContentBlock::class)
            ->setCategory('Description')
            ->isRequired(true);


        // Body
        $this->loadDelegate('body', '~admin/nightfire/ContentSlot')
            ->as(ContentSlot::class)
            ->setCategory('Article');
    }

    protected function setDefaultValues(): void
    {
        $this->values->isLive = true;
        $this->values->displayIntro = true;
        $this->values->allowComments = true;
    }

    protected function createUi(): void
    {
        $form = $this->content->addForm();
        $fs = $form->addFieldSet($this->_('Post details'));

        // Title
        $fs->addField($this->_('Title'))->push(
            $this->html->textbox('title', $this->values->title)
                ->isRequired(true)
        );

        // Slug
        $fs->addField($this->_('Slug'))->push(
            $this->html->textbox('slug', $this->values->slug)
                ->setPlaceholder($this->_('Auto-generate from title'))
        );

        // Post date
        $fs->addField($this->_('Post date'))->setDescription($this->_(
            'Leave empty to default to today'
        ))->push(
            $this->html->datePicker('postDate', $this->values->postDate)
        );

        // Archive date
        $fs->addField($this->_('Archive after'))->push(
            $this->html->datePicker('archiveDate', $this->values->archiveDate)
        );

        // Is live
        $fs->addField()->push(
            $this->html->checkbox('isLive', $this->values->isLive, $this->_(
                'This post should be live and accessible from the front end'
            ))
        );

        // Allow comments
        $fs->addField()->push(
            $this->html->checkbox('allowComments', $this->values->allowComments, $this->_(
                'Allow comments on this post'
            ))
        );


        $fs = $form->addFieldSet($this->_('Location'));


        // Category
        $fs->addField($this->_('Category'))->push($this['category']);

        // Tags
        $fs->addField($this->_('Tags'))->push($this['tags']);


        $fs = $form->addFieldSet($this->_('Content'));

        // Image
        $fs->addField($this->_('Header image'))->push($this['headerImage']);

        // Intro
        $fs->addField($this->_('Intro'))->push($this['intro']);

        // Display intro
        $fs->addField()->push(
            $this->html->checkbox('displayIntro', $this->values->displayIntro, $this->_(
                'Display the intro as part of the full body content'
            ))
        );

        // Body
        $fs->addField($this->_('Body'))->push(
            $this['body']
        );


        // Buttons
        $form->addDefaultButtonGroup();
    }

    protected function onSaveEvent()
    {
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

            // Post date
            ->addRequiredField('postDate', 'date')
                ->shouldDefaultToNow(true)

            // Archive date
            ->addField('archiveDate', 'Date')

            // Is live
            ->addRequiredField('isLive', 'boolean')

            // Allow comments
            ->addRequiredField('allowComments', 'boolean')

            // Display intro
            ->addRequiredField('displayIntro', 'boolean')

            ->validate($this->values)
            ->applyTo($this->_post, [
                'slug', 'postDate', 'archiveDate', 'category', 'tags', 'isLive', 'allowComments'
            ])
            ->applyTo($this->_version, [
                'title', 'headerImage', 'intro', 'displayIntro', 'body'
            ]);

        return $this->complete(function () {
            if ($this->_version->hasChanged()) {
                if (!$this->_keepVersion) {
                    $this->_version->makeNew();
                    $this->_version->creationDate = null;
                    $this->_version->lastEditDate = null;
                    $this->_post->activeVersion = $this->_version;
                } else {
                    $this->_version->lastEditDate = 'now';
                }

                if ($this->_version->isNew()) {
                    $this->_version->post = $this->_post;
                    $this->_version->owner = Disciple::getId();
                }

                $this->_post->lastEditDate = 'now';
            }

            if ($this->_post->isNew()) {
                $this->_post->owner = Disciple::getId();
            } elseif ($this->_post->hasChanged()) {
                $this->_post->lastEditDate = 'now';
            }

            $this->_post->save();
            $this->_version->save();

            $this->comms->flashSaveSuccess('post');
        });
    }
}
