<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Music.php 2010-10-21 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagemusic
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Pagemusic_Form_Music extends Engine_Form
{
  protected $_playlist;

  public function init()
  {
    // Init form
    $this
      ->setTitle('pagemusic_Add New Songs')
      ->setDescription('pagemusic_Choose music from your computer to add to this playlist.')
      ->setAttrib('id', 'form-upload-music')
      ->setAttrib('name', 'playlist_create')
      ->setAttrib('class', 'global_form hidden')
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save'), 'page_music', true));

    $this->addElement('Hidden', 'music_fancyuploadfileids');
    $this->addElement('Hidden', 'music_art_fileid');

    $this->addElement('Hidden', 'page_id');
    $this->addElement('Hidden', 'playlist_id');

    // Init name
    $this->addElement('Text', 'music_title', array(
      'label' => 'Playlist Name',
      'maxlength' => '63',
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));

    $this->addElement('Text', 'music_tags', array(
      'label' => 'Tags (Keywords)',
      'autocomplete' => 'on',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->music_tags->getDecorator("Description")->setOption("placement", "append");

    // Init descriptions
    $this->addElement('Textarea', 'music_description', array(
      'label' => 'pagemusic_Playlist Description',
      'maxlength' => '300',
      'filters' => array(
        'StripTags',
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '300')),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    // Init art uploader
    $fancyUpload2 = new Engine_Form_Element_FancyUpload('music_art');
    $fancyUpload2->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
        'viewScript' => 'fancy_upload_art.tpl',
        'placement' => '',
      ));

    Engine_Form::addDefaultDecorators($fancyUpload2);
    $this->addElement($fancyUpload2);

    // Init file uploader
    $fancyUpload = new Engine_Form_Element_FancyUpload('music_file');
    $fancyUpload->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
        'viewScript' => 'fancy_upload.tpl',
        'placement' => '',
      ));

    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    // Init submit
    $this->addElement('Button', 'music_submit', array(
      'label' => 'pagemusic_Save Playlist',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'music_cancel', array(
      'label' => 'cancel',
      'link' => false,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('music_submit', 'music_cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }

  public function clearUploads()
  {
    $this->getElement('music_fancyuploadfileids')->setValue('');
    $this->getElement('music_art_fileid')->setValue('');
  }

  public function saveValues()
  {
    $playlist = null;
    $values = $this->getValues();
    $translate = Zend_Registry::get('Zend_Translate');
    $viewer = Engine_Api::_()->user()->getViewer();
    $storage = Engine_Api::_()->storage();

    $file_ids = array();
    foreach (explode(' ', $values['music_fancyuploadfileids']) as $file_id) {
      $file_id = trim($file_id);
      if (!empty($file_id)) {
        $file_ids[] = $file_id;
      }
    }

    if (!empty($values['playlist_id'])) {
      $playlist = Engine_Api::_()->getItem('playlist', $values['playlist_id']);

      $playlist->title = trim($values['music_title']);
      $playlist->description = trim($values['music_description']);

      if (empty($playlist->title)) {
        $playlist->title = $translate->_('_PAGEMUSIC_UNTITLED_PLAYLIST');
      }

      $file = $storage->get($values['music_art_fileid']);
      if ($file) {
        $playlist->photo_id = $file->getIdentity();
      } else {
        $playlist->photo_id = 0;
      }
      $array = array(
        'tag' => $values['music_tags'],
        'count' => count($file_ids)
      );
      $playlist->search = 1;
      $playlist->track_count += count($file_ids);
      $playlist->save();

      if (count($file_ids)) {
        $activity = Engine_Api::_()->getDbtable('actions', 'activity');
        if (count($values['music_file']) > 0) {
          $action = $activity->addActivity(
            Engine_Api::_()->user()->getViewer(),
            $playlist->getPage(),
            'pagemusic_playlist_new',
            null,
            $array
          );

          if (null !== $action) $activity->attachActivity($action, $playlist);

          Engine_Api::_()->page()->sendNotification($playlist, 'post_pagemusic');
        }
      }

    } else {
      $playlist = $this->_playlist = Engine_Api::_()->getDbTable('playlists', 'pagemusic')->createRow();
      $playlist->title = trim($values['music_title']);

      if (empty($playlist->title)) {
        $playlist->title = $translate->_('_PAGEMUSIC_UNTITLED_PLAYLIST');
      }

      $playlist->owner_type = 'user';
      $playlist->owner_id = $viewer->getIdentity();
      $playlist->description = trim($values['music_description']);
      $playlist->page_id = (int)$values['page_id'];

      $file = $storage->get($values['music_art_fileid']);
      if ($file) {
        $playlist->photo_id = $file->getIdentity();
      } else {
        $playlist->photo_id = 0;
      }

      $playlist->search = 1;
      $playlist->track_count = count($file_ids);
      $playlist->save();
      $array = array(
        'tag' => $values['music_tags'],
        'count' => count($file_ids)
      );
      $values['playlist_id'] = $playlist->playlist_id;

      $playlist = $this->_playlist = Engine_Api::_()->getItem('playlist', $values['playlist_id']);

      if (count($file_ids)) {
        $activity = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activity->addActivity(
          Engine_Api::_()->user()->getViewer(),
          $playlist->getPage(),
          'pagemusic_playlist_new',
          null,
          $array
        );

        if (null !== $action) $activity->attachActivity($action, $playlist);

        Engine_Api::_()->page()->sendNotification($playlist, 'post_pagemusic');
      }

    }

    $search_api = Engine_Api::_()->getDbTable('search', 'page');

    if (!empty($file_ids)) {
      foreach ($file_ids as $file_id) {
        $song = $playlist->addSong($file_id);
        $search_api->saveData($song);
      }
    }

    $tags = preg_split('/[,]+/', $values['music_tags']);
    $playlist->tags()->setTagMaps($viewer, $tags);

    $search_api->saveData($playlist);

    return $playlist;
  }

}