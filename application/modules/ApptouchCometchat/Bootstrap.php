<?php

class ApptouchCometchat_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    public function __construct($application)
    {
        parent::__construct($application);
        if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('apptouch')){
            $this->initViewHelperPath();
            Zend_Controller_Front::getInstance()->registerPlugin(new ApptouchCometchat_Plugin_Core());
        }
    }
}