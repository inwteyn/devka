<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2010-09-20 17:53 idris $
 * @author     Idris
 */
?>

<?php if( $this->videos->getTotalItemCount() > 0 ): ?>

<script type="text/javascript">
page_video.files = <?php echo Zend_Json_Encoder::encode($this->files); ?>
</script>
 
<ul class='videos_manage'>
  <?php foreach( $this->videos as $item ): ?>
  <li>
    <div class="video_thumb_wrapper">
      <?php if ($item->duration):?>
      <span class="video_length">
        <?php
          if( $item->duration>360 ) $duration = gmdate("H:i:s", $item->duration); else $duration = gmdate("i:s", $item->duration);
            if ($duration[0] =='0') $duration = substr($duration,1); echo $duration;
          ?>
      </span>
      <?php endif;?>
      <?php
        if ($item->photo_id) echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('onclick' => "page_video.view({$item->getIdentity()}); return false;"));
          else echo '<img alt="" src="application/modules/Pagevideo/externals/images/video.png">';
        ?>
    </div>
    <div class='video_options'>
        <?php echo $this->htmlLink("javascript:page_video.edit({$item->getIdentity()})", $this->translate('Edit Video'), array(
            'class' => 'buttonlink icon_pagevideo_edit'
          )) 
        ?>
        <?php
        if ($item->status !=2){
            echo $this->htmlLink("javascript:page_video.confirm({$item->getIdentity()})", $this->translate('Delete Video'), array(
              'class' => 'buttonlink icon_pagevideo_delete'
            ));
          }
          ?>
    </div>
    <div class="video_info">
      <h3>
      <?php if ($item->status == 1): ?>
        <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('onclick' => "page_video.view({$item->getIdentity()}); return false;")); ?>
      <?php else: ?>
        <?php echo $item->getTitle(); ?>
      <?php endif; ?>
      </h3>
      <div class="video_desc">
        <?php echo Engine_String::substr(Engine_String::strip_tags($item->description), 0, 350); if (Engine_String::strlen($item->description)>349) echo "...";?>
      </div>
      <div class="video_stats">
        <span class="video_views"><?php echo $this->translate('Added');?> <?php echo $this->timestamp(strtotime($item->creation_date)) ?> - <?php echo $this->translate(array('%s comment', '%s comments', $item->comments()->getCommentCount()), $this->locale()->toNumber($item->comments()->getCommentCount())) ?> - <?php echo $this->translate(array('%s like', '%s likes', $item->likes()->getLikeCount()),$this->locale()->toNumber($item->likes()->getLikeCount())) ?> - <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?></span>
        <span class="video_star"></span><span class="video_star"></span><span class="video_star"></span><span class="video_star"></span><span class="video_star_half"></span>
      </div>
      <?php if($item->status == 0):?>
        <div class="tip">
          <span>
            <?php echo $this->translate('Your video is in queue to be processed - you will be notified when it is ready to be viewed.')?>
          </span>
        </div>
      <?php elseif($item->status == 2):?>
        <div class="tip">
          <span>
            <?php echo $this->translate('Your video is currently being processed - you will be notified when it is ready to be viewed.')?>
          </span>
        </div>
      <?php elseif($item->status == 3):?>
        <div class="tip">
          <span>
           <?php echo $this->translate('Video conversion failed. Please try %1$suploading again%2$s.', '<a href="'.$this->url(array('action' => 'create', 'type'=>3)).'">', '</a>'); ?>
          </span>
        </div>
      <?php elseif($item->status == 4):?>
        <div class="tip">
          <span>
           <?php echo $this->translate('Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create', 'type'=>3)).'">', '</a>'); ?>
          </span>
        </div>
       <?php elseif($item->status == 5):?>
        <div class="tip">
          <span>
           <?php echo $this->translate('Video conversion failed. Audio files are not supported. Please try %1$sagain%2$s.', '<a href="'.$this->url(array('action' => 'create', 'type'=>3)).'">', '</a>'); ?>
          </span>
        </div>
       <?php elseif($item->status == 7):?>
        <div class="tip">
          <span>
           <?php echo $this->translate('PAGE_CREATION_FAILED_DESC', '<a href="'.$this->url(array('action' => 'create', 'type'=>3)).'">', '</a>'); ?>
          </span>
        </div>
      <?php endif;?>
    </div>
  </li>
  <?php endforeach; ?>
</ul>
<?php else:?>
  <div class="tip">
   <span>
    <?php echo $this->translate('You do not have any videos.');?>
    <?php if ($this->can_create): ?>
      <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a href="javascript:page_video.create()">', '</a>'); ?>
    <?php endif; ?>
    </span>
  </div>

<?php endif; ?>
<br />
<?php echo $this->paginationControl($this->videos, null, array('pagination.tpl', 'pagevideo'), array(
  'page' => $this->pageObject
)); ?>