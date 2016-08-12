<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Events.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 25.09.13
 * Time: 11:16
 * To change this template use File | Settings | File Templates.
 */
class Hecontest_Model_DbTable_Hecontests extends Engine_Db_Table

{
    protected $_rowClass = "Hecontest_Model_Hecontest";
    protected $_name = 'hecontest_hecontests';

    public function getSelect($params = array())
    {
        $select = $this->select();
        return $select;
    }

    public function getContests($params = array())
    {
        $select = $this->getSelect($params);

        $select->order('hecontest_id desc');

        $ipp = isset($params['ipp']) ? $params['ipp'] : 10;
        $page = isset($params['page']) ? $params['page'] : 1;

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($ipp);
        $paginator->setCurrentPageNumber($page);

        return $paginator;
    }

    public function getActiveContest($contest_id = 0)
    {

        if($contest_id){
            $select = $this->select()->where('hecontest_id = ?', $contest_id);
        }
        $contest = $this->fetchRow($select);
        return $contest;
    }

    public function getActiveContests()
    {
       // $table = Engine_Api::_()->getDbTable('hecontests', 'hecontest');
        $select = $this->fetchAll($this->select()->where('is_active = ?', 1));
        return $select;
    }

    public function getRecentContest()
    {
        $contest = $this->fetchRow($this->select()->where('is_recent = ?', 1));
        return $contest;
    }

    public function getContest($contest_id)
    {
        $select = $this->select()->where('hecontest_id=?', $contest_id);
        $contest = $this->fetchRow($select);
        return $contest;
    }

    public function autoStartContest()
    {
        $select = $this->select()
            ->where('date_begin>?', new Zend_Db_Expr('NOW()'))
            ->orWhere('date_begin=?', new Zend_Db_Expr('NOW()'));
        $res = $this->fetchRow($select);

        if (!$res) {
            return;
        }

        $res->setActive();
    }

    public function deactivateAll()
    {
        $items = $this->fetchAll($this->select());
        foreach($items as $item) {
            $item->setRecent();
        }
    }
}
