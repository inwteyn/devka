<?php
/**
 * Created by JetBrains PhpStorm.
 * Author: Ulan
 * Date: 27.12.11
 * Time: 12:14
 * To change this template use File | Settings | File Templates.
 */
 
class Touch_Form_Requirements extends Touch_Form_Standard{
  public function init(){
    $this->setTitle('TOUCH_This browser does not meet some requirements');
    $this->setDescription('This is version of the site allows you to access using modern mobile devices(iPhone, Android, BlackBerry, iPad and many more). It requires mobile devices to support at least Javascript and Ajax');
    $this->setAttrib('class', 'touch_not_support');
  }
}
