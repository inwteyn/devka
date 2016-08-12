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

<?php echo $this->paginationControl($this->paginator, null, array('pagination/page_filter.tpl', 'touch'),
array(
'search'=>$this->form_value,
'filter_default_value'=>$this->translate('TOUCH_Search Videos'),
'filterUrl'=>$this->url(array('module'=>'pagevideo', 'controller'=>'index', 'action'=>'index', 'page_id'=>$this->subject->page_id), 'page_video', true),
)
); ?>

<?php if ($this->paginator->getTotalItemCount() > 0){ ?>
<div id="filter_block">
<ul class="items">

    <div style="height: 8px;"></div>
    <?php foreach( $this->paginator as $item ): ?>
    <li>
        <div class="item_photo">
            <a href="<?php echo $this->url(array('action' => 'view', 'page_id' => $item->page_id, 'video_id' => $item->getIdentity()), 'page_video', true)?>" onclick='Touch.navigation.subNavRequest($(this)); return false;'>
                <?php echo $this->itemPhoto($item, 'thumb.normal'); ?>
            </a>
        </div>
        <div class="item_body">
            <div class="page-misc-date">
                <?php echo $this->translate("Posted %s", $this->timestamp($item->creation_date)); ?>
            </div>

            <a href="<?php echo $this->url(array('action' => 'view', 'page_id' => $item->page_id, 'video_id' => $item->getIdentity()), 'page_video', true); ?>" class="video_title" onclick='Touch.navigation.subNavRequest($(this)); return false;'>
                <?php echo $item->getTitle();?>
            </a>

            <div class="video_author">

                <?php echo $this->translate('By') ?>
                <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' =>
                'touchajax')) ?>

                <?php if( $item->duration ): ?>
                <?php
                if( $item->duration > 360 ) $duration = gmdate("H:i:s", $item->duration); else $duration = gmdate("i:s",
                $item->duration);
                if( $duration[0] == '0' ) $duration = substr($duration,1);
                echo ' - ' . $duration;
                ?>
                <?php endif ?>
            </div>


        </div>
    </li>
    <?php endforeach; ?>
    <div class="clr"></div>
</ul>
</div>
<?php }else{ ?>

<div class="tip">
  <span>
    <?php echo $this->translate('You do not have any videos.');?>
    <?php if ($this->isAllowedPost) {?>
      <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a href="'.$this->url(array('action' => 'create')).'" onclick="Touch.navigation.subNavRequest($(this)); return false;">', '</a>'); ?>
    <?php } ?>
  </span>
</div>

<?php } ?>