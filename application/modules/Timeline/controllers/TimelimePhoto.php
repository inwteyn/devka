<?php
/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 15.04.2015
 * Time: 10:52
 */

class Timeline_TimelineController extends Core_Controller_Action_Standard {

    public function editUserPhotoAction()
    {
        $this->view->user = $user = Engine_Api::_()->core()->getSubject();
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        // Get form
        $this->view->form = $form = new User_Form_Edit_Photo();

        if (empty($user->photo_id)) {
            $form->removeElement('remove');
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Uploading a new photo
        if ($form->Filedata->getValue() !== null) {
            $db = $user->getTable()->getAdapter();
            $db->beginTransaction();

            try {
                $fileElement = $form->Filedata;

                $user->setPhoto($fileElement);

                $iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);

                // Insert activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update',
                    '{item:$subject} added a new profile photo.');

                // Hooks to enable albums to work
                if ($action) {
                    $event = Engine_Hooks_Dispatcher::_()
                        ->callEvent('onUserProfilePhotoUpload', array(
                            'user' => $user,
                            'file' => $iMain,
                        ));

                    $attachment = $event->getResponse();
                    if (!$attachment) $attachment = $iMain;

                    // We have to attach the user himself w/o album plugin
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
                }

                $db->commit();
            } // If an exception occurred within the image adapter, it's probably an invalid image
            catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } // Otherwise it's probably a problem with the database or the storage system (just throw it)
            catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } // Resizing a photo
        else if ($form->getValue('coordinates') !== '') {
            $storage = Engine_Api::_()->storage();

            $iProfile = $storage->get($user->photo_id, 'thumb.profile');
            $iSquare = $storage->get($user->photo_id, 'thumb.icon');

            // Read into tmp file
            $pName = $iProfile->getStorageService()->temporary($iProfile);
            $iName = dirname($pName) . '/nis_' . basename($pName);

            list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));

            $image = Engine_Image::factory();
            $image->open($pName)
                ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
                ->write($iName)
                ->destroy();

            $iSquare->store($iName);

            // Remove temp files
            @unlink($iName);
        }
    }

    public function editPagePhotoAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return 0;

        /**
         * @var $page Page_Model_Page
         */
        $page = Engine_Api::_()->core()->getSubject();
        $this->view->form = $form = new Page_Form_Photo();

        if (empty($page->photo_id)) {
            $form->removeElement('remove');
        }

        if (!$this->getRequest()->isPost()) {
            return 0;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return 0;
        }

        if ($form->Filedata->getValue() !== null) {
            $db = Engine_Api::_()->getDbTable('pages', 'page')->getAdapter();
            $db->beginTransaction();

            try {
                $fileElement = $form->Filedata;

                $page->setPhoto($fileElement);

                $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Image was successfully proccessed.'));

                $page->save();
                $db->commit();
            } catch (Engine_Image_Adapter_Exception $e) {
                $db->rollBack();
                $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } else if ($form->getValue('coordinates') !== '') {
            $storage = Engine_Api::_()->storage();

            $iProfile = $storage->get($page->photo_id, 'thumb.profile');
            $iSquare = $storage->get($page->photo_id, 'thumb.icon');

            // Read into tmp file
            $pName = $iProfile->getStorageService()->temporary($iProfile);
            $iName = dirname($pName) . '/nis_' . basename($pName);

            list($x, $y, $w, $h) = explode(':', $form->getValue('coordinates'));

            $image = Engine_Image::factory();
            $image->open($pName)
                ->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)
                ->write($iName)
                ->destroy();

            $iSquare->store($iName);

            // Remove temp files
            @unlink($iName);
        }
    }
}