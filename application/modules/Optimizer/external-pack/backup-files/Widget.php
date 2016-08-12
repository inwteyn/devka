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

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('optimizer')
        && Zend_Controller_Front::getInstance()->getRequest()->getModuleName() != 'optimizer'
        && empty($_GET['no_ajax'])
    ){
      // the widget will be loaded after
      if ($this->getParam('ajaxPostLoading', 0)){

        // Registry to post loading
        Engine_Api::_()->optimizer()->addAjaxWidget($this->getIdentity());

        // display a loading icon
        $html = '<img
        src="' . $this->getView()->layout()->staticBaseUrl . 'application/modules/Optimizer/externals/images/loading.gif"
        alt=""
        class="optimizer_loading"
        id="loader_content_id_' . $this->getIdentity() . '"/>';
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