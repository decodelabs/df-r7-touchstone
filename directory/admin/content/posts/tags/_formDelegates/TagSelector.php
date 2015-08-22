<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\tags\_formDelegates;

use df;
use df\core;
use df\apex;
use df\arch;
use df\fire;
use df\aura;
use df\opal;
    
class TagSelector extends arch\form\template\SelectorDelegate {

    protected function _getBaseQuery($fields=null) {
        return $this->data->touchstone->tag->select($fields)
            ->countRelation('posts')
            ->orderBy('name ASC');
    }

    protected function _renderCollectionList($result) {
        return $this->apex->component('TagList', [
                'actions' => false
            ])
            ->setCollection($result);
    }

    protected function _getResultDisplayName($record) {
        return $record['slug'];
    }

    protected function createInlineDetailsUi(aura\html\widget\FieldArea $fa) {
        $fa->push(
            $this->html->textbox($this->fieldName('slug'), $this->values->slug)
                ->setPlaceholder('Separate with commas: news, my stuff, etc')
                ->setFormEvent($this->eventName('insert')),

            $this->html->eventButton(
                    $this->eventName('insert'),
                    $this->_('Add tags')
                )
                ->setIcon('add')
                ->shouldValidate(false),

            $this->html('<br /><br />')
        );

        return parent::createInlineDetailsUi($fa);
    }

    protected function onInsertEvent() {
        foreach(explode(',', $this->values['slug']) as $slug) {
            $slug = trim($slug);

            if(!strlen($slug)) {
                continue;
            }

            $name = $this->format->name($slug);
            $tag = $this->data->touchstone->tag->ensureTagExists($slug, $name);
            $this->addSelected($tag['id']);
        }

        unset($this->values->slug);
    }

    public function apply() {
        if($this->values->slug->hasValue()) {
            $this->onInsertEvent();
        }

        return parent::apply();
    }
}