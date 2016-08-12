<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
?>

<div class="layout_right">
  <?php echo $this->form->render($this);?>
</div>

  <div class="layout_middle">
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
    <div class="events_options">
      <?php if( $this->viewer() && $event->isOwner($this->viewer())) : ?>
        <?php if( $item['type'] == 'page') : ?>
          <?php echo $this->htmlLink($event->getHref(), $this->translate('Edit Event'), array(
            'class' => 'buttonlink icon_pageevent_edit'
          )) ?>
          <?php echo $this->htmlLink(array('route' => 'page_events', 'action' => 'delete', 'pageevent_id' => $event->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Event'), array(
            'class' => 'buttonlink smoothbox icon_pageevent_delete'
          )); ?>
        <?php else : ?>
          <?php echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'edit', 'event_id' => $event->getIdentity()), $this->translate('Edit Event'), array(
            'class' => 'buttonlink icon_pageevent_edit'
          )) ?>
          <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'event', 'controller' => 'event', 'action' => 'delete', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Event'), array(
            'class' => 'buttonlink smoothbox icon_pageevent_delete'
          )); ?>
        <?php endif; ?>
      <?php elseif( $this->viewer() && $event->membership()->isMember($this->viewer()) && !$event->isOwner($this->viewer()) ): ?>
        <?php if( $item['type'] =='page' ) :  ?>
          <?php echo $this->htmlLink(array('route' => 'page_events', 'action' => 'leave', 'pageevent_id' => $event->getIdentity(), 'format' => 'smoothbox'), $this->translate('Leave Event'), array(
            'class' => 'buttonlink smoothbox icon_pageevent_leave'
          )) ?>
        <?php else : ?>
          <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller'=>'member', 'action' => 'leave', 'event_id' => $event->getIdentity()), $this->translate('Leave Event'), array(
            'class' => 'buttonlink smoothbox icon_pageevent_leave'
          )) ?>
        <?php endif; ?>
      <?php endif; ?>
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
        <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
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
  </div>