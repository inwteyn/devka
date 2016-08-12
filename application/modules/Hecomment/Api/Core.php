<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Hecomment_Api_Core extends Activity_Api_Core
{
  public function addComment($resource, $poster, $body)
  {
    $table = Engine_Api::_()->getDbTable('comments', 'core');
    $row = $table->createRow();

    if (isset($row->resource_type)) {
      $row->resource_type = 'comment';
    }

    $row->resource_id = $resource;
    $row->poster_type = $poster->getType();
    $row->poster_id = $poster->getIdentity();

    $row->creation_date = date('Y-m-d H:i:s');
    $row->body = $body;
    $row->save();
    if ($row) {
      $select = $table->select()->where('comment_id=?', $resource);
      $comment = $table->fetchRow($select);
    }

    if ($comment) {
      return $comment;
    } else {
      return false;
    }
  }

  public function removeReplyComment($resource, $comment)
  {
    $row = $comment;
    if (null === $row) {
      throw new Core_Model_Exception('No comment found to delete');
    }

    $row->delete();

    if (isset($resource->comment_count)) {
      $resource->save();
    }

    return $this;
  }

  //Add Like for  Comments Reply
    public function addLikeReplyComment($resource, $poster)
    {
      $thisLike = Engine_Api::_()->getDbtable('likes', 'core');
      $table = $thisLike;
      $row = $table->createRow();
      $row->resource_type = $resource->getType();
      $row->resource_id = $resource->getIdentity();
      $row->poster_type = 'user';
      $row->poster_id = $poster->getIdentity();
      $row->save();
      if (isset($resource->like_count)) {
        $resource->like_count++;
        $resource->save();
      }
      return $row;
    }

  //Remove Like for Comments Reply
    public function removeLikeReplyComment($resource, $poster)
    {
      $row = $this->getLike($resource, $poster);
      if (null === $row) {
        throw new Core_Model_Exception('No like to remove');
      }
      $row->delete();
      if (isset($resource->like_count)) {
        $resource->like_count--;
        $resource->save();
      }
      return $this;
    }
  // Get Like comment reply
  protected function getLike($resource, $poster)
  {
    $table = Engine_Api::_()->getDbtable('likes', 'core');
    $select = $table->select()->where('resource_type = ?', $resource->getType())->where('resource_id = ?', $resource->getIdentity())->where('poster_type = ?', $poster->getType())->where('poster_id = ?', $poster->getIdentity())->order('like_id ASC')->limit(1);
    return $table->fetchRow($select);
  }
}