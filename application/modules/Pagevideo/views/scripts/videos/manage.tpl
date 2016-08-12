<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2010-09-20 17:53 Ulan T $
 * @author     Ulan T
 */
?>

<div class="layout_right">
  <?php echo $this->form->render($this); ?>
</div>

<div class="layout_middle">
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

      <div class="options">
        <?php if($item['type'] == 'page') : ?>
        <?php echo $this->htmlLink($video->getHref(), '', array('class' => 'edit')); ?>
        <?php echo $this->htmlLink(array('route' => 'page_videos', 'action' => 'delete', 'pagevideo_id' => $video->pagevideo_id, 'format' => 'smoothbox'), '', array('class' => 'delete smoothbox')); ?>
        <?php else : ?>
        <?php echo $this->htmlLink(array(
          'route' => 'default',
          'module' => 'video',
          'controller' => 'index',
          'action' => 'edit',
          'video_id' => $video->video_id
        ), '', array('class' => 'edit')); ?>
        <?php echo $this->htmlLink(array(
          'route' => 'default',
          'module' => 'video',
          'controller' => 'index',
          'action' => 'delete',
          'video_id' => $video->video_id,
          'format' => 'smoothbox'
        ), '', array('class' => 'delete smoothbox')); ?>
        <?php endif; ?>
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
      <?php echo $this->translate('You don\'t have any videos.');?>
    </span>
</div>
<?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues,
)); ?>
</div>