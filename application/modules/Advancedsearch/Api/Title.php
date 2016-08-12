<?php
/***/
class Advancedsearch_Api_Title extends Core_Api_Abstract
{

  protected $_table;

  public function  __construct()
  {
    $this->_table = Engine_Api::_()->getDbtable('title', 'advancedsearch');
  }

  public function __get($key)
  {
    return $this->_table->getSetting($key);
  }

  public function __set($key, $value)
  {
    return $this->_table->setSetting($key, $value);
  }

  public function __isset($key)
  {
    return $this->_table->hasSetting($key);
  }

  public function __unset($key)
  {
    return $this->_table->removeSetting($key);
  }

  public function __call($method, array $arguments = array())
  {
    var_dump($method);
    $r = new ReflectionMethod($this->_table, $method);
    return $r->invokeArgs($this->_table, $arguments);
  }

}