<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_PageController extends Touch_Controller_Action_Standard
{
	public function init()
	{
		$page_id = $this->_getParam('page_id');
		$this->view->page = $page = Engine_Api::_()->getItem('page', $page_id);

		if ($page == null) {
			$this->_redirectCustom(array('route' => 'page_browse'));
			return ;
		}

		if( !$this->_helper->requireUser()->isValid() || !$page->isTeamMember()) {
			$this->_redirectCustom(array('route' => 'page_browse'));
  		return ;
  	}
	}

  public function deleteAction()
  {
  	$page_id = $this->_getParam('page_id');
 		$page = $this->view->page;

  	$this->view->form = $form = new Page_Form_Delete();
    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'onclick' => 'parent.Smoothbox.close();'
      ));
    }

  	$form->setAction($this->view->url(array('action' => 'delete', 'page_id' => $page_id), 'page_team'));
  	$description = sprintf(Zend_Registry::get('Zend_Translate')
  	  ->_('PAGE_DELETE_DESC'), $this->view->htmlLink($page->getHref(), $page->getTitle()));

  	$form->setDescription($description);

  	if (!$this->getRequest()->isPost()) {
  		return;
  	}

  	$db = Engine_Api::_()->getDbtable('pages', 'page')->getAdapter();
    $db->beginTransaction();

    try {
    	$page->delete();
    	$db->commit();
    } catch (Exception $e) {
    	$db->rollBack();
    	throw $e;
    }

    return $this->_forward('success', 'utility', 'touch', array(
      'messages' =>array($this->view->message),
      'parentRedirect' => $this->view->url(array(), 'page_manage', true),
    ));

  }

  public function editAction()
  {
  	$page_id = $this->_getParam('page_id');
 		$page = $this->view->page;
    if ($this->is_iPhoneUploading()){
      if (!isset($_FILES['picup-image-upload'])){
        return ;
      }
      try{
      $file = $_FILES['picup-image-upload'];
      $file = $this->fileUpload($file, $page->user_id);
      $this->view->photo_name = (isset($file['name'])) ? $file['name'] : '';
      $this->view->photo_id = $file->file_id;
      return;
      } catch (Exception $e) {
        $this->view->error = $e->getMessage();
      }
    } else {
      $aliasedFields = $page->fields()->getFieldsObjectsByAlias();
      $this->view->topLevelId = $topLevelId = 0;
      $this->view->topLevelValue = $topLevelValue = null;

      if( isset($aliasedFields['profile_type']) ) {
        $aliasedFieldValue = $aliasedFields['profile_type']->getValue($page);
        $topLevelId = $aliasedFields['profile_type']->field_id;
        $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
        if( !$topLevelId || !$topLevelValue ) {
          $topLevelId = null;
          $topLevelValue = null;
        }
        $this->view->topLevelId = $topLevelId;
        $this->view->topLevelValue = $topLevelValue;
      }

      $this->view->fieldForm = $fieldForm = new Page_Form_Edit(array('item' => $page,'topLevelId' => $topLevelId,'topLevelValue' => $topLevelValue));
      $this->view->photoForm = $photoForm = $this->getPhotoForm();
      $this->view->admins = $page->getAdmins();

      $fieldForm->setAction($this->view->url(array('action' => 'edit', 'page_id' => $page_id), 'page_team'));
      $photoForm->setAction($this->view->url(array('action' => 'edit', 'page_id' => $page_id), 'page_team'));

      $auth = Engine_Api::_()->authorization()->context;

      $roles = array('team', 'likes', 'registered', 'everyone');
      foreach ($roles as $roleString) {
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        }
        elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }
      }

      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString){
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        }
        elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

      }

      $roles = array('team', 'likes', 'registered');
      foreach ($roles as $roleString){
        $role = $roleString;

        if( $role === 'team' ) {
          $role = $page->getTeamList();
        }
        elseif( $role === 'likes' ) {
          $role = $page->getLikesList();
        }

      }

      $this->view->edit = $edit = $this->_getParam('edit');

      if ($this->getRequest()->isPost()) {
        try {
          $db = Engine_Api::_()->getDbTable('pages', 'page')->getAdapter();
          $db->beginTransaction();
          switch ($edit) {
            case 'info' :
              if ($fieldForm->isValid($this->getRequest()->getPost())){
                $fieldForm->saveValues();
                if ($fieldForm->getSubForm('extra')->isValid($this->_getParam('extra'))){
                  $values = $this->_getParam('extra');
                  $address = array($values['country'], $values['city'], $values['street']);

                  if ($address[0] == '' && $address[1] == '' && $address[2] == '') {
                    $page->deleteMarker();
                  }elseif ($page->isAddressChanged($address)){
                    $page->addMarkerByAddress($address);
                  }

                  $raw_tags = preg_split('/[,]+/', $values['tags']);
                  $tags = array();
                  foreach ($raw_tags as $tag){
                    $tag = trim(Engine_String::strip_tags($tag));
                    if ($tag == ""){
                      continue ;
                    }
                    $tags[] = $tag;
                  }
                  $page->tags()->setTagMaps(Engine_Api::_()->user()->getViewer(), $tags);

                  $misTypes = array('http//', 'htp://', 'http://');
                  $values['website'] = str_replace($misTypes, '', trim($values['website']));

                  if (function_exists('mb_convert_encoding')) {
                    $values['description'] = mb_convert_encoding(Engine_String::strip_tags( $values['description'] ), 'UTF-8');
                    $values['title'] = mb_convert_encoding(Engine_String::strip_tags( $values['title'] ), 'UTF-8');
                  } else {
                    $values['description'] = Engine_String::strip_tags($values['description']);
                    $values['title'] = Engine_String::strip_tags($values['title']);
                  }

                  $page->setFromArray($values);
                  $page->displayname = $page->title;
                  $page->keywords = $values['tags'];
                }

                $fieldForm->setAttrib('class', 'page_edit_form');
                $fieldForm->addNotice(Zend_Registry::get('Zend_Translate')->_('Chagnes were successfully saved.'));
              }
              break;
            case 'photo' :
              $values = $this->getRequest()->getPost();
              if ($photoForm->isValid($this->getRequest()->getPost())){
                $photo_id = $values['photo_id'];
                if($photo_id){
                  if ( null != ($photo = Engine_Api::_()->storage()->get($photo_id)) )
                  {
                    $page->setPhoto($photo->storage_path);
                  }
                } else if ($photoForm->photo->getValue() !== null){
                  $page->setPhoto($photoForm->photo);
                }
                $photoForm->setAttrib('class', 'form-wrapper page_edit_form');
                $photoForm->addNotice(Zend_Registry::get('Zend_Translate')->_('Image was successfully proccessed.'));
              }

              break;
            default:
              $photoForm->addError(Zend_Registry::get('Zend_Translate')->_('Do not specified editting type.'));
              break;
          }

          $page->modified_date = date('Y-m-d H:i:s');
          $page->save();
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }

      $tags = $page->tags()->getTagMaps();
      $tagString = '';
      foreach ( $tags as $tagmap ) {
        if( $tagString !== '' ) $tagString .= ', ';
        $tagString .= $tagmap->getTag()->getTitle();
      }

  //		$fieldForm->getSubForm('extra')->description->setValue($page->getDescription());
  //		$fieldForm->getSubForm('extra')->title->setValue($page->getTitle());

      $photoForm->populate($page->toArray());
    }
  }

	public function postNoteAction()
	{
    $page_id = $this->_getParam('page_id');

    $this->view->form = $form = new Touch_Form_Standard;
    $form
        ->addElement('Textarea', 'note', array('label' => 'TOUCH_PAGE_NOTE'))
        ->addElement('Hidden', 'page_id', array('value' => $page_id))
        ->addElement('Button', 'submit', array(
          'label' => 'TOUCH_PAGE_POST_SUBMIT',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array('ViewHelper')
        ))
        ->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'decorators' => array(
            'ViewHelper'
          ),
        ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $form->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');

    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'onclick' => 'parent.Smoothbox.close();'
      ));
    }

    $page = $this->view->page;

    $form->note->setValue($page->note);

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

		$page->note = trim(Engine_String::strip_tags($form->getValue('note')));
		$page->save();

    return $this->_forward('success', 'utility', 'touch', array(
      'messages' =>array($this->view->message),
      'parentRedirect' => $this->view->url(array('action' => 'manage'), 'page_browse', true),
    ));



	}

  public function deletePhotoAction()
  {
  	$page_id = $this->_getParam('page_id');
 		$page = $this->view->page;

  	$page->removePhotos();
  	$page->photo_id = 0;
  	$page->save();

  	$this->_redirectCustom(array('route' => 'page_team', 'action' => 'edit', 'page_id' => $page_id));
  }
  protected function getPhotoForm(){
    $form = new Page_Form_Photo();
    $form->addElement('Hidden', 'photo_id', array('id'=>'photo_id'));
    return $form;
  }
  public function fileUpload($file, $user_id)
  {
    $user = Engine_Api::_()->getItem('user', $user_id);
    if (!$user){
      return ;
    }
    try {
      $params = array(
        'parent_type' => 'temporary',
        'parent_id' => 0,
        'user_id' => $user->getIdentity()
      );
      return Engine_Api::_()->storage()->create($file, $params);

    } catch (Exception $e){
      return ;
    }

  }
}
