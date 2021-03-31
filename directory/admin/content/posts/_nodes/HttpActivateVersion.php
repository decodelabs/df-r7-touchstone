<?php
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\apex\directory\admin\content\posts\_nodes;

use df;
use df\core;
use df\apex;
use df\arch;

use DecodeLabs\Tagged as Html;

class HttpActivateVersion extends arch\node\ConfirmForm
{
    protected $_version;

    protected function init()
    {
        $this->_version = $this->data->fetchForAction(
            'axis://touchstone/PostVersion',
            $this->request['version']
        );
    }

    protected function getInstanceId()
    {
        return $this->_version['id'];
    }

    protected function getMainMessage()
    {
        return $this->_(
            'Are you sure you want to activate this version?'
        );
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
        $this->_version['post']->activeVersion = $this->_version;
        $this->_version['post']->save();
    }
}
