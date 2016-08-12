<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CoverController.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Timeline_PhotoController extends Core_Controller_Action_Standard
{
    protected $_type;
    protected $item_type;
    protected $item_id;
    protected $subject;

    protected $setting_name;
    protected $position_setting_name;
    protected $cover_parent_setting_name;

    public function init()
    {

        $subject = null;
        if (!Engine_Api::_()->core()->hasSubject()) {
            $id = $this->_getParam('item_id', null);
            $item_type = $this->_getParam('item_type', null);

            $subject = Engine_Api::_()->getItem($item_type, $id);
            Engine_Api::_()->core()->setSubject($subject);
        }

        if (!$subject) {
            return $this->_helper->content->setNoRender();
        }

        $this->subject = $subject;
        $this->item_id = $subject->getIdentity();
        $this->item_type = $subject->getType();
        $this->view->type = $this->_type = $this->_getParam('type', 'cover');
        $this->setting_name = Engine_Api::_()->timeline()->getCoverPhotoSetting($this->item_id, $this->item_type, $this->_type);
        $this->position_setting_name = Engine_Api::_()->timeline()->getCoverPhotoPositionSetting($this->item_id, $this->item_type, $this->_type);
        $this->cover_parent_setting_name = Engine_Api::_()->timeline()->getCoverParentSetting($this->item_id, $this->item_type, $this->_type);

        $this->_helper->contextSwitch
            ->addActionContext('get', 'json')
            ->addActionContext('set', 'json')
            ->addActionContext('position', 'json')
            ->initContext();
    }

    public function albumsAction()
    {
        /*if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return $this->_helper->content->setNoRender();
        }

        if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid()) {
            return $this->_helper->content->setNoRender();
        }*/

        $this->view->page = $page = $this->_getParam('page', 1);

        $table = Engine_Api::_()->timeline()->getSubjectAlbumTable($this->item_type);
        $this->view->paginator = $paginator = Engine_Api::_()->timeline()->getSubjectAlbums($this->item_id, $this->item_type, $table);

        $this->view->subject = $this->subject;
        $this->view->just_items = $this->_getParam('just_items', false);
        $paginator->setItemCountPerPage(9);
        $paginator->setCurrentPageNumber($page);

        if ($this->_getParam('format') != 'html') {
            $this->_helper->layout->setLayout('default-simple');
        }
    }

    public function photosAction()
    {
        /*if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return $this->_helper->content->setNoRender();
        }*/

        $this->view->page = $page = $this->_getParam('page', 1);

        $this->view->album_type = $album_type = $this->_getParam('album_type');
        $this->view->album_id = $album_id = $this->_getParam('album_id');

        $this->view->album = $album = Engine_Api::_()->getItem($album_type, $album_id);

        /*if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->isValid()) {
            return $this->_helper->content->setNoRender();
        }*/

        $this->view->subject = $this->subject;

        // Prepare data

        if ($album_type == 'album') {
            $photoTable = Engine_Api::_()->getItemTable('album_photo');
            $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
                'album' => $album,
            ));
        } else {
            $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        }
        $paginator->setItemCountPerPage(9);
        $paginator->setCurrentPageNumber($page);

        $this->view->just_items = $this->_getParam('just_items', false);

        if ($this->_getParam('format') != 'html') {
            $this->_helper->layout->setLayout('default-simple');
        }
    }

    public function getAction()
    {
        $this->view->position = json_encode(array('top' => 0, 'left' => 0));
        $this->view->coverPhoto = Engine_Api::_()->timeline()->getTimelinePhoto($this->item_id, $this->item_type, $this->_type);
        /*$albumPhoto = Engine_Api::_()->timeline()->getTimelinePhotoObject($this->item_id, $this->item_type, $this->_type);

        if (is_object($albumPhoto)) {
            $albumPhoto = $albumPhoto->getHref();
        }
        if (!strlen(trim($albumPhoto))) {
            $albumPhoto = 'javascript://';
        }
        $this->view->albumPhoto = $albumPhoto;*/
    }

    public function setAction()
    {
        $table = Engine_Api::_()->getDbTable('settings', 'core');

        $photo_id = $this->_getParam('photo_id');
        $photo_type = $this->_getParam('photo_type', 'album_photo');
        if (!$photo_id) {
            $this->view->status = false;
            return;
        }

        $photo = Engine_Api::_()->getItem($photo_type, $photo_id);

        $cover_parent = 'subject';
        if ($photo->getType() == 'pagealbumphoto') {
            $cover_parent = 'pagealbum';
        }
        if ($photo->getType() == 'album_photo') {
            $cover_parent = 'album';
        }
        $table->setSetting($this->cover_parent_setting_name, $cover_parent);
        $table->setSetting($this->setting_name, $photo_id);
        $this->view->status = true;
    }

    public function positionAction()
    {
        $position_tmp = $this->_getParam('position', array());

        $position = array('top' => 0, 'left' => 0);

        if (isset($position_tmp['top'])) {
            $position['top'] = (int)$position_tmp['top'];
        }

        if (isset($position_tmp['left'])) {
            $position['left'] = (int)$position_tmp['left'];
        }

        $table = Engine_Api::_()->getDbTable('settings', 'core');
        $setting_name = Engine_Api::_()->timeline()->getCoverPhotoPositionSetting($this->_getParam('item_id'), $this->_getParam('item_type'), $this->_getParam('type'));
        $this->view->status = (boolean)$table->setSetting($setting_name, serialize($position));
    }

    public function uploadAction()
    {
        /**
         * @var $subject Core_Model_Item_Abstract
         * @var $table Core_Model_DbTable_Settings
         */

        $subject = $this->subject;
        $table = Engine_Api::_()->getDbTable('settings', 'core');

        $this->view->form = $form = new Timeline_Form_Photo_Upload();

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Uploading a new photo
        if ($form->Filedata->getValue() !== null) {
            $db = $this->subject->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $fileElement = $form->Filedata;

                $photo_id = $this->setTimelinePhoto($fileElement);
                $table->setSetting($this->setting_name, $photo_id);

                $iMain = Engine_Api::_()->getItem('storage_file', $photo_id);

                // Insert activity
                $activity_type = 'post_self';
                $body = '';

                $attachment = null;
                // Hooks to enable albums to work
                $event = Engine_Hooks_Dispatcher::_()
                    ->callEvent('onSubjectTimelinePhotoUpload', array(
                        'subject' => $subject,
                        'file' => $iMain,
                        'type' => $this->_type
                    ));

                $attachment = $event->getResponse();

                $cover_parent = 'album';


                if (!$attachment) {
                    $attachment = $iMain;
                    $cover_parent = 'storage';
                } else {
                    if ($attachment->getType() == 'pagealbumphoto') {
                        $cover_parent = 'pagealbum';
                    }

                    if ($this->item_type == 'user') {
                        if ($this->_type == 'cover') {
                            $activity_type = 'cover_photo_update';
                            $body = '{item:$subject} added a new cover photo.';
                        } elseif ($this->_type == 'born') {
                            $activity_type = 'birth_photo_update';
                            $body = '{item:$subject} added a new birth photo.';
                        }

                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($this->subject, $this->subject, $activity_type, $body);
                        if($action) Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
                    }
                }


                $table->setSetting($this->setting_name, $attachment->getIdentity());
                $table->setSetting($this->cover_parent_setting_name, $cover_parent);

                $db->commit();
                $this->view->photo_id = $attachment->getIdentity();
            } catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    public function removeAction()
    {
        $this->view->form = $form = new Timeline_Form_Photo_Remove();

        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $table = Engine_Api::_()->getDbTable('settings', 'core');
        $table->setSetting($this->setting_name, 0);

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your photo has been removed.');

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your photo has been removed.'))
        ));
    }

    public function setTimelinePhoto($photo = null)
    {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setTimelinePhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        // @TODO user_id - is null correct?
        $params = array(
            'parent_type' => $this->item_type,
            'parent_id' => $this->item_id,
            'user_id' => 0,
            'name' => basename($fileName),
        );

        /**
         * Save
         * @var $filesTable Storage_Model_DbTable_Files
         */
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
//      ->resize(850, 315)
            ->write($mainPath)
            ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        // Store
        $iMain = $filesTable->createFile($mainPath, $params);

        // Remove temp files
        @unlink($mainPath);

        return $iMain->file_id;
    }
}
