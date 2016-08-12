<?php

class Autologin_Widget_BuynowframeController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $url = Zend_Registry::get('Zend_Controller_Front')->getRequest();

    $modules = $url->getModuleName();

    $result =  Engine_Api::_()->getDbTable('modules','hecore')->isModuleEnabled($modules) || $modules == 'hecore' ||
      $modules=='hequestion' || $modules=='hebadge' || $modules=='headvancedalbum' || $modules=='pagedocument' || $modules=='pagevideo' || $modules=='pagediscussion' ||
      $modules=='pagealbum' || $modules=='pagemusic' || $modules=='pageblog' || $modules=='pageevent' || $modules=='pagefaq';

    if(!$result) {
      $this->setnoRender();
    }

    $alias = Engine_Api::_()->getDbTable('alias','autologin')->getAlias($modules);

    $this->view->alias = $alias;
    $this->view->module = $modules;
  }

}