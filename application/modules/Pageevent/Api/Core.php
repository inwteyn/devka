<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:54 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Pageevent_Api_Core extends Core_Api_Abstract
{

  public function uploadPhoto($photo, $params = array())
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Exception('Invalid argument passed to uploadPhoto: '.print_r($photo,1));
    }

    $extension = ltrim(strrchr($file, '.'), '.');
    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

		if (!$extension){ $extension = 'jpg'; }

    $params    = array_merge(array(
      'name'        => $name,
      'parent_type' => 'pageeventphoto',
      'parent_id'   => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'user_id'     => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'extension'   => $extension,
    ), $params);

    $storage = Engine_Api::_()->storage();

    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(400, 400)
      ->write($path.'/m_'.$name.'.'.$extension)
      ->destroy();

    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 150)
      ->write($path.'/p_'.$name.'.'.$extension)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/ti_'.$name.'.'.$extension)
      ->destroy();

    $iMain       = $storage->create($path.'/m_'.$name.'.'.$extension,  $params);
    $iProfile    = $storage->create($path.'/p_'.$name.'.'.$extension,  $params);
    $iIcon       = $storage->create($path.'/ti_'.$name.'.'.$extension,  $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIcon, 'thumb.icon');

    @unlink($path.'/p_'.$name.'.'.$extension);
    @unlink($path.'/m_'.$name.'.'.$extension);
    @unlink($path.'/ti_'.$name.'.'.$extension);
    @unlink($file);

    return $iMain;
  }

  public function deletePhoto($photo_id)
  {
    $storage = Engine_Api::_()->storage();

    $thumb = $storage->get($photo_id, 'thumb.profile');
    if ($thumb){ $thumb->delete(); }

    $thumb = $storage->get($photo_id, 'thumb.icon');
    if ($thumb){ $thumb->delete(); }

    $photo = $storage->get($photo_id);
    if ($photo){ $photo->delete(); }

  }

  public function getComments($page = null)
  {
    $row = Engine_Api::_()->core()->getSubject();
    if( null !== $page)
    {
      $commentSelect = $row->comments()->getCommentSelect();
      $commentSelect->order('comment_id ASC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(10);
    }
    else
    {
      $commentSelect = $row->comments()->getCommentSelect();
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);
    }

    return $comments;
  }

  public function getInviteMembers($param = array())
  {
    $keyword = (isset($param['keyword'])) ? $param['keyword'] : '';
    $event_id = (isset($param['id'])) ? $param['id'] : 0;

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()){ return ; }

    if (!$event_id){ return ; }

    $event = Engine_Api::_()->getDbTable('pageevents', 'pageevent')->findRow($event_id);
    if (!$event){ return ; }

    $select = $event->membership()->getInviteMembersSelect($viewer);

    if ($keyword) {
      $select->where('u.displayname LIKE ?', "%{$keyword}%");
    }

    return Zend_Paginator::factory($select);
  }

  public function getInviteMembersDisabled($param = array())
  {
    $keyword = (isset($param['keyword'])) ? $param['keyword'] : '';
    $event_id = (isset($param['id'])) ? $param['id'] : 0;

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()){ return ; }

    if (!$event_id){ return ; }

    $event = Engine_Api::_()->getDbTable('pageevents', 'pageevent')->findRow($event_id);
    if (!$event){ return ; }

    $select = $event->membership()->getInviteMembersSelectDisabled($viewer);

    if ($keyword) {
      $select->where('u.displayname LIKE ?', "%{$keyword}%");
    }

    return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

  }

  public function getMembers($param = array())
  {
    $keyword = (isset($param['keyword'])) ? $param['keyword'] : '';
    $event_id = (isset($param['id'])) ? $param['id'] : 0;
    $rsvp = (isset($param['rsvp']) && in_array($param['rsvp'], array(0,1,2))) ? $param['rsvp'] : 2;
    $only_friend = (isset($param['list_type']) && $param['list_type'] == 'mutual');

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer || !$viewer->getIdentity()){ return ; }

    if (!$event_id){ return ; }

    $event = Engine_Api::_()->getDbTable('pageevents', 'pageevent')->findRow($event_id);
    if (!$event){ return ; }

    $select = $event->membership()->getMemberSelect($rsvp, $only_friend);

    if ($keyword) {
      $select->where('u.displayname LIKE ?', "%{$keyword}%");
    }

    return Zend_Paginator::factory($select);

  }

  public function isAllowedPost($pageObject)
  {
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($pageObject, Engine_Api::_()->user()->getViewer(), 'event_posting');
    return (bool) $isAllowedPost;
  }

  public function getInitJs($content_info, $method = '', $subject) /// for SEO by Kirill
  {
    if (empty($content_info))
        return false;
    $content = $content_info['content'];
    $content_id = $content_info['content_id'];
    $res = "Pageevent.init_event();";

    if( $subject->isTimeline() ) {
      $tbl = Engine_Api::_()->getDbTable('content', 'page');
      $id = $tbl->select()->from($tbl->info('name'), array('content_id'))
        ->where('page_id = ?', $subject->getIdentity())
        ->where("name = 'pageevent.profile-event'")
        ->where('is_timeline = 1')
        ->query()
        ->fetch();
      $res = "tl_manager.fireTab('{$id['content_id']}');";
    }

    if ($content == 'page_event'){
        $event = Engine_Api::_()->getItem('pageevent', $content_id);
        if (!$event || !Engine_Api::_()->authorization()->isAllowed($event, null, 'view'))
            return false;
        if (!empty($method)) {
          switch($method){
            case "edit":
              $method = "Pageevent.formEvent({$content_id})";
            break;
            case 'remove':
              $method = "Pageevent.remove({$content_id});";
            break;
            case 'join':
              $method = "Pageevent.remove({$content_id}, 2);";
            break;
            case 'leave':
              $method = "Pageevent.memberApprove({$content_id}, 0);";
            break;
            default:
              $method;
            break;
          }
        }
        return  $res . $method;
    }elseif ($content == 'pageevents'){/// for SEO by Kirill
        if($content_id == 1)
            return $res;/// for SEO by Kirill
        else if($content_id == 2)
            return $res;/// for SEO by Kirill
        else
            return $res;/// for SEO by Kirill
    }elseif ($content == 'event_page') {
        return Pageevent.init_event();
    }
    return false;
  }

  public function getEventsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getEventsSelect($params));

    if( !empty($params['page'] )) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if( !empty($params['ipp'] )) {
      $paginator->setItemCountPerPage($params['ipp']);
    }


    return $paginator;
  }

  public function getEventsSelect($params = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('event');

    //Get Tables
    $pageeventTbl = Engine_Api::_()->getItemTable('pageevent');
    $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
    $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');

    if( !empty($params['view']) && $params['view'] == 1) {
      // Get an array of friend ids
      $table = Engine_Api::_()->getItemTable('user');
      $select = $viewer->membership()->getMembersSelect('user_id');
      $friends = $table->fetchAll($select);
      // Get stuff
      $ids = array();
      foreach( $friends as $friend )
      {
        $ids[] = $friend->user_id;
      }
      $str = "'".join("', '", $ids)."'";
    }

    if( $module && $module->enabled ) {
      $eventTbl = Engine_Api::_()->getItemTable('event');
      $eventselect = $eventTbl->select()
        ->from(array('ev' => $eventTbl->info('name')), array('event_id', 'member_count', 'creation_date', 'starttime', 'view_count', new Zend_Db_Expr("'event' as 'type'")));

      if( !empty($params['search']) ) {
        $eventselect->where('ev.title LIKE ? OR ev.description LIKE ?  OR ev.location LIKE ? ', '%'.$params['search'].'%');
      }

      if( !empty($params['view']) && $params['view'] == 1) {
        $eventselect->where('user_id in (?)', new Zend_Db_Expr($str));
      } elseif(!empty($params['view']) && $params['view'] == 2 && $params['owner']) {

        $eventmembershipTbl = Engine_Api::_()->getDbTable('membership', 'event');
        $eventselect->joinLeft(array('m' => $eventmembershipTbl->info('name')), "ev.event_id = m.resource_id", array())
          ->where('m.user_id = ?', $params['owner']->getIdentity());

      } elseif(!empty($params['view']) && $params['view'] == 3 && $params['owner']) {
        $eventselect->where('user_id = ?', $params['owner']->getIdentity());
      }

      if( !empty($params['category_id']) && $params['category_id'] != 0) {
        $eventselect->where("category_id = ?", $params['category_id']);
      }

      // Endtime
      if( !empty($params['filter']) && $params['filter'] == 'past') {
        $eventselect->where("endtime <= FROM_UNIXTIME(?)", time());
      } elseif( !empty($params['filter']) && $params['filter'] == 'future') {
        $eventselect->where("endtime > FROM_UNIXTIME(?)", time());
      }

      $unionselect = $eventselect;
    }

    $pageeventselect = false;

    if( empty($params['category_id']) || $params['category_id'] == 0) {
      $pageeventselect = $pageeventTbl->select()
        ->from(array('pe' => $pageeventTbl->info('name')), array('event_id' => 'pageevent_id', 'member_count', 'creation_date', 'starttime', 'view_count', new Zend_Db_Expr("'page' as 'type'")))
        ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_type = 'page' AND a.resource_id = pe.page_id AND a.action = 'view'", array())
        ->joinLeft(array('li' => $listitemTbl->info('name')), "li.list_id = a.role_id", array())
        ->joinLeft(array('aa' => $authallowTbl->info('name')), "aa.resource_type = 'pageevent' AND aa.resource_id = pe.pageevent_id AND aa.action = 'view'", array())
        ->joinLeft(array('lii' => $listitemTbl->info('name')), "lii.list_id = aa.role_id", array())
        ->where("(a.role = 'everyone' OR a.role = 'registered' OR li.child_id = ?) AND (aa.role = 'everyone' OR aa.role = 'registered' OR lii.child_id = ?)", $viewer->getIdentity())
        ->group("pe.pageevent_id");

      if( !empty($params['search']) ) {
        $pageeventselect->where('pe.title LIKE ? OR pe.description LIKE ?  OR pe.location LIKE ? ', '%'.$params['search'].'%');
      }

      if(!empty($params['view']) && $params['view'] == 1) {
        $pageeventselect->where('user_id in (?)', new Zend_Db_Expr($str));
      } elseif(!empty($params['view']) && $params['view'] == 2 && $params['owner']) {
        $pageeventMembershipTbl = Engine_Api::_()->getDbTable('pageeventmembership', 'pageevent');
        $pageeventselect->joinLeft(array('pm' => $pageeventMembershipTbl->info('name')), "pe.pageevent_id = pm.resource_id", array())
          ->where("pm.user_id = ?", $params['owner']->getIdentity());
      } elseif(!empty($params['view']) && $params['view'] == 3 && $params['owner']) {
        $pageeventselect->where('user_id = ?', $params['owner']->getIdentity());
      }

      // Endtime
      if( !empty($params['filter']) && $params['filter'] == 'past') {
        $pageeventselect->where("endtime <= FROM_UNIXTIME(?)", time());
      } elseif( !empty($params['filter']) && $params['filter'] == 'future') {
        $pageeventselect->where("endtime > FROM_UNIXTIME(?)", time());
      }

      $unionselect = $pageeventselect;
    }


    if( $module && $module->enabled && $pageeventselect ) {
      $unionselect = Engine_Db_Table::getDefaultAdapter()->select()->union(array($eventselect, $pageeventselect));
    }

    if( empty($params['order']) ) {
      $params['order'] = 'creation_date DESC';
    }

    if( $unionselect ) $unionselect->order($params['order']);

    return $unionselect;
  }
}

