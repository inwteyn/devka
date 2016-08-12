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

<?php if( $this->videos->getTotalItemCount() > 0 ){ ?>
<div id="widget_content">
<div class="page_sub_navigation">
<ul class="touch_sub_navigation">
    <li>
        <a href="<?php echo $this->url(array('action' => 'index', 'page_id' => $this->subject()->page_id), 'page_video', true)?>"
            class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
            <?php echo $this->translate("Browse Videos"); ?>
        </a>
    </li>
    <li>

        <a href="<?php echo $this->url(array('action' => 'manage', 'page_id' => $this->subject()->page_id), 'page_video', true)?>"
            class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
            <?php echo $this->translate("My videos"); ?>
        </a>
    </li>
    <?php if ($this->isAllowedPost):?>
    <li>
        <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject()->page_id), 'page_video', true)?>"
            class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
            <?php echo $this->translate("Post Video"); ?>
        </a>
    </li>
      <?php endif; ?>
    </ul>
    </div>

  <div style="clear: both; height: 8px;"></div>

  <div id="sub_navigation_loading"  style="display: none;">
    <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
  </div>
<div id="sub_navigation_content" >
  <?php echo $this->paginationControl($this->videos, null, array('pagination/page_filter.tpl', 'touch'),
  array(
  'search'=>$this->form_filter->getElement('search')->getValue(),
  'filter_default_value'=>$this->translate('TOUCH_Search Videos'),
  'filterUrl'=>$this->url(array('module'=>'pagevideo', 'controller'=>'index', 'action'=>'index', 'page_id'=>$this->subject()->page_id), 'page_video', true),
)
); ?>

  <div id="filter_block">
    <ul class="items">
    <?php foreach( $this->videos as $item ) { ?>
    <li>
        <div class="item_photo">
            <a href="<?php echo $this->url(array('action' => 'view', 'page_id' => $item->page_id, 'video_id' => $item->getIdentity()), 'page_video', true)?>" onclick="Touch.navigation.subNavRequest($(this)); return false;">
                <?php echo $this->itemPhoto($item, 'thumb.normal'); ?>
            </a>
        </div>
        <div class="item_body">
            <div class="page-misc-date">
                <?php echo $this->translate("Posted %s", $this->timestamp($item->creation_date)); ?>
            </div>

            <a href="<?php echo $this->url(array('action' => 'view', 'page_id' => $item->page_id, 'video_id' => $item->getIdentity()), 'page_video', true); ?>" class="video_title " onclick="Touch.navigation.subNavRequest($(this)); return false;">
                <?php echo $item->getTitle();?>
            </a>

            <div class="video_author">

                <?php echo $this->translate('By') ?>
                <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' =>
                'touchajax')) ?>

                <?php if( $item->duration ){ ?>
                <?php
                if( $item->duration > 360 ) $duration = gmdate("H:i:s", $item->duration); else $duration = gmdate("i:s",
                $item->duration);
                if( $duration[0] == '0' ) $duration = substr($duration,1);
                echo ' - ' . $duration;
                ?>
                <?php } ?>
            </div>


        </div>
      </li>
    <?php } ?>
      </ul>
    </div>
</div>
<?php } else {?>
  <div id="sub_navigation_loading"  style="display: none;">
    <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
  </div>

  <div id="sub_navigation_content" >
<div class="tip">
  <span>
    <?php echo $this->translate('Nobody has created a video yet.');?>
      <?php if ($this->isAllowedPost) { ?>
        <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject()->page_id), 'page_video', true)?>" onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
          <?php echo $this->translate("Nobody has created a video yet."); ?>
        </a>
      <?php } ?>
  </span>
</div>
  </div>
<?php } ?>
</div>
