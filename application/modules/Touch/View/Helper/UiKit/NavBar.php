<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 17.05.12
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */
class Touch_View_Helper_UiKit_NavBar extends Touch_View_Helper_UiKit_Abstract
{

  public function __construct($title, $attribs = array()){
    $this->attribs = array_merge($this->attribs, $attribs);
  }



}
