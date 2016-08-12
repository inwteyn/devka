<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */

?>

<?php if( count($this->paginator) > 0 ): ?>
<ul class='events_browse'>
  <?php foreach( $this->paginator as $item ): ?>
  <?php if( $item['type'] == 'event') : ?>
    <?php $event = Engine_Api::_()->getItem('event', $item['event_id']); ?>
  <?php else: ?>
    <?php $event = Engine_Api::_()->getItem('pageevent', $item['event_id'])?>
   <?php endif; ?>
    <li>
      <div class="events_photo">
        <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
      </div>

      <div class="events_info">
        <div class="events_title">
          <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
        </div>
        <div class="events_members">
          <?php echo $this->locale()->toDateTime($event->starttime) ?>
        </div>
        <div class="events_members">
          <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
          <?php echo $this->translate('led by') ?>
          <?php $owner = $event->getOwner();?>
          <?php if( $item['type'] == 'page' && (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $event->page_id)->getOwner() != $event->getOwner())) : ?>
            <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
          <?php elseif ($item['type'] == 'page'):?>
            <?php echo $this->htmlLink(Engine_Api::_()->getItem('page', $event->page_id)->getHref(), Engine_Api::_()->getItem('page', $event->page_id)->getTitle());?>
          <?php else:?>
            <?php echo $this->htmlLink($owner->getHref(), $owner->getTitle());?>
          <?php endif;?>
          <?php if( $item['type'] == 'page') : ?>
            <br/>
            <?php echo $this->translate('on page ');?>
            <?php echo $this->htmlLink($event->getPage()->getHref(), $event->getPage()->getTitle());?>
          <?php endif; ?>
        </div>
        <div class="events_desc">
          <?php echo $event->getDescription() ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php if( $this->paginator->count() > 1 ): ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
    )); ?>
  <?php endif; ?>

<?php else: ?>

<div class="tip">
    <span>
    <?php if( $this->filter != "past" ): ?>
      <?php echo $this->translate('Nobody has created an event yet.') ?>
      <?php else: ?>
      <?php echo $this->translate('There are no past events yet.') ?>
      <?php endif; ?>
    </span>
</div>

<?php endif; ?>
