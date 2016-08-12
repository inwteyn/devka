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
class Pageinstagram_Widget_RandomInstagramController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {

        $table = Engine_Api::_()->getDbTable('instagrams', 'pageinstagram');
        $select = $table->select()->order("rand()");

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(3);
        $this->view->paginator = $paginator->setCurrentPageNumber( 1 );
        $this->view->page_number = 1;

    }
}