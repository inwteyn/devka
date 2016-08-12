<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-20 17:53 idris $
 * @author     Idris
 */
?>

<?php if( $this->videos->getTotalItemCount() > 0 ): ?>
  <div class="pagevideos_browse pagevideos_browse_index_<?php echo $this->theme_class; ?>">
    <?php foreach( $this->videos as $item ): ?>
      <div class="pagevideo">
        <div class="pagevideo_thumb_wrapper">
          <?php
            if ($item->photo_id) echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
              else echo $this->htmlLink($item->getHref(), '<img alt="" src="application/modules/Pagevideo/externals/images/video2.png" class="thumb_normal item_photo_pagevideo  thumb_normal">');
            ?>
          <?php echo $this->htmlImage($this->baseUrl() . "/application/modules/Pagevideo/externals/images/videoframe_{$this->theme_class}.png", '',
            array('class' => 'pagevideo_frame', 'onClick' => 'page_video.view('.$item->getIdentity().'); return false;')); ?>
        </div>
      </div>
    <?php endforeach; ?>
    <div class="clr"></div>
  </div>

  <br />
  <?php echo $this->paginationControl($this->videos, null, array('pagination.tpl', 'pagevideo'), array(
    'page' => $this->pageObject
  )); ?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a video yet.');?>
      <?php if ($this->isAllowedPost) : ?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="javascript:page_video.create()">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>