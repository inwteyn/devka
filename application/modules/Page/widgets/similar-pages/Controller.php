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
class Page_Widget_SimilarPagesController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;
    public function indexAction()
    {
        if (Engine_Api::_()->core()->hasSubject('page')) {
            $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
        } else {
            return $this->setNoRender();
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();


        $table = Engine_Api::_()->getItemTable('page');


        $fo = null;
        /*$fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
        foreach($fieldStructure as $s) {
            print_arr($s->toArray());
            if($s->option_id) {
                $fo = $s->getOption();
                break;
            }
        }*/
        $category = $subject->getCategory();
        $categoryId = $category['cat_id'];

        if (!$categoryId) {
            return $this->setNoRender();
        }

        $params['ipp'] = 9;
        $params['setId'] = 1;
        $params['search'] = 1;
        $params['approved'] = 1;
        $params['fields']['profile_type'] = $categoryId;

        $likesTable = new Core_Model_DbTable_Likes();
        $likesSelect = $likesTable->select()
            ->from('engine4_core_likes', array('resource_id'))
            ->where('resource_type="page"')
            ->where('poster_type="user"')
            ->where('poster_id=?', $viewer->getIdentity());

        $ids = $likesTable->fetchAll($likesSelect);
        $tmpIds = array();
        foreach ($ids as $id) {
            $tmpIds[] = $id->resource_id;
        }
        $tmpIds[] = $subject->getIdentity();
        $ids = '(' . implode(',', $tmpIds) . ')';

        $select = $table->getSelect($params);
        $select->where('engine4_page_pages.page_id not in ' . $ids);
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber(1);

        $arr = array();
        foreach($paginator as $item) {
            $arr[] = ($item->getIdentity());
        }


        if (!$paginator->getTotalItemCount()) {
            return $this->setNoRender();
        }

        $this->view->items = $paginator;
        if( $this->_getParam('titleCount', false)) {
            $this->_childCount = $paginator->getTotalItemCount();
        }
    }

    public function getChildCount()
    {
        return $this->_childCount;
    }
}