<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: browse.tpl 2012-02-28 17:53 Ulan T $
 * @author     Ulan T
 */
?>


<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

<div class='videos_manage'>
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
    <div class="pagevideo_image">
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
          <br/>
          <?php echo $this->translate('By '); ?>
          <?php echo $this->htmlLink($video->getOwner()->getHref(), $video->getOwner()->getTitle()); ?>
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

<?php elseif( $this->category ):?>
<div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted a video with that criteria.');?>
    </span>
</div>
<?php else:?>
<div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a video yet.');?>
    </span>
</div>
<?php endif; ?>
<?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
)); ?>