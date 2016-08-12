<?php

class Optimizer_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
  }

  public function widgetAction()
  {
    $content_id = (int)$this->_getParam('content_id');

    try {
      $contentTable = Engine_Api::_()->getDbTable('content', 'core');
      $row = $contentTable->fetchRow(array(
        'content_id = ?' => $content_id
      ));

      if (!$row){
        throw new Exception('content_id is not exists');
      }

      // Build full structure from children
      $page_id = $row->page_id;
      $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
      $content = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $page_id));
      $structure = $pageTable->createElementParams($row);
      $children = $pageTable->prepareContentArea($content, $row);
      if (!empty($children)){
        $structure['elements'] = $children;
      }
      $structure['request'] = $this->getRequest();

      // Create element (with structure)
      $element = new Engine_Content_Element_Container(array(
        'elements' => array($structure),
        'decorators' => array(
          'Children'
        )
      ));

      // Strip decorators
      /*      foreach( $element->getElements() as $cel ) {
              $cel->clearDecorators();
            }*/
      /*
            foreach ($this->view->headScript() as $item){
              unset($item);
            }*/
      //

      // Remove Title
      $widget = null;
      $elements = $element->getElements();
      if (!empty($elements[0])){
        $widget = $elements[0];
        if (!$this->_getParam('isTitle')){
          $widget->removeDecorator('Title');
        }
      }

      // Render body
      $html = $element->render();

      // Get title and set if widget in tabs
      $title = "";
      $childCount = 0;
      if ($widget){
        // Get title and childcount
        $title = $widget->getTitle();
        $childCount = null;
        if( method_exists($widget, 'getWidget') && method_exists($widget->getWidget(), 'getChildCount') ) {
          $childCount = $widget->getWidget()->getChildCount();
        }
        if( !$title ) $title = $widget->getName();
      }

      $content = '';
      $html = trim($html);

      if (!empty($html)){
        if ($row->name == 'wall.feed' && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('checkin', 'core')) {
          $container = $this->view->headScript()->getContainer();

          foreach ($container as $key => $dat){
            if ($dat->attributes['src'] == 'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places') {
              unset($container[$key]);
              break;
            }
          }
        }

        $content = $this->view->headScript()->toString();
        $content .= '<script>' . $this->view->headTranslate()->toString() . '</script>';
        $content .= $html;

        // Set widget title via js
        $info = array(
          'title' => $title,
          'childCount' => $childCount,
          'content_id' => $content_id
        );
        $content .= '<script>AjaxWidget.setWidgetTitle('.$this->view->jsonInline($info).')</script>';
      }

      $this->getResponse()->setBody($content);

      $this->_helper->viewRenderer->setNoRender(true);
      return;

    } catch (Exception $e){
      die($e . '');
    }


  }

}
