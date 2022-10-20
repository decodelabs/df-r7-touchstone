<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\content\posts\tags\_nodes;

class HttpEdit extends HttpAdd
{
    protected function init()
    {
        $this->_tag = $this->scaffold->getRecord();
    }

    protected function getInstanceId()
    {
        return $this->_tag['id'];
    }

    protected function setDefaultValues(): void
    {
        $this->values->importFrom($this->_tag, [
            'name', 'slug'
        ]);
    }
}
