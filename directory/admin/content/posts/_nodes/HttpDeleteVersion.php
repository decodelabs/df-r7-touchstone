<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */

namespace df\apex\directory\admin\content\posts\_nodes;

use DecodeLabs\Tagged as Html;

use df\arch;

class HttpDeleteVersion extends arch\node\DeleteForm
{
    protected $_version;

    protected function init(): void
    {
        $this->_version = $this->data->fetchForAction(
            'axis://touchstone/PostVersion',
            $this->request['version']
        );
    }

    protected function getInstanceId(): ?string
    {
        return $this->_version['id'];
    }

    protected function initWithSession(): void
    {
        if ($this->_version['id'] == $this->_version['post']['#activeVersion']) {
            $this->values->addError('active', $this->_(
                'This version is currently active and cannot be deleted'
            ));
        }
    }

    protected function createItemUi($container)
    {
        $container->addAttributeList($this->_version)
            // Title
            ->addField('title')

            // Created
            ->addField('creationDate', $this->_('Created'), function ($version) {
                return Html::$time->date($version['creationDate']);
            })

            // Last edited
            ->addField('lastEditDate', $this->_('Edited'), function ($version) {
                return Html::$time->since($version['lastEditDate']);
            })

            // Owner
            ->addField('owner', function ($version) {
                return $this->apex->component('~admin/users/clients/UserLink', $version['owner']);
            });
    }



    protected function apply()
    {
        $this->_version->delete();
    }
}
