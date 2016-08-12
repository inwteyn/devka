<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-09-06 16:05 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Pagealbum_Widget_ProfileRandomController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $this->getElement()->setTitle('');

        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }

        //Check this module. Is enabled in core modules and exists in page modules and page content?
        if (!Engine_Api::_()->getDbTable('content', 'page')->getEnabledExistAddOns($subject->getIdentity(), 'pagealbum')) {
            return $this->setNoRender();
        }

        $table = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
        $this->view->photos = $photos = $table->getAllPhotos($subject->getIdentity(), 9); //todo settings

        if (!count($photos)) {
            return $this->setNoRender();
        }
    }
}