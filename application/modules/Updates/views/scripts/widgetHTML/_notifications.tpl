<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _notification.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?><div  style="padding:0px;background-color:#8197AB;padding:4px;margin-top:25px"><div style="padding: 3px;text-align: center; background-color:#8197AB;"><a href="<?php echo $this->url(array(), 'recent_activity', true); ?>" style="color: #ffffff; text-decoration: none;font-weight:bold"><?php echo $this->content['title'] ?></a></div><div style="background: #FFFFFF;"><table cellpadding="0" cellspacing="0" style="color:#555555;font-size: 11px;"><?php foreach( $this->items as $notification ):
    switch($notification->type):
    case 'commented':
    case 'commented_commented':
      $icon = '/application/modules/Activity/externals/images/activity/comment.png';
      break;
    case 'liked':
    case 'liked_commented':
      $icon = '/application/modules/Activity/externals/images/activity/like.png';
      break;
    case 'message_new':
      $icon = '/application/modules/Messages/externals/images/send.png';
      break;
    case 'friend_request':
      $icon = '/application/modules/User/externals/images/friends/request.png';
      break;
    default:
      $icon ='/application/modules/Updates/externals/images/notification.jpg';
      break;
  endswitch; ?><tr><td valign="top" style="padding:4px; border-bottom:1px solid #DDDDDD;"><img src="<?php echo $icon; ?>" border="0"/></td><td style="padding:4px;border-bottom:1px solid #DDDDDD;"><?php $not = str_replace( array('class="', 'href="'), array('class="msgLink ', 'style="text-decoration:none; color:'.$this->linkColor.'" href="'), $notification->__toString()); echo $not ?></td></tr><?php endforeach; ?></table></div></div>