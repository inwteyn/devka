<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WidgetController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Core_WidgetController extends Touch_Controller_Action_Standard
{
  public function indexAction()
  {
    $content_id = $this->_getParam('content_id');
    $view = $this->_getParam('view');
    $show_container = $this->_getParam('container', true);
    $params = $this->_getAllParams();

    // Render by content row
    if( null !== $content_id ) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'touch');
      $row = $contentTable->find($content_id)->current();
      if( null !== $row ) {
        // Build full structure from children
        $page_id = $row->page_id;
        $pageTable = Engine_Api::_()->getDbtable('pages', 'touch');
        $content = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $page_id));
        $structure = $pageTable->createElementParams($row);
        $children = $pageTable->prepareContentArea($content, $row);
        if( !empty($children) ) {
          $structure['elements'] = $children;
        }
        $structure['request'] = $this->getRequest();
        $structure['action'] = $view;

        // Create element (with structure)
        $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
            'Children'
          )
        ));

        // Strip decorators
        if( !$show_container ) {
          foreach( $element->getElements() as $cel ) {
            $cel->clearDecorators();
          }
        }

        $content = $element->render();
        $this->getResponse()->setBody($content);
      }

      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    // Render by widget name
    $mod = $this->_getParam('mod');
    $name = $this->_getParam('name');
    if( null !== $name ) {
      if( null !== $mod ) {
        $name = $mod . '.' . $name;
      }
      $structure = array(
        'type' => 'widget',
        'name' => $name,
        'request' => $this->getRequest(),
        'action' => $view,
      );

      // Create element (with structure)
      $element = new Engine_Content_Element_Container(array(
        'elements' => array($structure),
        'decorators' => array(
          'Children'
        )
      ));

      $content = $element->render();
      $this->getResponse()->setBody($content);

      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    $this->getResponse()->setBody('Aw, shucks.');
    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }
}
