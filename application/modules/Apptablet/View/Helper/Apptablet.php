<?php

class Apptablet_View_Helper_Apptablet extends Zend_View_Helper_Abstract
{

  public function apptablet()
  {
    return $this;
  }


  public function scripts()
  {
    $staticBaseUrl = $this->view->layout()->staticBaseUrl;

    return  <<<CONTENT
    <script type="text/javascript" src="{$staticBaseUrl}application/modules/Apptablet/externals/scripts/jquery.lazyload.min.js"></script>
    <script type="text/javascript" src="{$staticBaseUrl}application/modules/Apptablet/externals/scripts/activity.js"></script>
    <script type="text/javascript" src="{$staticBaseUrl}application/modules/Apptablet/externals/scripts/components.js"></script>
    <script type="text/javascript" src="{$staticBaseUrl}application/modules/Apptablet/externals/scripts/core.js"></script>
    <script type="text/javascript" src="{$staticBaseUrl}application/modules/Apptablet/externals/scripts/initializers.js"></script>
CONTENT;

  }

  public function css()
  {
    $activeTheme = Engine_Api::_()->getDbTable('themes', 'apptouch')->getActiveThemeName();
    $staticBaseUrl = $this->view->layout()->staticBaseUrl;

    $activeTheme = $staticBaseUrl . 'application/modules/Apptablet/externals/themes/' . $activeTheme . '/theme.css';

    return <<<CONTENT
  <link href="{$staticBaseUrl}application/modules/Apptablet/externals/styles/activity.css" media="screen" rel="stylesheet" type="text/css"/>
  <link href="{$staticBaseUrl}application/modules/Apptablet/externals/styles/components.css" media="screen" rel="stylesheet" type="text/css"/>
  <link href="{$staticBaseUrl}application/modules/Apptablet/externals/styles/core.css" media="screen" rel="stylesheet" type="text/css"/>
  <link href="{$staticBaseUrl}application/modules/Apptablet/externals/styles/custom.css" media="screen" rel="stylesheet" type="text/css"/>
  <link href="{$activeTheme}" media="screen" rel="stylesheet" type="text/css"/>

CONTENT;

  }

  public function ui()
  {
    return $this->view->render('application/modules/Apptablet/views/scripts/_templates/ui.tpl');
  }



}
