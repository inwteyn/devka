<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Action.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Wall_Model_Action extends Activity_Model_Action
{
    public $grouped_subjects = array();
    protected $_attachments;
    protected $_types_exception = array(
        'post_self',
        'post'
    );

    public function getView()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    }

    public function getContent()
    {
        $model = Engine_Api::_()->getApi('core', 'wall');
        $params = array_merge(
            $this->toArray(),
            (array)$this->params,
            array(
                'subject' => $this->getSubject(),
                'object' => $this->getObject(),
            )
        );
        //$content = $model->assemble($this->body, $params);
        $content = $model->assemble($this->getTypeInfo()->body, $params, $this);


        return $content;
    }

    public function getType()
    {
        return 'activity_action';
    }

    public function getCheckin()
    {
        $checkin = Engine_Api::_()->getDbTable('modules', 'core');
        if (!$checkin->isModuleEnabled('checkin')) {
            return 0;
        }

        return $checkin->getActionById($this->action_id);
    }

    public function hasObjectItem()
    {
        return Engine_Api::_()->hasItemType($this->object_type);
    }

    public function canChangePrivacy($viewer)
    {
        if (!$viewer) {
            return;
        }
        return (('user' == $this->subject_type && $viewer->getIdentity() == $this->subject_id));
    }


    public function changePrivacy($privacy)
    {
        $privacy_type = $this->object_type;
        $privacy_list = Engine_Api::_()->wall()->getPrivacy($privacy_type);

        if (empty($privacy_list)) {
            return;
        }
        if (!in_array($privacy, $privacy_list)) {
            return;
        }

        $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
        $privacyTable->delete(array('action_id = ?' => $this->action_id));
        $privacyTable->createRow(array('action_id' => $this->action_id, 'privacy' => $privacy))->save();

        if ($this->type = 'post_self') {
            $attachmentsTable = Engine_Api::_()->getDbTable('attachments', 'activity');
            $attachments = $attachmentsTable->select()
                ->where('action_id = ?', $this->action_id)
                ->query()
                ->fetchAll();

            if (sizeof($attachments)) {
                $auth = Engine_Api::_()->getDbTable('allow', 'authorization');

                foreach ($attachments as $attachment) {
                    $auth->update(array('role' => $privacy),
                        array(
                            'resource_type = ?' => 'album_photo',
                            'resource_id = ?' => $attachment['id'],
                            'action = ?' => 'view'
                        ));
                }
            }
        }

        Engine_Api::_()->getDbTable('actions', 'wall')->resetActivityBindings($this);


    }


    public function getHref($params = array())
    {

        $slug = '';
        $object = $this->getObject();
        if ($object && method_exists($object, 'getSlug')) {
            $slug = $object->getSlug($object->getTitle());
        }

        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble(array_merge($params, array(
                'id' => $this->getIdentity(),
                'object' => $slug
            )), 'wall_view', true);
    }


    public function getPeopleTags()
    {
        $table = Engine_Api::_()->getDbTable('tags', 'wall');
        $select = $table->select()
            ->where('action_id = ?', $this->getIdentity())
            ->where('is_people = 1')
            ->order('tag_id ASC')
            ->limit(100);

        return $table->fetchAll($select);

    }

    public function getTags()
    {

        $table = Engine_Api::_()->getDbTable('tags', 'wall');
        $select = $table->select()
            ->where('action_id = ?', $this->getIdentity())
            ->where('is_people = 0')
            ->order('tag_id ASC')
            ->limit(100);

        return $table->fetchAll($select);

    }


    public function canRemoveTag(Core_Model_Item_Abstract $object)
    {
        if ($object->getType() != 'user') {
            return false;
        }
        $tags = $this->getTags();

        $has_me = false;

        foreach ($tags as $item) {
            if ($item->object_type == $object->getType() && $item->object_id == $object->getIdentity()) {
                $has_me = true;
            }
        }

        $people_tags = $this->getPeopleTags();

        foreach ($people_tags as $item) {
            if ($item->object_type == $object->getType() && $item->object_id == $object->getIdentity()) {
                $has_me = true;
            }
        }

        return $has_me;

    }

    public function comments()
    {
        $commentable = $this->getTypeInfo()->commentable;
        if (in_array($this->type, $this->_types_exception)) {
            $commentable = 1;
        }
        switch ($commentable) {
            // Comments linked to action item
            default:
            case 0:
            case 1:
                return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'activity'));
                break;

            // Comments linked to subject
            case 2:
                return $this->getSubject()->comments();
                break;

            // Comments linked to object
            case 3:
                return $this->getObject()->comments();
                break;
            case 4:
                $attachments = $this->getAttachments();
                if (!isset($attachments[0]) || !$attachments[0]->item) {
                    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'activity'));
                    break;
                }
                return $attachments[0]->item->comments();
                break;
        }

        throw new Activity_Model_Exception('Comment handler undefined');
    }

    public function likes()
    {
        $commentable = $this->getTypeInfo()->commentable;
        if (in_array($this->type, $this->_types_exception)) {
            $commentable = 1;
        }
        switch ($commentable) {
            // Comments linked to action item
            default:
            case 0:
            case 1:
                return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'activity'));
                break;

            // Comments linked to subject
            case 2:
                return $this->getSubject()->likes();
                break;

            // Comments linked to object
            case 3:
                return $this->getObject()->likes();
                break;
            case 4:
                $attachments = $this->getAttachments();
                if (!isset($attachments[0]) || !$attachments[0]->item) {
                    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'activity'));
                    break;
                }
                return $attachments[0]->item->likes();
                break;
        }

        throw new Activity_Model_Exception('Likes handler undefined');
    }

    public function getComments($commentViewAll)
    {
        if (null !== $this->_comments) {
            return $this->_comments;
        }

        $comments = $this->comments();
        if (get_class($comments->getSender()) != 'Wall_Model_Action') {
            $table = $comments->getReceiver();
            $comment_count = $comments->getCommentCount();

            $reverseOrder = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.commentreverseorder', false);

            // Always just get the last three comments
            $select = $comments->getCommentSelect();
            $select->where('poster_type != ?', 'comment');

            if ($comment_count <= 5) {
                $select->limit(5);
            } else if (!$commentViewAll) {
                if ($reverseOrder)
                    $select->limit(5);
                else
                    $select->limit(5, $comment_count - 5);

                //$total = $table->select()->union(array($select, $select2));
                // 'SELECT * from User LIMIT 1 UNION SELECT * from User LIMIT 74,1';

            }
        } else {
            $table = $comments->getReceiver();
            $comment_count = $this->getCommentCountAction();

            if ($comment_count <= 0) {
                // return;
            }

            // Always just get the last three comments
            $select = $this->getCommentSelect($this);
            $select->where('poster_type != ?', 'comment');
            if ($comment_count <= 5) {
                $select->limit(5);
            } else if (!$commentViewAll) {
                $select->limit(5, $comment_count - 5);

                //$total = $table->select()->union(array($select, $select2));
                // 'SELECT * from User LIMIT 1 UNION SELECT * from User LIMIT 74,1';

            }

        }
        return $this->_comments = $table->fetchAll($select);

    }

    public function getCommentSelect(Core_Model_Item_Abstract $resource)
    {
        $comment = Engine_Api::_()->getDbTable('comments', 'activity');
        $select = $comment->select();


        $select
            ->where('resource_id = ?', $resource->getIdentity())
            ->where('poster_type != ?', 'comment');

        return $select;
    }

    public function getCommentCountAction()
    {
        $comment = Engine_Api::_()->getDbTable('comments', 'activity');
        $select = $comment->select()->where('poster_type != ?', 'comment')->where('resource_id = ?', $this->getIdentity());

        $c = $comment->fetchAll($select);
        return (int)count($c);
    }

    public function getCommentsLikes($comments, $viewer)
    {
        if (empty($comments)) {
            return array();
        }

        $firstComment = $comments[0];
        if (!is_object($firstComment) ||
            !method_exists($firstComment, 'likes')
        ) {
            return array();
        }

        $likes = $firstComment->likes();
        $table = $likes->getReceiver();

        $ids = array();

        foreach ($comments as $c) {
            $ids[] = $c->comment_id;
        }

        $select = $table
            ->select()
            ->from($table, 'resource_id')
            ->where('resource_id IN (?)', $ids)
            ->where('poster_type = ?', $viewer->getType())
            ->where('poster_id = ?', $viewer->getIdentity());

        if ($table instanceof Core_Model_DbTable_Likes) {
            $select->where('resource_type = ?', $firstComment->getType());
        }

        $isLiked = array();

        $rs = $table->fetchAll($select);

        foreach ($rs as $r) {
            $isLiked[$r->resource_id] = true;
        }

        return $isLiked;
    }

    public function getAttachments()
    {
        if (null !== $this->_attachments) {
            return $this->_attachments;
        }

        if ($this->attachment_count <= 0) {
            return null;
        }

        $table = Engine_Api::_()->getDbtable('attachments', 'activity');
        $select = $table->select()
            ->where('action_id = ?', $this->action_id);

        foreach ($table->fetchAll($select) as $row) {
            $item = Engine_Api::_()->getItem($row->type, $row->id);
            if ($item instanceof Core_Model_Item_Abstract) {
                $val = new stdClass();
                $val->meta = $row;
                $val->item = $item;
                $this->_attachments[] = $val;
            }
        }

        return $this->_attachments;
    }

    public function getCommentable()
    {
        $commentable = $this->getTypeInfo()->commentable;
        if ($commentable !== 4) {
            return $commentable;
        }
        $attachment = $this->getFirstAttachment();
        if (!($attachment && $attachment->item instanceof Core_Model_Item_Abstract) || !method_exists($attachment->item, 'comments') || !method_exists($attachment->item, 'likes')) {
            $commentable = 1;
        }

        return $commentable;
    }

    public function getCommentableItem()
    {
        $commentable = $this->getCommentable();

        // Comments linked to the first attachment
        if ($commentable === 4) {
            return $this->getFirstAttachment()->item;
        }

        return $this->getObject();
    }
}