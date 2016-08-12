<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */
?>

<ul id="pageevents-upcoming">
  <?php foreach( $this->paginator as $item ):

  if( $item['type'] == 'page' )
   $event = Engine_Api::_()->getItem('pageevent', $item['event_id']);
  else
    $event = Engine_Api::_()->getItem('event', $item['event_id']);

  // Convert the dates for the viewer
  $startDateObject = new Zend_Date(strtotime($event->starttime));
  $endDateObject = new Zend_Date(strtotime($event->endtime));
  if( $this->viewer() && $this->viewer()->getIdentity() ) {
    $tz = $this->viewer()->timezone;
    $startDateObject->setTimezone($tz);
    $endDateObject->setTimezone($tz);
  }
  $isOngoing = ( $startDateObject->toValue() < time() );
  ?>
  <li<?php if( $isOngoing ):?> class="ongoing"<?php endif ?>>
    <?php echo $event->__toString() ?>
    <div class="pageevents-upcoming-date">
      <?php echo $this->timestamp($event->starttime, array('class'=>'eventtime')) ?>
    </div>
    <?php if( $isOngoing ): ?>
    <div class="pageevents-upcoming-ongoing">
      <?php echo $this->translate('Ongoing') ?>
    </div>
    <?php endif; ?>
  </li>
  <?php endforeach; ?>
</ul>