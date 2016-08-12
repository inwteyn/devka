<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Content
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Widget.php 9999 2013-03-26 21:31:27Z jung $
 */

/**
 * @category   Engine
 * @package    Engine_Content
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Engine_Content_Element_Widget extends Engine_Content_Element_Abstract
{
  protected $_action;

  protected $_request;

  protected $_throwExceptions = false;

  protected $_widget;

  public function __construct($options = null)
  {
    parent::__construct($options);
    //$this->_throwExceptions = ( APPLICATION_ENV === 'development' );
  }

  public function setAction($action)
  {
    $this->_action = $action;
    return $this;
  }

  public function setRequest(Zend_Controller_Request_Abstract $request)
  {
    $this->_request = $request;
    return $this;
  }

  public function setThrowExceptions($flag = true)
  {
    $this->_throwExceptions = (bool) $flag;
    return $this;
  }

  public function getWidget()
  {
    return $this->_widget;
  }

  protected function _render()
  {
    // TODO Optimizer Michael's modification

    $is_ie87 = false;
    if (!empty($_SERVER['HTTP_USER_AGENT'])){
      $matches = array();
      preg_match('/MSIE\s([\d]+)/', $_SERVER['HTTP_USER_AGENT'], $matches);
      if (!empty($matches) && !empty($matches[1]) && ($matches[1] == 8 || $matches[1] == 7)){
        $is_ie87 = true;
      }
    }

    $is_timeline = (Zend_Controller_Front::getInstance()->getRequest()->getModuleName() == 'timeline');

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('optimizer')
      && Zend_Controller_Front::getInstance()->getRequest()->getModuleName() != 'optimizer'
      && !(
        Zend_Controller_Front::getInstance()->getRequest()->getModuleName() == 'core' &&
          Zend_Controller_Front::getInstance()->getRequest()->getControllerName() == 'widget'
      )
      && empty($_GET['no_ajax']) && Engine_Api::_()->getDbTable('settings', 'core')->getSetting('optimizer.ajax.enabled', 1)
      && !($is_timeline && $is_ie87) // IE 8 and 7 is not supported
    ){

      // the widget will be loaded after
      if ($this->getParam('ajaxPostLoading', 0)){

        $subject = '';
        if (Engine_Api::_()->core()->hasSubject()){
          $subject = Engine_Api::_()->core()->getSubject()->getGuid();
        }

        // Registry to post loading
        Engine_Api::_()->optimizer()->addAjaxWidget(
          $this->getIdentity(),
          array(
            'subject' => $subject,
            'isTitle' => (int)$this->getDecorator('Title'),
            'name' => $this->getName()
          )
        );

        // display a loading icon
        $html = '<ul id="loader_content_id_' . $this->getIdentity() . '" class="heloader" style="">
        <li class="li1"></li>
        <li class="li2"></li>
        <li class="li3"></li>
        <li class="li4"></li>
        <li class="li5"></li>
      </ul>';

        return $html;
      }
    }
    // TODO end Optimizer Michael's modification

    try {
      $contentInstance = Engine_Content::getInstance();
      $this->_widget = $contentInstance->loadWidget($this->getName());

      // don't throw exception if it's because the module is not installed / disabled
      if(!$this->_widget){
        $this->setNoRender();
        return '';
      }

      $this->_widget->setElement($this);
      if( null !== $this->_request ) {
        $this->_widget->setRequest($this->_request);
      }
      $this->_widget->render($this->_action);
      return $this->_widget->getContent();
    } catch( Exception $e ) {
      $this->setNoRender();
      if( $this->_throwExceptions ) {
        throw $e;
      } else {
        if( !($e instanceof Engine_Exception) ) {
          $log = Zend_Registry::get('Zend_Log');
          $log->log($e->__toString(), Zend_Log::CRIT);
        }
        // Silence
        //if( APPLICATION_ENV === 'development' ) {
        //  trigger_error('Exception thrown while rendering widget: ' . $e->__toString(), E_USER_WARNING);
        //}
      }
      return '';
    }
  }
}