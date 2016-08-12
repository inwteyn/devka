<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Pageevent_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $form = new Pageevent_Form_Search();
    $form->removeElement('search');
    $form->view->removeMultiOption(2);
    $form->view->removeMultiOption(3);

    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('event');

    if( $module && $module->enabled ) {
      $form->addElement('Select', 'category_id', array(
        'label' => 'Category:',
        'multiOptions' => array(
          '' => 'All Categories',
        ),
        'onchange' => "$(this).getParent('form').submit()"
      ));

      foreach( Engine_Api::_()->getDbtable('categories', 'event')->fetchAll() as $row ) {
        $form->category_id->addMultiOption($row->category_id, $row->title);
      }
      if (count($form->category_id->getMultiOptions()) <= 1) {
        $form->removeElement('category_id');
      }
    }

    if( !Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $form->removeElement('view');
    }

    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    $form->populate($params);

    $this->view->form = $form;
  }
}
