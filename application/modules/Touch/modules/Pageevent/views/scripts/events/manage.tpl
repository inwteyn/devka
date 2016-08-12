
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
					'search'=>$this->formFilter->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Events'),
          'filterUrl'=>$this->url(array('action' => 'manage'), 'event_general', true)
				)
		); ?>
	</div>

	<div id="filter_block">
	<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

    <ul class='items'>
      <?php foreach( $this->paginator as $item ): ?>
      <?php if( $item['type'] == 'event') : ?>
        <?php $event = Engine_Api::_()->getItem('event', $item['event_id']); ?>
      <?php else: ?>
        <?php $event = Engine_Api::_()->getItem('pageevent', $item['event_id'])?>
      <?php endif; ?>
        <li>
          <div class="item_photo">
            <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal'), array('class' => 'touchajax')) ?>
          </div>
          <div class="item_body">
            <div class="events_title">
              <?php echo $this->htmlLink($event->getHref(), $this->touchSubstr($event->getTitle()), array('class' => 'touchajax'))?>
            </div>
            <div class="item_date">
              <?php echo $this->locale()->toDateTime($event->starttime) ?>
              <div class="events_members">
                <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
                <?php echo $this->translate('led by') ?>
                <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
                <?php if( $item['type'] == 'page') : ?>
                <br/>
                <?php echo $this->translate('on page ');?>
                <?php echo $this->htmlLink($event->getPage()->getHref(), $event->getPage()->getTitle());?>
                <?php endif; ?>
              </div>
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
      </span>
    </div>

	<?php endif; ?>
	</div>
</div>

