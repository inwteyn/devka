<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Hebadge_Plugin_Core
{

  public function onUserLoginBefore($event)
  {
    $item = $event->getPayload();

    $info = Engine_Api::_()->getDbTable('info', 'hebadge')->getInfo($item);
    if (!$info) {
      return;
    }

    $info->login++;
    $info->save();


    $item = $event->getPayload();
    if (empty($item)) {
      return;
    }

    $api = Engine_Api::_()->hebadge()->getRequireClass('login');
    if ($api) {
      $api->check($item);
    }


  }

  public function onItemCreateAfter($event)
  {

    try {

      $item = $event->getPayload();
      $viewer = Engine_Api::_()->user()->getViewer();

      if (empty($item) || !$viewer->getIdentity()) {
        return;
      }

      if ($item instanceof Core_Model_Item_Abstract) {

        if ($item->getType() == 'activity_notification') {

          // friend membership
          if (in_array($item->type, array('friend_follow', 'friend_accepted', 'friend_follow_request', 'friend_request'))) {

            $subject = Engine_Api::_()->getItem('user', $item->subject_id);
            $object = Engine_Api::_()->getItem('user', $item->object_id);

            if ($subject) {
              $api = Engine_Api::_()->hebadge()->getRequireClass('friend');
              if ($api) {
                $api->check($subject, $object->getIdentity());
              }
            }
            if ($object) {
              $api = Engine_Api::_()->hebadge()->getRequireClass('friend');
              if ($api) {
                $api->check($object, $subject->getIdentity());
              }
            }
          }

        } else if ($item->getType() == 'activity_action') {

          if (in_array($item->type, array('status', 'post', 'post_self')) && $item->subject_type == 'user') {

            $subject = Engine_Api::_()->getItem('user', $item->subject_id);

            // status
            $api = Engine_Api::_()->hebadge()->getRequireClass('status');
            if ($api) {
              $api->check($subject, $item->getIdentity());
            }

            // checkin
            $api = Engine_Api::_()->hebadge()->getRequireClass('checkin');
            if ($api) {
              $api->check($subject);
            }


          }

          // blog
        } else if ($item->getType() == 'blog') {
          $api = Engine_Api::_()->hebadge()->getRequireClass('blog');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          // photo
        } else if ($item->getType() == 'album_photo') {
          $api = Engine_Api::_()->hebadge()->getRequireClass('photo');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          // classified
        } else if ($item->getType() == 'classified') {
          $api = Engine_Api::_()->hebadge()->getRequireClass('classified');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          // comment
        } else if ($item->getType() == 'core_comment' || $item->getType() == 'activity_comment') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('comment');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

        } else if ($item->getType() == 'core_like') {

          // like
          $api = Engine_Api::_()->hebadge()->getRequireClass('like');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          // like me
          $api = Engine_Api::_()->hebadge()->getRequireClass('likeme');
          if ($api) {
            $api->check($viewer);
          }

          if ($item->resource_type == 'user') {

            $subject = Engine_Api::_()->getItem($item->resource_type, $item->resource_id);

            // like
            $api = Engine_Api::_()->hebadge()->getRequireClass('like');
            if ($api) {
              $api->check($subject);
            }

            // like me
            $api = Engine_Api::_()->hebadge()->getRequireClass('likeme');
            if ($api) {
              $api->check($subject);
            }

          }


          // music
        } else if ($item->getType() == 'music_playlist_song') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('music');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          // poll
        } else if ($item->getType() == 'poll') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('poll');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          $api = Engine_Api::_()->hebadge()->getRequireClass('pollpassed');
          if ($api) {
            $api->check($viewer);
          }

          // quiz
        } else if ($item->getType() == 'quiz' || $item->getType() == 'quiz_result') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('quiz');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          $api = Engine_Api::_()->hebadge()->getRequireClass('quizpassed');
          if ($api) {
            $api->check($viewer);
          }

          // rate
        } else if ($item->getType() == 'rate') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('rate');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }


          // review
        } else if ($item->getType() == 'pagereview') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('review');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          // store
        } else if ($item->getType() == 'store_product') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('store');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          // suggest
        } else if ($item->getType() == 'suggest') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('suggest');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          // video
        } else if ($item->getType() == 'video') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('video');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

          // forum
        } else if ($item->getType() == 'forum_post') {

          $api = Engine_Api::_()->hebadge()->getRequireClass('forum');
          if ($api) {
            $api->check($viewer, $item->getIdentity());
          }

        }


        // event
        if ($item->getType() == 'activity_action') {
          if (in_array($item->type, array('event_join')) && $item->subject_type == 'user') {
            $subject = Engine_Api::_()->getItem($item->subject_type, $item->subject_id);
            if ($subject) {
              $api = Engine_Api::_()->hebadge()->getRequireClass('event');
              if ($api) {
                $api->check($subject);
              }
            }
          }
        }
        if ($item->getType() == 'activity_notification') {
          if (in_array($item->type, array('event_accepted', 'event_approve', 'event_invite')) && $item->subject_type == 'user') {
            $subject = Engine_Api::_()->getItem($item->subject_type, $item->subject_id);
            if ($subject) {
              $api = Engine_Api::_()->hebadge()->getRequireClass('event');
              if ($api) {
                $api->check($subject);
              }
            }
          }
        }


        // group
        if ($item->getType() == 'activity_action') {
          if (in_array($item->type, array('group_join')) && $item->subject_type == 'user') {
            $subject = Engine_Api::_()->getItem($item->subject_type, $item->subject_id);
            if ($subject) {
              $api = Engine_Api::_()->hebadge()->getRequireClass('group');
              if ($api) {
                $api->check($subject);
              }
            }
          }
        }
        if ($item->getType() == 'activity_notification') {
          if (in_array($item->type, array('group_accepted', 'group_approve', 'group_invite')) && $item->subject_type == 'user') {
            $subject = Engine_Api::_()->getItem($item->subject_type, $item->subject_id);
            if ($subject) {
              $api = Engine_Api::_()->hebadge()->getRequireClass('group');
              if ($api) {
                $api->check($subject);
              }
            }
          }
        }


      }

    } catch (Exception $e) {
      //die( $e->__toString() );
      throw $e;
    }


  }

  public function onInviterSendInvite()
  {
    try {
      $api = Engine_Api::_()->hebadge()->getRequireClass('invite');
      if ($api) {
        $api->check(Engine_Api::_()->user()->getViewer());
      }
    } catch (Exception $e) {
      //die( $e->__toString() );
      throw $e;
    }

  }

  public function onCreditBalanceCreateAfter()
  {
    try {
      Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->checkOwnerRank(Engine_Api::_()->user()->getViewer());
    } catch (Exception $e) {
      //die( $e->__toString() );
      throw $e;
    }
  }

  public function onCreditBalanceUpdateAfter()
  {
    try {
      Engine_Api::_()->getDbTable('creditbadges', 'hebadge')->checkOwnerRank(Engine_Api::_()->user()->getViewer());
    } catch (Exception $e) {
      //die( $e->__toString() );
      throw $e;
    }
  }

  public function onItemDeleteBefore($event)
  {
    $item = $event->getPayload();
  }

  public function onUserDeleteBefore($event)
  {
    $user = $event->getPayload();

    if ($user instanceof User_Model_User) {
      foreach (Engine_Api::_()->getDbTable('complete', 'hebadge')->fetchAll(array('object_type = ?' => $user->getType(), 'object_id = ?' => $user->getIdentity())) as $item) {
        $item->delete();
      }
      foreach (Engine_Api::_()->getDbTable('members', 'hebadge')->fetchAll(array('object_type = ?' => $user->getType(), 'object_id = ?' => $user->getIdentity())) as $item) {
        $item->delete();
      }
      foreach (Engine_Api::_()->getDbTable('info', 'hebadge')->fetchAll(array('user_id = ?' => $user->getType())) as $item) {
        $item->delete();
      }
    }

  }

  public function onInviterRefered($event)
  {
    $invite = $event->getPayload();
    if (is_null($invite) || !is_object($invite)) {
      return;
    }

    $user = Engine_Api::_()->getItem('user', $invite->user_id);

    $info = Engine_Api::_()->getDbTable('info', 'hebadge')->getInfo($user);
    if (!$info) {
      return;
    }
    $info->referral++;
    $info->save();
  }

}