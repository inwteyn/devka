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
					'search'=>$this->formFilter->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Events'),
					'filterUrl'=>$this->url(array('action' => 'manage'), 'event_general', true)
				)
		); ?>
	</div>

	<div id="filter_block">
	<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>


    <ul class='items'>
      <?php foreach( $this->paginator as $event ): ?>
        <li>
          <div class="item_photo">
            <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal'), array('class' => 'touchajax')) ?>
          </div>
          <div class="item_body">
            <div class="item_title">
              <?php echo $this->htmlLink($event->getHref(), $this->touchSubstr($event->getTitle()), array('class' => 'touchajax'))?>
            </div>
            <div class="item_date">
              <?php echo $this->locale()->toDateTime($event->starttime) ?>
              <?php if( $this->viewer() && $event->isOwner($this->viewer()) ): ?>
               - <?php echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'edit', 'event_id' => $event->getIdentity()), $this->translate('Edit'), array('class' => 'touchajax')) ?>
               - <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'event', 'controller' => 'event', 'action' => 'delete', 'event_id' => $event->getIdentity()), $this->translate('Delete'), array('class' => 'smoothbox'))?>
              <?php endif;?>
            </div>
          </div>
          <?php echo $this->touchEventRate('event', $event->getIdentity())?>
        </li>
      <?php endforeach; ?>
    </ul>

	<?php else: ?>
  <div class="tip">
    <span>
        <?php echo $this->translate('You have not joined any events yet.') ?>
        <?php if( $this->canCreate): ?>
          <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
            '<a href="'.$this->url(array('action' => 'create'), 'event_general').'" class="touchajax">', '</a>') ?>
        <?php endif; ?>
    </span>
  </div>
	<?php endif; ?>

	</div>
</div>
