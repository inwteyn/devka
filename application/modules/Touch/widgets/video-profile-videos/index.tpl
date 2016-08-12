<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */



$urlParams = array(
  'module' => 'core',
  'controller' => 'widget',
  'action' => 'index',
  'content_id' => $this->identity,
  'subject' => $this->subject()->getGuid(),
  'format' => 'html'
);

?>

<div id="widget_content">

	<div class="search">

		<?php echo $this->paginationControl($this->paginator, null,
				array('pagination/filter.tpl', 'touch'),
				array(
					'search'=>$this->form->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Videos'),
					'filterUrl'=> $this->url($urlParams, 'default', true),
          'filterOptions' => array(
            'replace_content' => 'widget_content',
            'noChangeHash' => 1,
          ),
          'pageUrlParams' => $urlParams
        )
		); ?>
  </div>

  <div id="filter_block">

    <?php if ($this->paginator->getTotalItemCount()):?>

      <ul class="items">
        <?php foreach( $this->paginator as $item ): ?>
          <li>

            <div class="item_photo">
              <?php
                if( $item->photo_id ) echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('class' => 'touchajax'));
                else echo '<img alt="" src="application/modules/Video/externals/images/video.png" class="thumb_normal">';
              ?>
            </div>
            <div class="item_body">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'video_title touchajax')) ?>

              <div class="video_author">

               <?php echo $this->translate('By') ?>
                <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' => 'touchajax')) ?>

                <?php if( $item->duration ): ?>
                <?php
                    if( $item->duration > 360 ) $duration = gmdate("H:i:s", $item->duration); else $duration = gmdate("i:s", $item->duration);
                if( $duration[0] == '0' ) $duration = substr($duration,1);
                echo ' - ' . $duration;
                ?>
                <?php endif ?>
              </div>


            <div class="video_stats">
              <span class="video_views">
                <?php echo $this->translate(array('%1$s view', '%1$s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
              </span>
              <?php if( $item->rating > 0 ): ?>
                <?php for( $x=1; $x<=$item->rating; $x++ ): ?>
                  <span class="rating_star_generic rating_star"></span>
                <?php endfor; ?>
                <?php if( (round($item->rating) - $item->rating) > 0): ?>
                  <span class="rating_star_generic rating_star_half"></span>
                <?php endif; ?>
              <?php endif; ?>
            </div>

            </div>

          </li>
        <?php endforeach; ?>
      </ul>

    <?php else :?>

      <div class="tip">
        <span><?php echo $this->translate('TOUCH_WIDGET_NOITEMS')?></span>
      </div>

    <?php endif;?>
  </div>
</div>
