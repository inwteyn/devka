<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 05.04.12
 * Time: 11:07
 * To change this template use File | Settings | File Templates.
 */
class Touch_Controller_Action_Helper_ContextSwitch extends Core_Controller_Action_Helper_ContextSwitch
{
  public function __construct($options = null)
  {
    if ($options instanceof Zend_Config) {
        $this->setConfig($options);
    } elseif (is_array($options)) {
        $this->setOptions($options);
    }

    if (empty($this->_contexts)) {
        $this->addContexts(array(
            'json' => array(
                'suffix'    => 'json',
                'headers'   => array('Content-Type' => 'application/json'),
                'callbacks' => array(
                    'init' => 'initJsonContext',
                    'post' => 'postJsonContext'
                )
            ),
            'xml'  => array(
                'suffix'    => 'xml',
                'headers'   => array('Content-Type' => 'application/xml'),
            ),
            'html' => array(
                'suffix'    => '',
                'headers'   => array('Content-Type' => 'text/html'),
                /*
                'callbacks' => array(
                    'init' => 'initHtmlContext',
                    'post' => 'postHtmlContext'
                )*/
            ),
            'async' => array(
                'suffix'    => '',
                'headers'   => array('Content-Type' => 'text/html'),
                'layout' => 'async',
            ),
            'smoothbox' => array(
                'suffix'    => '',
                'headers'   => array('Content-Type' => 'text/html'),
                'layout' => 'default-simple',
            ),
            'touchajax' => array(
                'suffix'    => '',
                'headers'   => array('Content-Type' => 'text/html'),
                'layout' => 'ajax',
            ),
            'frame' => array(
                'suffix'    => '',
                'headers'   => array('Content-Type' => 'text/html'),
                'layout' => 'default-simple',
            )
        ));
    }

    $this->init();
  }

  public function preDispatch()
  {
    $controller = $this->getActionController();
    if( !empty($controller->{$this->_autoContextSwitchKey}) )
    {
      $actionName = $this->getActionController()->getRequest()->getActionName();
      $this
        ->addActionContext($actionName, 'json')
        ->addActionContext($actionName, 'html')
        ->addActionContext($actionName, 'async')
        ->addActionContext($actionName, 'smoothbox')
        ->addActionContext($actionName, 'touchajax')
        ->addActionContext($actionName, 'frame')
        ->initContext();
    }
  }

}
