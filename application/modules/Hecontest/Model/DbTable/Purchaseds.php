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
class Hecontest_Model_DbTable_Purchaseds extends Engine_Db_Table
{
    public function getPaidedContest($contest_id){
        $viewer = Engine_Api::_()->user()->getViewer();
        $select = $this->select()->where('contest_id = ?',$contest_id)->where('user_id = ?',$viewer->getIdentity())->where('status = 1');
        $result = $this->fetchRow($select);
        if(count($result)>0){
            return 1;
        }else{
            return 0;
        }
    }
    public function setPaidedContest($contest_id,$user_id){
        $row  = $this->createRow();
        $row->contest_id = $contest_id;
        $row->status = '1';
        $row->user_id = $user_id;
        $row->save();
        return true;
    }

}
