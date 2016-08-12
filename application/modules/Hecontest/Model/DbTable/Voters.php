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
class Hecontest_Model_DbTable_Voters extends Engine_Db_Table
{
    protected $_name = 'hecontest_voters';

    public function vote($photo_id, $user_id) {

    }

    public function unVote($photo_id, $user_id) {
        $select = $this->select()->where('photo_id=?', $photo_id)
            ->where('user_id=?', $user_id);
        $row = $this->fetchRow($select);
        $row->delete();
    }

    public function isVoter($photo_id, $user_id)
    {
        $select = $this->select()->where('photo_id=?', $photo_id)
            ->where('user_id=?', $user_id);
        $result = $this->fetchAll($select);
        return count($result);
    }

    public function cleanVotes($photo_id)
    {
        $select = $this->select()->where('photo_id=?', $photo_id);
        $result = $this->fetchAll($select);
        foreach ($result as $item) {
            $item->delete();
        }
    }
}
