<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>

<?php
  $this->item = Engine_Api::_()->getItem($this->item->object_type, $this->item->object_id);
?>

	<?php if ($this->step == 'thumb'): ?>

  <a href='<?php $this->item->getHref(); ?>' style="border:1px solid #DDDDDD;padding:4px;vertical-align:bottom;text-decoration:none;display:inline-block;width:48px;height:48px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.icon'); ?>
  </a>

	<?php elseif($this->step == 'details'): ?>

  	<a href="<?php echo $this->item->getHref() ?>" style="font-weight: bold; color:<?php echo $this->linkColor?>; font-size: 12px;text-decoration: none">
			<?php echo $this->item->getTitle(); ?>
		</a>

		<div style="font-size: 10px">
      <?php echo str_replace('href=', 'style="font-weight: bold; color:' . $this->linkColor . '; text-decoration: none" href=', $this->suggestDetails($this->item)); ?>
    </div>

    <div style="font-size: 11px; text-align: right; margin-top: 2px;">
      <span>
        <?php
          $type = $this->item->getType();
          $id = $this->item->getIdentity();
          $label = $this->translate("suggest_view_this_".$type);

          $icons = array(
            'suggest_view_page' => '/application/modules/Suggest/externals/images/types/page.png',
            'suggest_view_blog' => '/application/modules/Suggest/externals/images/types/blog.png',
            'suggest_view_classified' => '/application/modules/Suggest/externals/images/types/classified.png',
            'suggest_view_poll' => '/application/modules/Suggest/externals/images/types/poll.png',
            'suggest_view_video' => '/application/modules/Suggest/externals/images/types/video.png',
            'suggest_view_album' => '/application/modules/Suggest/externals/images/types/album.png',
            'suggest_view_photo' => '/application/modules/Suggest/externals/images/types/photo.png',
            'suggest_view_album_photo' => '/application/modules/Suggest/externals/images/types/photo.png',
            'suggest_view_quiz' => '/application/modules/Suggest/externals/images/types/quiz.png',
            'suggest_view_friend' => '/application/modules/Suggest/externals/images/suggest.png',
            'suggest_view_music_playlist' => '/application/modules/Suggest/externals/images/types/music.png',
            'suggest_view_article' => '/application/modules/Suggest/externals/images/types/article.png',
            'suggest_view_question' => '/application/modules/Suggest/externals/images/types/question.png',
            'icon_event_join' => '/application/modules/Event/externals/images/member/join.png',
            'icon_group_join' => '/application/modules/Group/externals/images/member/join.png',
            'icon_friend_add' => '/application/modules/User/externals/images/friends/add.png'
          );

          switch ($type) {
            case 'group':
            case 'event':
              $url = $this->url(array(
                  'controller' => 'member',
                  'action' => 'join',
                  $type.'_id' => $id
                ), $type.'_extended');

                $icon_src = $icons['icon_'.$type.'_join'];
                $params = array('class' => 'msgLink', 'style' => 'margin-left: 3px; font-weight: bold; color:' . $this->linkColor . '; text-decoration: none');
            break;
            case 'user':
              $url = $this->url(array(
                  'controller' => 'friends',
                  'action' => 'add',
                    'user_id' => $id
                ), 'user_extended');

              $icon_src = $icons['icon_friend_add'];
              $params = array('class' => 'msgLink', 'style' => 'margin-left: 3px; font-weight: bold; color:' . $this->linkColor . '; text-decoration: none');
            break;
            default:
              $url = $this->url(array(
                  'controller' => 'index',
                  'action' => 'accept-suggest',
                  'object_type' => $type,
                  'object_id' => $id,
                ), 'suggest_general');

              $icon_src = $icons['suggest_view_'.$type];
              $params = array('style' => 'margin-left: 3px; font-weight: bold; color:' . $this->linkColor . '; text-decoration: none');
            break;
          }

          echo $this->htmlImage($icon_src);
          echo $this->htmlLink($url, $label, $params);
        ?>
      </span>
    </div>
	<?php endif; ?>