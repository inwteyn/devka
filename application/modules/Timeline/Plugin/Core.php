<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Timeline_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        if (!(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('apptouch') &&
            Engine_Api::_()->apptouch()->isApptouchMode()) &&
            (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('touch') &&
                Engine_Api::_()->touch()->isTouchMode() ||
                Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('mobile') &&
                    Engine_Api::_()->mobile()->isMobileMode())
        ) {
            return false;
        }


        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        /**
         * @var $settings Core_Api_Settings
         */
        $settings = Engine_Api::_()->getApi('settings', 'core');

        if ($module == 'user' && $controller == 'profile' && $action == 'index') {

            if ($settings->__get('timeline.usage', 'choice') == 'force') {
                $request->setModuleName('timeline');
                return;
            }

            $id = $request->getParam('id');

            $user = Engine_Api::_()->user()->getUser($id);
            if ($user->getIdentity()) {
                $user = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($user->getIdentity());
            }

            if ($user->getIdentity() && Engine_Api::_()->getDbTable('settings', 'user')->getSetting($user, 'timeline-usage')) {
                $request->setModuleName('timeline');
                return;
            }
        }
    }

    public function onRenderLayoutDefault($event, $mode = null)
    {
        $view = $event->getPayload();
        if (!($view instanceof Zend_View_Interface)) {
            return;
        }
        $view->headScript()->appendFile($view->baseUrl() . '/application/modules/Timeline/externals/scripts/core.js');
    }

    public function onRenderLayoutAdmin($event, $mode = null)
    {
        $view = $event->getPayload();
        if (!($view instanceof Zend_View_Interface)) {
            return;
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if ($controller == 'admin-content' && $action == 'index') {
            $view->headScript()->appendFile($view->baseUrl() . '/application/modules/Timeline/externals/scripts/admin/core.js');
        }
    }

    public function onSubjectTimelinePhotoUpload($event)
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if(!$viewer->getIdentity()) {
            return;
        }
        $payload = $event->getPayload();

        if (empty($payload['subject']) || !($payload['subject'] instanceof Core_Model_Item_Abstract)) {
            return;
        }
        if (empty($payload['file']) || !($payload['file'] instanceof Storage_Model_File)) {
            return;
        }

        $subject = $payload['subject'];
        $file = $payload['file'];
        $type = $payload['type'];

        $item_id = $subject->getIdentity();
        $item_type = $subject->getType();

        $api = Engine_Api::_()->getApi('core', 'timeline');
        $album = null;

        if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album')) {
            return null;
        }

        $albumTable = Engine_Api::_()->getDbtable('albums', 'timeline');
        $photoTable = $api->getSubjectPhotosTable($item_type, $item_id);
        $isPageAlbumsEnabled = false;
        switch ($item_type) {
            case 'page':
                $album = $albumTable->getSpecialPageAlbum($viewer, $subject, 'page_cover');

                $album_type = $album->getType();

                $isPageAlbumsEnabled = (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('pagealbum') && ($album_type == 'pagealbum'));

                if ($isPageAlbumsEnabled) {
                    $photoTable = Engine_Api::_()->getDbtable('pagealbumphotos', 'pagealbum');
                    $newPhotoArray = array(
                        'owner_id' => $viewer->getIdentity(),
                        'collection_id' => $album->getIdentity(),
                        'owner_type' => 'user'
                    );
                } else {
                    $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
                    $newPhotoArray = array(
                        'owner_type' => 'user',
                        'owner_id' => $viewer->getIdentity()
                    );
                }
                break;
            case 'event':
                $album = $albumTable->getSpecialEventAlbum($subject, 'cover');
                $newPhotoArray = array(
                    'user_id' => $viewer->getIdentity(),
                    'album_id' => $album->getIdentity(),
                    'collection_id' => $album->getIdentity(),
                    'event_id' => $item_id
                );
                break;
            case 'offer':
                $owner = $subject->getOwner();
                $newPhotoArray = array(
                    'collection_id' => $item_id,
                    'owner_id' => $owner->getIdentity()
                );
                break;
            case 'group':
                $album = $albumTable->getSpecialGroupAlbum($subject, 'cover');
                $newPhotoArray = array(
                    'user_id' => $viewer->getIdentity(),
                    'collection_id' => $album->getIdentity(),
                    'album_id' => $album->getIdentity(),
                    'group_id' => $item_id
                );
                break;
            default:
                $album = $albumTable->getSpecialAlbum($subject, $type);
                $newPhotoArray = array(
                    'owner_type' => $item_type,
                    'owner_id' => $item_id
                );
                break;
        }
        if (!$newPhotoArray) {
            return;
        }

        $photo = $photoTable->createRow();
        $photo->setFromArray($newPhotoArray);
        $photo->save();
        $photo->setPhoto($file);

        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);

        if ($album) {
            if ($item_type == 'page' && $isPageAlbumsEnabled) {
                $photo->collection_id = $album->getIdentity();
            } else {
                $photo->album_id = $album->getIdentity();
            }

            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);
        }

        $event->addResponse($photo);
    }
}
