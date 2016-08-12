<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hecontest_Api_Core
{

    public function isPageEnabled()
    {
        $hecoreTbl = Engine_Api::_()->getDbTable('modules', 'hecore');
        if(!$hecoreTbl->isModuleEnabled('page')) {
            return false;
        }
        $pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');
        $select = $pagesTbl->select();
        $pages = $pagesTbl->fetchAll($select);
        if(!count($pages)) {
            return false;
        }
        return true;
    }
    public function buyContest($buyer, $contest){
        /**
         * @var $table Credit_Model_DbTable_Logs
         * @var $actionTypes Credit_Model_DbTable_ActionTypes
         * @var $buyerBalance Credit_Model_Balance
         */

        $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
        $table = Engine_Api::_()->getDbTable('logs', 'credit');

        $buyerBalance = Engine_Api::_()->getItem('credit_balance', $buyer->getIdentity());
        if (!$buyerBalance) {
            $buyerBalance = Engine_Api::_()->getItemTable('credit_balance')->createRow();
            $buyerBalance->balance_id = $buyer->getIdentity();
            $buyerBalance->save();
        }

        $buy_emoticon = $actionTypes->getActionType('buy_hecontest');
        if(!$buy_emoticon){
            $a = $actionTypes->createRow();
            $a->action_type = 'buy_hecontest';
            $a->group_type = 'spent';
            $a->action_module = 'hecontest';
            $a->action_name = 'Buy contest %s';
            $a->credit = '1';
            $a->max_credit = '0';
            $a->rollover_period = '0';
            $a->save();
            $buy_emoticon = $actionTypes->getActionType('buy_hecontest');
        }
        $row = $table->createRow();
        $row->user_id = $buyer->getIdentity();
        $row->action_id = $buy_emoticon->action_id;
        $row->credit = $contest;
        //$row->object_type = 'emoticon';
        //$row->object_id = $collection_id;
        $row->creation_date = new Zend_Db_Expr('NOW()');

        $buyerBalance->setCredits($contest);
        $row->save();
    }
}