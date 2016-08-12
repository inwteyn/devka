
<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Pageevent
* @copyright  Copyright Hire-Experts LLC
* @license    http://www.hire-experts.com
* @version    $Id: index.tpl 2010-07-02 17:53 michael $
* @author     Michael
*/

?>

<div class="pageevent_calendar">

  <?php foreach ($this->paginator as $event):?>

    <?php
      $startDateObject = new Zend_Date(strtotime($event->starttime));
      if ($this->viewer->getIdentity()){
        $startDateObject->setTimezone($this->viewer->timezone);
      }
    ?>
    <div class="item">
      <div class="calendar">
        <div class="container">
          <div class="month"><?php echo $startDateObject->toString(Zend_Date::MONTH_NAME_SHORT);?></div>
          <div class="day"><?php echo $startDateObject->toString(Zend_Date::DAY);?></div>
        </div>
      </div>
      <div class="event_info">
        <div class="title"><a href="<?php echo $event->getHref(); ?>" onclick="Pageevent.loadView(<?php echo $event->getIdentity()?>); return false;"><?php echo $event->getTitle()?></a></div>
        <?php if (!empty($event->location)):?><div class="where"><?php echo $event->location;?></div><?php endif;?>
        <div class="show"><a href="<?php echo $event->getHref(); ?>" onclick="Pageevent.loadView(<?php echo $event->getIdentity()?>); return false;"><?php echo $this->translate('PAGEVENT_SHOW')?></a></div>
      </div>
      <div class="clr"></div>
    </div>

  <?php endforeach;?>

  <div class="clr"></div>

</div>