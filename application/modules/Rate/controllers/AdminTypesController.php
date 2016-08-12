<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminTypesController.php 2010-07-02 19:27 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_AdminTypesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('rate_admin_main', array(), 'rate_admin_main_review');

    // if pages not installed or disbled
//    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page') && !Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
//      $this->_forward('notfound', 'error', 'core');
//      return;
//  	}

    $categories = Engine_Api::_()->getApi('core', 'rate')->getPageCategories();
    $categories2 = Engine_Api::_()->getApi('core', 'rate')->getStoreCategories();

    $this->view->categories = array();
    $category_ids = array();
    foreach ($categories as $category) {
      $category_ids[] = $category->option_id;
      $this->view->categories[$category->option_id] = $category->label;
    }

    $this->view->categories2 = array();
    $category2_ids = array();
    foreach ($categories2 as $category2) {
        $category2_ids[] = $category2->option_id;
        $this->view->categories2[$category2->option_id] = $category2->label;
    }


    $category_id = $this->_getParam('category_id', false);
    $category2_id = $this->_getParam('category2_id', false);

    if (!$category_id || !in_array($category_id, $category_ids)) {
      $category_id = $category_ids[0];
    }
    $this->view->category_id = $category_id;
    if (!$category2_id || !in_array($category2_id, $category2_ids)) {
      $category2_id = $category2_ids[0];
    }
    $this->view->category2_id = $category2_id;

    $type_key = 'type_';

    $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');
    $select = $tbl_type->select()
        ->where('category_id = ?', $category_id)
        ->where('type = ?', 'page')
        ->order('order');
    $types = $tbl_type->fetchAll($select);
    $select2 = $tbl_type->select()
        ->where('category_id = ?', $category2_id)
        ->where('type = ?', 'store')
        ->order('order');
    $types2 = $tbl_type->fetchAll($select2);

    $form = new Rate_Form_Review_TypeEdit();
    $form2 = new Rate_Form_Review_TypeEdit();

    $create_link = '<div class="create">'.$this->view->htmlLink(
      array(
        'module' => 'rate',
        'controller' => 'types',
        'action' => 'create',
        'category'=>'getPageCategories',
        'category_id' => $category_id,
        'type'=>'page'
      ),
      $this->view->translate('RATE_REVIEW_TYPECREATE_TITLE'),
      array('class' => 'smoothbox')
    ).'</div>';

    $create2_link = '<div class="create">'.$this->view->htmlLink(
      array(
        'module' => 'rate',
        'controller' => 'types',
        'action' => 'create',
        'category'=>'getStoreCategories',
        'category_id' => $category2_id,
         'type'=>'store'
      ),
      $this->view->translate('RATE_REVIEW_TYPECREATE_TITLE'),
      array('class' => 'smoothbox')
    ).'</div>';

    $form->submit->addDecorator('TypeSubmit', array('element2' => $create_link));
    $form2->submit->addDecorator('TypeSubmit', array('element2' => $create2_link));

    $type_ids = array();
    $counter = 0;
    $count = count($types);
    $type2_ids = array();
    $counter2 = 0;
    $count2 = count($types2);

    if (!$count){
      $form->addElement('Hidden', 'tip', array(
        'ignore' => true,
        'order' => 1
      ));
      $form->tip->addDecorator('TypeTip', array(
        'text' => $this->view->translate('RATE_REVIEW_TYPEEDITFORM_TIP')
      ));
    }

    foreach ($types as $type){

      $type_id = $type->getIdentity();


      $name = $type_key.$type_id;

      $form->addElement('Text', $name, array('value' => $type->label));

      $elements = "<div class='options'>";
      if ($counter != 0){
        $title = $this->view->translate('RATE_REVIEW_TYPEEDITFORM_MOVEUP');
        $img = $this->view->htmlImage($this->view->baseUrl().'/application/modules/Rate/externals/images/moveup.gif', $title);
        $link = $this->view->url(array('id' => $type_id, 'moveup' => 'true','type'=>'page'));
        $elements .= $this->view->htmlLink($link, $img, array('title' => $title));
      }
      if ($counter != $count-1){
        $title = $this->view->translate('RATE_REVIEW_TYPEEDITFORM_MOVEDOWN');
        $img = $this->view->htmlImage($this->view->baseUrl().'/application/modules/Rate/externals/images/movedown.gif', $title);
        $link = $this->view->url(array('id' => $type_id, 'movedown' => 'true','type'=>'page'));
        $elements .= $this->view->htmlLink($link, $img, array('title' => $title));
      }

      $title = $this->view->translate('RATE_REVIEW_TYPEEDITFORM_DELETE');
      $img = $this->view->htmlImage($this->view->baseUrl().'/application/modules/Rate/externals/images/delete.png', $title);
      $link = $this->view->url(array('id' => $type_id, 'delete' => 'true','type'=>'page'));
      // if demoadmin
      if (Engine_Api::_()->user()->getViewer()->getIdentity() == 1250) {
        $link = $this->view->url(array('id' => $type_id,'type'=>'page'));
      }
      $elements .= $this->view->htmlLink($link, $img, array(
        'title' => $title,
        'onClick' => 'return confirm("'.$this->view->translate('RATE_REVIEW_TYPE_DELETE').'");'
      ));

      $elements .= "</div>";

      $form->getElement($name)->addDecorator('TypeItem', array('element2' => $elements));

      $type_ids[] = $type_id;
      $counter++;

    }

    $this->view->form = $form;
    $this->view->count_types = $counter;

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())){
      $values = $form->getValues();
      foreach ($values as $key => $value){
        $key_id = substr($key, strlen($type_key), strlen($key)-strlen($type_key));
        if (!in_array($key_id, $type_ids)){ continue; }
        $tbl_type
            ->findRow($key_id)
            ->setFromArray(array('label' => $value))
            ->save();
      }
    }

    $row_id = $this->_getParam('id', false);
    $type_of = $this->_getParam('type');

    if ($row_id) {
        $row = $tbl_type->fetchRow($tbl_type->select()->where('type_id = ? ',$row_id)->where('type =?',$type_of));
        if ($row) {
            $category_id = $row->category_id;
            if ($this->_getParam('delete', false)) {
                $row->delete();
            }
            if ($this->_getParam('moveup', false)) {
                $row->changeOrder(true);
            }
            if ($this->_getParam('movedown', false)) {
                $row->changeOrder(false);
            }
            $this->_redirectCustom($this->view->url(array(
                    'module' => 'rate',
                    'controller' => 'types',
                    'action' => 'index'
                ), 'admin_default', true) . '?category_id=' . $category_id);
            return;
        }
    }

