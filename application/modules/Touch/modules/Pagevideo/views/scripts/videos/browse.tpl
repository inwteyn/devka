
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: browse.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */


?>
<?php if( count($this->navigation) > 0 ): ?>
	<?php
		// Render the menu
		echo $this->navigation()
			->menu()
			->setContainer($this->navigation)
			->setPartial(array('navigation/index.tpl', 'touch'))
			->render();
	?>
<?php endif; ?>

<div id="navigation_content">
	<div	class="search">
		<?php echo $this->paginationControl(
				$this->paginator,
				null,
				array('pagination/filter.tpl', 'touch'),
				array(
					'search'=>$this->form->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Videos'),
					'filterUrl'=>$this->url(array(), 'video_general', true),
				)
		); ?>
	</div>

	<div id="filter_block">

  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <ul class="items">
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
      <li>
        <div class="item_photo">
          <?php
            if( $video->photo_id ) echo $this->htmlLink($video->getHref(), $this->itemPhoto($video, $thumb), array('class' => 'touchajax'));
            else echo '<img alt="" src="application/modules/Video/externals/images/video.png" class="thumb_normal">';
          ?>
        </div>
        <div class="item_body">
          <?php echo $this->htmlLink($video->getHref(), $video->getTitle(), array('class' => 'video_title touchajax')) ?>

          <div class="video_author">

           <?php echo $this->translate('By') ?>
            <?php echo $this->htmlLink($video->getOwner()->getHref(), $video->getOwner()->getTitle(), array('class' => 'touchajax')) ?>
            <?php if( $item['type'] == 'page') : ?>
            <br/>
            <?php echo $this->translate('On page ')?>
            <?php echo $this->htmlLink($video->getPage()->getHref(), $video->getPage()->getTitle()); ?>
            <?php endif; ?>

            <?php if( $video->duration ): ?>
            <?php
                if( $video->duration > 360 ) $duration = gmdate("H:i:s", $video->duration); else $duration = gmdate("i:s", $video->duration);
            if( $duration[0] == '0' ) $duration = substr($duration,1);
            echo ' - ' . $duration;
            ?>
            <?php endif ?>
          </div>


        <div class="video_stats">
          <span class="video_views">
            <?php echo $this->translate(array('%1$s view', '%1$s views', $video->view_count), $this->locale()->toNumber($video->view_count)) ?>
          </span>
    <?php if($item['type'] == 'video'){ ?>
          <?php if( $video->rating > 0 ): ?>
            <?php for( $x=1; $x<=$video->rating; $x++ ): ?>
              <span class="rating_star_generic rating_star"></span>
            <?php endfor; ?>
            <?php if( (round($video->rating) - $video->rating) > 0): ?>
              <span class="rating_star_generic rating_star_half"></span>
            <?php endif; ?>
          <?php endif;
    }?>
        </div>

        </div>

      </li>
    <?php endforeach; ?>
  </ul>
  <?php elseif( $this->search ):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted a video with that criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has created a video yet.');?>
        <?php if ($this->can_create):?>
          <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), "video_general").'" class="touchajax">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

  </div>
</div>
