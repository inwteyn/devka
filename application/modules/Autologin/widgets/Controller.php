<?php

class Widget_BuynowController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $url = Zend_Registry::get('Zend_Controller_Front')->getRequest();

    $modules = $url->getModuleName();

    $result =  Engine_Api::_()->getDbTable('modules','hecore')->isModuleEnabled($modules);

    if(!$result) {
      $this->setnoRender();
    }

    $this->view->module = $modules;
  }

  public function getModules(){
    $url = Zend_Registry::get('Zend_Controller_Front')->getRequest();
    $modules = $url->getModuleName();
    $result =  Engine_Api::_()->getDbTable('modules','hecore')->isModuleEnabled($modules);
    if($result){
      return $modules;
    }
  }
}