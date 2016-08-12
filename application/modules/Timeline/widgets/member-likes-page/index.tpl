<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>
<style>
    .like_list img.thumb_icon.item_photo_user{
        border-radius: 0px;
    }
    .like_list .thumb_normal.item_photo_user.thumb_normal{
        width: 100%;
    }
    .page_like_this_page{
        padding: 0;
    }
    .like_list  a.title_like_page{
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.19) 33%, rgba(0, 0, 0, 0.5) 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
        bottom: 0;
        color: rgb(255, 255, 255);
        opacity: 0;
        padding: 14px 9px 9px;
        position: absolute;
        text-shadow: 0 1px 3px rgba(64, 64, 64, 0.7);
        transition: opacity 0.7s ease 0s;
        width: 100%;
    }
    .page_like_link{
        background-position: center center;
        background-size: cover;
        display: block !important;
        padding-top: 100%;
    }
    .like_list  a.title_like_page:hover {
        transition: opacity 1s ease 0s;
        opacity: 1;
    }
    .like_list .like_hint_tip_links, .like_tool_tip_links, .like_profile_tip{
        float: none;
    }
</style>
<script type="text/javascript">
	en4.core.runonce.add(function(){

		var miniTipsOptions = {
			'htmlElement': '.like_tip_text',
			'delay': 1,
			'className': 'he-tip-mini',
			'id': 'he-mini-tool-tip-id',
			'ajax': false,
			'visibleOnHover': false
		};

		var $likesTips = new HETips($$('.like_tool_tip_links'), miniTipsOptions);
	});
</script>

<div class="he_like_cont page_like_this_page">
  <div class="like_list likes_all active_list">
    <?php $this->likes = $this->all_likes; ?>
    <?php $this->period_type = 'all'; ?>
      <?php if ($this->widget == 'most_liked') : ?>
          <?php  $total_items = count($this->like_items); ?>

          <?php if ($total_items == 0) : ?>

              <div class="he_like_no_content"><?php echo $this->translate('There are no content.'); ?></div>

          <?php else : ?>

              <div class="most-liked-<?php echo $this->item_type?>s">
                  <div class="list">
                      <?php foreach($this->counts as $item_id => $count):
                          if ( null != ($item = Engine_Api::_()->getItem($this->item_type, $item_id))): ?>
                              <div class="item">
                                  <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'most-liked-' . $this->item_type . ' display_block', 'id' => 'most_liked_' . $this->item_type . '_'.$item->getIdentity())); ?>
                                  <div title="<?php echo $this->translate(array('like_%s like', 'like_%s likes', $this->counts[$item->getIdentity()]), $this->counts[$item->getIdentity()]); ?>" class="like_count"><?php echo $count; ?></div>
                              </div>
                          <?php endif; ?>
                      <?php endforeach; ?>
                      <div class="clr"></div>
                  </div>
              </div>

          <?php endif; ?>
      <?php elseif ($this->widget == 'profile_likes') : ?>
          <?php
          $total_items = $this->total;
          ?>

          <?php if ($total_items == 0) : ?>
              <div class="he_like_no_content"><?php echo $this->translate('There are no content.'); ?></div>
          <?php else : ?>
<!--              <div class="see_all_container" style="margin-left: 12px;">
                  <?php
/*                  $label = $this->translate(array("like_%s item", "like_%s items", $this->total), ($this->total));
                  echo ($this->total && $this->likedMembersAndPages)
                      ? $this->htmlLink("javascript:like.see_all_liked({$this->subject->getIdentity()}, '$this->period_type');", $label)
                      : $this->htmlLink($this->url(array('action' => 'index', 'user_id' => $this->subject->getIdentity(), 'period_type' => $this->period_type), 'like_default'), $label, array('target' => '_blank'));
                  */?>
              </div>-->

              <?php
              $count = 0;
              $nophoto_items = array('blog', 'pageblog', 'poll', 'classified');
              ?>
              <div class="clr"></div>
              <div class="list">
                  <?php if (!empty($this->items)): ?>
                      <?php $counter = 0; ?>
                      <?php foreach ($this->items as $like): ?>
                          <?php
                          if (!($like instanceof Core_Model_Item_Abstract)){
                              continue ;
                          }
                          if ($count >= $this->ipp) {
                              break;
                          }
                          $count++;
                          ?>
                          <div class="item">
                              <?php
                              if ( in_array( $like->getType(), $nophoto_items ) ){
                                  $photo = $this->htmlImage($this->baseUrl() . '/application/modules/Like/externals/images/nophoto/' . $like->getType() . '.png', '', array(
                                      'class' => 'thumb_icon item_photo_' . $like->getType()
                                  ));
                              }else{
                                  $photo = $this->itemPhoto($like, 'thumb.icon');
                              }
                              ?>
                              <?php echo $this->htmlLink($like->getHref(), $photo, array('class' => 'like_profile_tip')); ?>
                              <div class="like_tip_title hidden"></div>
                              <div class="like_tip_text hidden"><?php echo $like->getTitle(); ?></div>
                          </div>
                          <?php $counter++; ?>
                          <?php if ($counter % 3 == 0): ?><div class="clr"></div><?php endif; ?>
                      <?php endforeach; ?>
                  <?php endif; ?>
                  <div class="clr"></div>
              </div>

              <div class="clr" style="margin-bottom:10px;"></div>
          <?php endif; ?>


      <?php elseif ($this->widget == 'box') : ?>
          <?php
          $total_items = $this->likes->getTotalItemCount();
          ?>
          <?php if ($total_items == 0) : ?>
              <div class="he_like_no_content"><?php echo $this->translate('There are no content.'); ?></div>
          <?php else : ?>
              <div class="see_all_container" style="margin-left: 12px;">
                  <a href="javascript:like.see_all('<?php echo $this->subject->getType(); ?>', <?php echo $this->subject->getIdentity(); ?>, '<?php echo $this->period_type; ?>');">
                      <?php echo $this->translate(array("like_%s like", "like_%s likes", $this->likes->getTotalItemCount()), ($this->likes->getTotalItemCount())); ?>
                  </a>
              </div>

              <div class="clr"></div>
              <div class="list">
                  <?php $counter = 0; ?>
                  <?php foreach ($this->likes as $like):
                      $photoUrl = $like->getPhotoUrl('thumb.profile');
                      $photoUrl = $photoUrl ? $photoUrl : rtrim($this->baseUrl(), '/') . '/application/modules/User/externals/images/nophoto_user_thumb_profile.png';

                      ?>

                      <div class="item" style=" display: block;    float: left;    margin: 0.5%;    position: relative;    width: 32.333%;">
                          <a class="like_tool_tip_links page_like_link" href="<?php echo $like->getHref(); ?>"  style="display: block;background-image: url(<?php echo $photoUrl ?>);">

                          </a>
                          <div class="like_tip_title hidden"></div>
                          <a  href="<?php echo $like->getHref(); ?>"  class="like_tip_text title_like_page"><?php echo $like->getTitle(); ?></a>
                      </div>
                      <?php $counter++; ?>
                      <?php if ($counter % 3 == 0): ?><div class="clr"></div><?php endif; ?>
                  <?php endforeach; ?>
                  <div class="clr"></div>
              </div>
              <div class="clr" style="margin-bottom:10px;"></div>
          <?php endif; ?>
      <?php endif; ?>
  </div>

</div>