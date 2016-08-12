<?php

/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 01.09.2015
 * Time: 11:05
 */
class Page_Widget_PageLikesController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;

    public function indexAction()
    {
        if (Engine_Api::_()->core()->hasSubject() && Engine_Api::_()->core()->getSubject()->getType() == 'page') {
            $page = Engine_Api::_()->core()->getSubject();

            if (Engine_Api::_()->page()->isModuleExists('like')) {
                $likeApi = Engine_Api::_()->getApi('core', 'like');

                $users = $likeApi->getAllLikesUsers($page);

                $this->view->users = $paginator = Zend_Paginator::factory($users);

                // Set item count per page and current page number
                $paginator->setItemCountPerPage(9);
                $paginator->setCurrentPageNumber($this->_getParam('page', 1));

                // Add count to title if configured
                $this->_childCount = $paginator->getTotalItemCount();
            }
        }
    }

    public function getChildCount()
    {
        return $this->_childCount;
    }
}