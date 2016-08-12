<?php

class Autologin_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
       public function onRenderLayoutAdmin($event)
      {
          $view = $event->getPayload();
          $content = $view->content()->renderWidget('autologin.buynowframe');
          $view->layout()->content .= $content;
      }
}