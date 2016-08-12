<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: mine.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */


?>

<?php echo $this->paginationControl($this->videos, null, array('pagination/page_filter.tpl', 'touch'),
array(
'search'=>$this->form_value,
'filter_default_value'=>$this->translate('TOUCH_Search Albums'),
'filterUrl'=>$this->url(array('module'=>'pagevideo', 'controller'=>'index', 'action'=>'manage', 'page_id'=>$this->subject->page_id), 'page_video', true),
)
); ?>
	<div id="filter_block">

    <?php if( $this->paginator->getTotalItemCount() > 0 ) { ?>

        <ul class='items'>
        <?php foreach( $this->paginator as $item ) { ?>
            <li>
              <div class="item_photo">
                <a href="<?php echo $this->url(array('action' => 'view', 'page_id' => $item->page_id, 'video_id' => $item->getIdentity()), 'page_video', true)?>" onclick='Touch.navigation.subNavRequest($(this)); return false;'>
                <?php echo $this->itemPhoto($item, 'thumb.normal'); ?>
                </a>
              </div>
              <div class="item_body">
                <a href="<?php echo $this->url(array('action' => 'view', 'page_id' => $item->page_id, 'video_id' => $item->getIdentity()), 'page_video', true); ?>" class="video_title" onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
                  <?php echo $item->getTitle();?>
                </a>

              <div class="video_author">

              <?php echo $this->translate('By') ?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('onclick'=>'Touch.navigation.subNavRequest($(this)); return false;')) ?>

              <?php if( $item->duration ){ ?>
                <?php
                  if( $item->duration > 360 ) $duration = gmdate("H:i:s", $item->duration); else $duration = gmdate("i:s", $item->duration);
                  if( $duration[0] == '0' ) $duration = substr($duration,1);
                    echo ' - ' . $duration;
                ?>
              <?php } ?>
              </div>

              <div class="video_stats item_options">
                <span class="video_views">
              
                  <?php echo $this->htmlLink(array(
                    'route' => 'pagevideo_edit',
                    'video_id' => $item->getIdentity()
                   ), $this->translate('Edit Video'), array(
                      'class' => 'icon_video_edit touchajax'
                      )) ?>
            &nbsp;|&nbsp;

                <?php echo $this->htmlLink(array(
              'route' => 'pagevideo_delete',
              'controller' => 'index',
              'action' => 'delete',
              'video_id' => $item->getIdentity(),
            ), $this->translate('Delete Video'), array(
              'class' => 'smoothbox'
             )) ?>
              
          </span>
            </div>

          </div>
        </li>
        <?php }; ?>
      </ul>
</div>

    <?php } else {?>

      <div class="tip">
        <span>
          <?php echo $this->translate('You do not have any videos.');?>
          <?php if ($this->isAllowedPost) {?>
            <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a href="'.$this->url(array('action' => 'create')).'" onclick="Touch.navigation.subNavRequest($(this)); return false;">', '</a>'); ?>
          <?php } ?>
        </span>
      </div>

    <?php } ?>