//**************************************************************************************************************************************
      if (!$count2){
          $form2->addElement('Hidden', 'tip', array(
              'ignore' => true,
              'order' => 1
          ));
          $form2->tip->addDecorator('TypeTip', array(
              'text' => $this->view->translate('RATE_REVIEW_TYPEEDITFORM_TIP')
          ));
      }

      foreach ($types2 as $type2){

          $type2_id = $type2->getIdentity();

          $name = $type_key.$type2_id;

          $form2->addElement('Text', $name, array('value' => $type2->label));

          $elements2 = "<div class='options'>";
          if ($counter2 != 0){
              $title = $this->view->translate('RATE_REVIEW_TYPEEDITFORM_MOVEUP');
              $img2 = $this->view->htmlImage($this->view->baseUrl().'/application/modules/Rate/externals/images/moveup.gif', $title);
              $link2 = $this->view->url(array('id' => $type2_id, 'moveup' => 'true','type'=>'store'));
              $elements2 .= $this->view->htmlLink($link2, $img2, array('title' => $title));
          }
          if ($counter2 != $count2-1){
              $title= $this->view->translate('RATE_REVIEW_TYPEEDITFORM_MOVEDOWN');
              $img2 = $this->view->htmlImage($this->view->baseUrl().'/application/modules/Rate/externals/images/movedown.gif', $title);
              $link2 = $this->view->url(array('id' => $type2_id, 'movedown' => 'true','type'=>'store'));
              $elements2 .= $this->view->htmlLink($link2, $img2, array('title' => $title));
          }

          $title = $this->view->translate('RATE_REVIEW_TYPEEDITFORM_DELETE');
          $img2 = $this->view->htmlImage($this->view->baseUrl().'/application/modules/Rate/externals/images/delete.png', $title);
          $link2 = $this->view->url(array('id' => $type2_id, 'delete' => 'true','type'=>'store'));
          // if demoadmin
          if (Engine_Api::_()->user()->getViewer()->getIdentity() == 1250) {
              $link2 = $this->view->url(array('id' => $type2_id));
          }
          $elements2 .= $this->view->htmlLink($link2, $img2, array(
              'title' => $title,
              'onClick' => 'return confirm("'.$this->view->translate('RATE_REVIEW_TYPE_DELETE').'");'
          ));

          $elements2 .= "</div>";

          $form2->getElement($name)->addDecorator('TypeItem', array('element2' => $elements2));

          $type2_ids[] = $type2_id;
          $counter2++;

      }

      $this->view->form2 = $form2;
      $this->view->count_types2 = $counter2;

      if ($this->getRequest()->isPost() && $form2->isValid($this->getRequest()->getPost())){
          $values = $form2->getValues();
          foreach ($values as $key => $value){
              $key_id = substr($key, strlen($type_key), strlen($key)-strlen($type_key));
              if (!in_array($key_id, $type2_ids)){ continue; }
              $tbl_type
                  ->findRow($key_id)
                  ->setFromArray(array('label' => $value))
                  ->save();
          }
      }

      $row2_id = $this->_getParam('id', false);



      if ($row2_id){
          $row2 = $tbl_type->fetchRow($tbl_type->select()->where('type_id=?',$row_id)->where('type',$this->_getParam('type')));
          if ($row2){
              $category2_id = $row2->category_id;
              if ($this->_getParam('delete', false))
              { $row2->delete(); }
              if ($this->_getParam('moveup', false))
              { $row2->changeOrder(true); }
              if ($this->_getParam('movedown', false))
              { $row2->changeOrder(false); }
              $this->_redirectCustom($this->view->url(array(
                      'module' => 'rate',
                      'controller' => 'types',
                      'action' => 'index'
                  ), 'admin_default', true) . '?category2_id='.$category2_id);
              return;
          }
      }
  }

  public function createAction(){


    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')){
      $this->_forward('notfound', 'error', 'core');
      return;
  	}
    $this->_helper->layout->setLayout('default-simple');

      $ttt = $this->_getParam('category');
     

    $categories = Engine_Api::_()->getApi('core', 'rate')->$ttt();

    $this->view->categories = array();
    $category_ids = array();
    foreach ($categories as $category){
      $category_ids[] = $category->option_id;
    }

    $category_id = $this->_getParam('category_id', false);

    if (!$category_id || !in_array($category_id, $category_ids)){
      $category_id = $category_ids[0];
    }
    $this->view->category_id = $category_id;

    $this->view->form = $form = new Rate_Form_Review_TypeCreate;
    $form->getElement('category')->setValue($category_id);

    $this->view->redirect = false;

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())){

      $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');

      $select = $tbl_type->select()
          ->from($tbl_type->info('name'), 'MAX(`order`)')
          ->where('category_id = ?', $category_id)
          ->where('type =?',$this->_getParam('type'));
      $max = (int)$tbl_type->getAdapter()->fetchOne($select);

//print_die($max);

      $row =  array(
        'category_id' => $category_id,
        'label' => $form->getValue('label'),
        'order' => $max+1,
        'type'=>$this->_getParam('type')
      );

      $tbl_type->createRow($row)->save();

      $this->view->redirect = true;

    }

  }

}