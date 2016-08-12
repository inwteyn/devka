<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Details.php 4/20/12 10:29 AM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Model_DbTable_Details extends Engine_Db_Table
{
    public function getDetail(User_Model_User $user, $key)
    {
        if(!$user->getIdentity()) {
            $token = Engine_Api::_()->store()->getToken();
        }
        switch ($key) {
            case 'country':
                $key = 'location_id_1';
                break;
            case 'state':
            case 'region':
                $key = 'location_id_2';
        }

        try {
            $select = $this->select()
                ->from($this, $key);
            if($token) {
                $select->where('token = ?', $token);
            } else {
                $select->where('user_id = ?', $user->getIdentity());
            }
            $detail = $select->query()->fetchColumn();
        } catch (Exception $e) {
            if ($e->getCode() == 1054) {
                return null;
            }
        }

        return $detail;
    }

    public function getDetails(User_Model_User $user, $keys = null)
    {
        if(!$user->getIdentity()) {
            $token = Engine_Api::_()->store()->getToken();
        }
        if (null === $keys) {
            $select = $this->select();
            if($token) {
                $select->where('token = ?', $token);
            } else {
                $select->where('user_id = ?', $user->getIdentity());
            }
            $data = $select->query()->fetch();
        } else if (is_array($keys) && count($keys) > 1) {
            foreach ($keys as $key) {
                $data[$key] = $this->getDetail($user, $key, $token);
            }
        } else {
            return null;
        }

        return $data;
    }

    public function setDetail(User_Model_User $user, $key, $value)
    {
        if(!$user->getIdentity()) {
            $token = Engine_Api::_()->store()->getToken();
        }
        $select = $this->select();

        if($token) {
            $select->where('token = ?', $token);
        } else {
            $select->where('user_id = ?', $user->getIdentity());
        }

        switch ($key) {
            case 'country':
                $key = 'location_id_1';
                break;
            case 'state':
            case 'region':
                $key = 'location_id_2';
        }

        if (null == ($row = $this->fetchRow($select)) || !isset($row->$key)) {
            return false;
        }

        $row->$key = $value;

        return $row->save();
    }

    public function setDetails(User_Model_User $user, $data)
    {
        if(!$user->getIdentity()) {
            $token = Engine_Api::_()->store()->getToken();
        }
        $select = $this->select();

        if($token) {
            $select->where('token = ?', $token);
        } else {
            $select->where('user_id = ?', $user->getIdentity());
        }

        if (null == ($row = $this->fetchRow($select))) {
            $row = $this->createRow();
        }

        $row->setFromArray($data);
        $row->user_id = $user->getIdentity();
        $row->location_id_1 = $data['country'];
        if (isset($data['state'])) {
            $row->location_id_2 = $data['state'];
        }
        if($token) {
            $row->token = $token;
        }

        return $row->save();
    }

}
