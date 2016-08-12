<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Pageinstagram_Widget_ProfileInstagramController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {

        if(Engine_Api::_()->core()->hasSubject('page')){
            $subject_id = Engine_Api::_()->core()->getSubject('page')->page_id;
            $this->view->page_id = $subject_id;
        }

        $page = Engine_Api::_()->getItem('page', $subject_id);
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->owner = $page->isOwner($viewer);


        $viewer = Engine_Api::_()->user()->getViewer();

        if($viewer->isAdmin()){
            $admin = true;
        }else{
            $admin = false;
        }

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $option = $settings->getSetting('page.count.item.on.page');

        $this->view->admin = $admin;
        $table = Engine_Api::_()->getDbTable('instagrams', 'pageinstagram');
        $select = $table->select()
                        ->where('page_id = ?',$subject_id);
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($option);

        $this->view->paginator = $paginator->setCurrentPageNumber( 1 );
    }
}