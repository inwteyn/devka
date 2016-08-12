<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Album.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Wall_Form_Album_Album extends Engine_Form
{
  public function init()
  {

    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $user = Engine_Api::_()->user()->getViewer();

    // Init form
    $this
      ->setTitle('Add New Photos')
      ->setDescription('Choose photos on your computer to add to this album.')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('name', 'albums_create')
      ->setAttrib('enctype','multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    
    // Init album

    $albumTable = Engine_Api::_()->getItemTable('advalbum_album');
    $myAlbums = $albumTable->select()
        ->from($albumTable, array('album_id', 'title'))
        ->where('owner_type = ?', 'user')
        ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
        ->query()
        ->fetchAll();

    $albumOptions = array('0' => 'Create A New Album');
    foreach( $myAlbums as $myAlbum ) {
      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
    }

    $this->addElement('Select', 'album', array(
      'label' => 'Choose Album',
      'multiOptions' => $albumOptions,
      'onchange' => "updateTextFields()",
    ));
    
    // Init name
    $this->addElement('Text', 'title', array(
      'label' => 'Album Title',
      'maxlength' => '40',
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));


    // Init file
    $this->addElement('FancyUpload', 'file');
    //$file = new Engine_Form_Element_FancyUpload('file');

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Photos',
      'type' => 'submit',
    ));
  }

/*  public function clearAlbum()
  {
    //$this->getElement('album')->setValue(0);
  }*/
  
  /*public function saveValues()
  {
    $set_cover = false;
    $values = $this->getValues();
    $params = Array();
    if ((empty($values['owner_type'])) || (empty($values['owner_id'])))
    {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      $params['owner_type'] = 'user';
    }
    else
    {
      $params['owner_id'] = $values['owner_id'];
      $params['owner_type'] = $values['owner_type'];
      throw new Zend_Exception("Non-user album owners not yet implemented");
    }

    if( ($values['album'] == 0) )
    {
      $params['title'] = $values['title'];
      if (empty($params['title'])) {
        $params['title'] = "Untitled Album";
      }
      $params['category_id'] = (int) @$values['category_id'];
      $params['description'] = $values['description'];
      $params['search'] = $values['search'];

      $album = Engine_Api::_()->getDbtable('albums', 'album')->createRow();
      $album->setFromArray($params);
      $album->save();

      $set_cover = true;

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = key($form->auth_view->options);
        if( empty($values['auth_view']) ) {
          $values['auth_view'] = 'everyone';
        }
      }
      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = key($form->auth_comment->options);
        if( empty($values['auth_comment']) ) {
          $values['auth_comment'] = 'owner_member';
        }
      }
      if( empty($values['auth_tag']) ) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if( empty($values['auth_tag']) ) {
          $values['auth_tag'] = 'owner_member';
        }
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $tagMax = array_search($values['auth_tag'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
      }
    }
    else
    {
      if (!isset($album))
      {
        $album = Engine_Api::_()->getItem('album', $values['album']);
      }
    }

    // Add action and attachments
    $api = Engine_Api::_()->getDbtable('actions', 'activity');
    $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['file'])));

    // Do other stuff
    $count = 0;
    foreach( $values['file'] as $photo_id )
    {
      $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
      if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;
      
      if( $set_cover )
      {
        $album->photo_id = $photo_id;
        $album->save();
        $set_cover = false;
      }
      
      $photo->album_id = $album->album_id;
      $photo->order    = $photo_id;
      $photo->save();

      if( $action instanceof Activity_Model_Action && $count < 8 )
      {
        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
      $count++;
    }

    return $album;
  }*/

}
