<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Event.php 19.10.13 08:20 jungar $
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
 * Time: 11:17
 * To change this template use File | Settings | File Templates.
 */
class Hecontest_Model_Photo extends Core_Model_Item_Abstract
{
    public function getUser()
    {
        $user = Engine_Api::_()->getItem('user', $this->user_id);
        if(!$user->getIdentity()) {
            return null;
        }
        return $user;
    }

    public function getParent($resourceType = null)
    {
        $user = Engine_Api::_()->getItem('user', $this->user_id);
        if(!$user->getIdentity()) {
            return null;
        }
        return $user;
    }

    public function getContest()
    {
        $contestsTbl = Engine_Api::_()->getItemTable('hecontest');
        return $contestsTbl->getContest($this->contest_id);
    }

    public function getHref()
    {
        $contest = $this->getContest();
        $action = 'contestview';
        $route_name = 'hecontest_general_view';


        $params = array(
          'route' => $route_name,
          'action' => $action,
          'contest_id' => $contest->getIdentity(),
          'reset' => true,
        );
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        $href = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset) . '#' . $this->getIdentity();
        return $href;
    }
    public function getTouchHref()
    {
        $contest = $this->getContest();

        $route = 'hecontest_general';
        $action = ($contest->is_active) ? 'index' : 'recent';

        $params = array(
            'action' => $action,
            'id' => $this->getIdentity()
        );

        $href = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
        return $href;
    }

    public function getPhotoUrl($itemType = "hecontest_photo", $type = "thumb.normal")
    {
        $filesTbl = Engine_Api::_()->getDbTable('files', 'storage');
        $select = $filesTbl->select()
            ->where('file_id = ?', $this->file_id);

        $file = $filesTbl->fetchRow($select);

        if (!$file || !$file->storage_path)
            return "application/modules/Hecontest/externals/images/hecontest-no-prize-photo.png";
        else
            return $file->getHref();
    }

    public function getPreviewPhotoUrl()
    {
        $filesTbl = Engine_Api::_()->getDbTable('files', 'storage');
        $select = $filesTbl->select()
            ->where('parent_file_id = ?', $this->file_id);

        $file = $filesTbl->fetchRow($select);

        if (!$file || !$file->storage_path)
            return "application/modules/Hecontest/externals/images/hecontest-no-prize-photo.png";
        else
            return $file->getHref();
    }

    public function allowLike($user_id)
    {
        if (!$user_id) {
            return false;
        }
        if ($user_id == $this->user_id) {
            return false;
        }

        $user = Engine_Api::_()->getItem('user', $user_id);
        $authTb = Engine_Api::_()->authorization()->getAdapter('levels');
        $vote = $authTb->getAllowed('hecontest', $user, 'vote');
        if(!$vote) {
            return false;
        }

        return true;
    }


    public function isVoter($user_id)
    {
        $votersTbl = Engine_Api::_()->getDbTable('voters', 'hecontest');
        return $votersTbl->isVoter($this->getIdentity(), $user_id);
    }

    public function vote($user_id)
    {
        /*if($this->isVoter($user_id)) {
            $this->unlike($user_id);
        } else {
            $this->like($user_id);
        }*/
        $isVoter = $this->isVoter($user_id);

        $votersTbl = Engine_Api::_()->getDbTable('voters', 'hecontest');
        $db = $votersTbl->getAdapter();
        $db->beginTransaction();
        try {
            if($isVoter) {
                $row = $votersTbl->unVote($this->getIdentity(), $user_id);
                $db->commit();
                $this->votes--;
                $this->save();
                $db->commit();
            } else {
                $row = $votersTbl->createRow(array(
                    'photo_id' => $this->getIdentity(),
                    'user_id' => $user_id
                ));
                $row->save();
                $db->commit();

                $this->votes++;
                $this->save();
                $db->commit();
            }
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    public function like($user_id) {
        $votersTbl = Engine_Api::_()->getDbTable('voters', 'hecontest');
        $db = $votersTbl->getAdapter();
        $db->beginTransaction();
        try {
            $row = $votersTbl->createRow(array(
                'photo_id' => $this->getIdentity(),
                'user_id' => $user_id
            ));
            $row->save();

            $this->votes++;
            $this->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }
    public function unlike($user_id) {

    }

    public function destroy()
    {

        $filesTbl = Engine_Api::_()->getItemTable('storage_file');

        $select = $filesTbl->select()
            ->where('parent_file_id = ?', $this->file_id);
        $mini = $filesTbl->fetchRow($select);
        $mini->delete();

        $select = $filesTbl->select()
            ->where('file_id = ?', $this->file_id);
        $file = $filesTbl->fetchRow($select);
        $file->delete();


        Engine_Api::_()->getDbTable('voters', 'hecontest')->cleanVotes($this->getIdentity());


        $this->delete();
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    public function getVoters() {
        $votersTbl = Engine_Api::_()->getDbTable('voters', 'hecontest');

        $select = $votersTbl->select()
            ->where('photo_id=?', $this->getIdentity());

        if (isset($params['limit'])) {
            $select->limit($params['limit']);
        }

        $voters = $votersTbl->fetchAll($select);
        $paginator = Zend_Paginator::factory($voters);
        $ipp = isset($params['ipp']) ? $params['ipp'] : 10;
        $page = isset($params['page']) ? $params['page'] : 1;
        $paginator->setItemCountPerPage($ipp);
        $paginator->setCurrentPageNumber($page);
        return $paginator;
    }
}
