<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2011-04-26 11:18:13 mirlan $
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
					'filterUrl'=>$this->url(array('action' => 'manage'), 'video_general', true),
				)
		); ?>
	</div>

	<div id="filter_block">

    <?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
      <div class="tip">
        <span>
          <?php echo $this->translate('You have already created the maximum number of videos allowed. If you would like to post a new video, please delete an old one first.');?>
        </span>
      </div>
      <br/>
    <?php endif; ?>

    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

      <ul class='items'>
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

            - <?php echo $this->htmlLink(array(
              'route' => 'default',
              'module' => 'video',
              'controller' => 'index',
              'action' => 'edit',
              'video_id' => $item->video_id
            ), $this->translate('Edit'), array(
              'class' => 'touchajax'
            )); ?>
            <?php
            if ($item->status !=2){
              echo ' - ' . $this->htmlLink(array('route' => 'default', 'module' => 'video', 'controller' => 'index', 'action' => 'delete', 'video_id' => $item->video_id), $this->translate('Delete'), array(
                'class' => 'smoothbox'
              ));
            }
            ?>

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

    <?php else:?>

      <div class="tip">
        <span>
          <?php echo $this->translate('You do not have any videos.');?>
          <?php if ($this->can_create): ?>
            <?php echo $this->translate('Get started by %1$sposting%2$s a new video.', '<a href="'.$this->url(array('action' => 'create')).'" class="touchajax">', '</a>'); ?>
          <?php endif; ?>
        </span>
      </div>

    <?php endif; ?>

  </div>
</div>
