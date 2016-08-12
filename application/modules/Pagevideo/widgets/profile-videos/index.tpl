<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){

  <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_pagevideos').getParent();
    $('profile_pagevideos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_pagevideos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_pagevideos_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('profile_pagevideos_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<div class='videos_manage' id="profile_pagevideos">
  <?php foreach( $this->paginator as $item ): ?>
  <?php
  if($item['type'] == 'page'){
    $video = Engine_Api::_()->getItem('pagevideo', $item['video_id']);
    $thumb = 'thumb.norm';
  }
  else{
    $video = Engine_Api::_()->getItem('video', $item['video_id']);
    $thumb = 'thumb.normal';
  }
  ?>
  <div class="pagevideo_browse_item">
    <div class="video_thumb_wrapper">
      <?php if ($video->duration):?>
      <span class="video_length">
        <?php
        if( $video->duration>360 ) $duration = gmdate("H:i:s", $video->duration); else $duration = gmdate("i:s", $video->duration);
        if ($duration[0] =='0') $duration = substr($duration,1); echo $duration;
        ?>
      </span>
      <?php endif;?>
      <?php
      if ($video->photo_id) echo $this->htmlLink($video->getHref(), $this->itemPhoto($video, $thumb));
      else echo '<img alt="" src="application/modules/Pagevideo/externals/images/video.png">';
      ?>
    </div>

    <div class="pagevideo_browse_info">
      <h5>
        <?php echo $this->htmlLink($video->getHref(), $video->getTitle()); ?>
      </h5>

      <div class="video_stats">
        <span class="video_views">
          <?php echo $this->translate('Added');?>
          <?php echo $this->timestamp(strtotime($video->creation_date)) ?>
          <br/>
          <?php echo $this->translate(array('%s comment', '%s comments', $video->comments()->getCommentCount()), $this->locale()->toNumber($video->comments()->getCommentCount())) ?>
          -
          <?php echo $this->translate(array('%s like', '%s likes', $video->likes()->getLikeCount()),$this->locale()->toNumber($video->likes()->getLikeCount())) ?>
          -
          <?php echo $this->translate(array('%s view', '%s views', $video->view_count), $this->locale()->toNumber($video->view_count)) ?>
          <?php if( $item['type'] == 'page') : ?>
          <br/>
          <?php echo $this->translate('On page ')?>
          <?php echo $this->htmlLink($video->getPage()->getHref(), $video->getPage()->getTitle()); ?>
          <?php endif; ?>
        </span>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<div>
  <br/>
</div>
<div>
  <div id="profile_pagevideos_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
    'onclick' => '',
    'class' => 'buttonlink icon_previous'
  )); ?>
  </div>
  <div id="profile_pagevideos_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
    'onclick' => '',
    'class' => 'buttonlink_right icon_next'
  )); ?>
  </div>
</div>