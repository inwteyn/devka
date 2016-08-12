
<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

?>

<div class="pageevent_event">

  <div class="content">

    <div class="header">

      <span><?php echo $this->translate('PAGEVENT_ONWER', $this->subject->getOwner()->__toString())?></span>

      <div class="options">

        <?php if ($this->member && !$this->member->resource_approved):?>

          <div class="item">
            <span><?php echo $this->translate('PAGEEVENT_MEMBER_WAITING')?></span>
          </div>
          <div class="item">
            <button class="<?php if ($this->member && $this->member->rsvp == 2):?>active<?php endif;?>" type="submit" onclick="Pageevent.memberApprove(<?php echo $this->event_id?>, 0);"><?php echo $this->translate('PAGEEVENT_CANCEL')?></button>
          </div>

        <?php elseif ($this->isLogin):?>

          <?php if ($this->subject->approval && !$this->member):?>

            <div class="item">
              <button class="<?php if ($this->member && $this->member->rsvp == 0):?>active<?php endif;?>" type="submit" onclick="Pageevent.rsvp(<?php echo $this->event_id?>, 0);"><?php echo $this->translate('PAGEEVENT_REQUEST_INVITE')?></button>
            </div>

          <?php else:?>

             <div class="item">
             <?php if ($this->member && $this->member->rsvp == 2):?>
              <button class="active" type="submit" onclick="Pageevent.rsvp(<?php echo $this->event_id?>, 2);"><?php echo $this->translate('PAGEEVENT_ATTENDING')?></button>
             <?php else: ?>
              <a href="javascript://" onclick="Pageevent.rsvp(<?php echo $this->event_id?>, 2);" class="pageevent_rsp_status"><?php echo $this->translate('PAGEEVENT_ATTENDING')?></a>
             <?php endif;?>
            </div>
            <div class="item">
            <?php if ($this->member && $this->member->rsvp == 1):?>
              <button class="active" type="submit" onclick="Pageevent.rsvp(<?php echo $this->event_id?>, 1);"><?php echo $this->translate('PAGEEVENT_MAYBEATTENDING')?></button>
            <?php else: ?>
              <a href="javascript://" onclick="Pageevent.rsvp(<?php echo $this->event_id?>, 1);" class="pageevent_rsp_status"><?php echo $this->translate('PAGEEVENT_MAYBEATTENDING')?></a>
            <?php endif;?>
            </div>
            <div class="item">
            <?php if ($this->member && $this->member->rsvp == 0):?>
              <button class="active" type="submit" onclick="Pageevent.rsvp(<?php echo $this->event_id?>, 0);"><?php echo $this->translate('PAGEEVENT_NOTATTENDING')?></button>
            <?php else: ?>
              <a href="javascript://" onclick="Pageevent.rsvp(<?php echo $this->event_id?>, 0);" class="pageevent_rsp_status"><?php echo $this->translate('PAGEEVENT_NOTATTENDING')?></a>
            <?php endif;?>
            </div>

          <?php endif;?>

        <?php endif;?>

        <div class="clr"></div>
      </div>

      <div class="clr"></div>

    </div>

    <div class="title"><?php echo $this->subject->title?></div>

    <div class="event_info">

      <div class="item">
        <div class="label"><?php echo $this->translate('Posted')?></div>
        <div class="value"><?php echo $this->timestamp($this->subject->creation_date)?></div>
        <div class="clr"></div>
      </div>

      <?php if ($this->subject->starttime == $this->subject->endtime ): ?>

        <div class="item">
          <div class="label"><?php echo $this->translate('Date')?></div>
          <div class="value"><?php echo $this->locale()->toDate($this->startDateObject)?> <?php echo $this->locale()->toTime($this->startDateObject) ?></div>
          <div class="clr"></div>
        </div>

      <?php elseif( $this->startDateObject->toString('y-MM-dd') == $this->endDateObject->toString('y-MM-dd') ): ?>

        <div class="item">
          <div class="label"><?php echo $this->translate('Date')?></div>
          <div class="value"><?php echo $this->locale()->toDate($this->startDateObject) ?></div>
          <div class="clr"></div>
        </div>
        <div class="item">
          <div class="label"><?php echo $this->translate('Time')?></div>
          <div class="value">
            <?php echo $this->locale()->toTime($this->startDateObject)?> -
            <?php echo $this->locale()->toTime($this->endDateObject)?>
          </div>
          <div class="clr"></div>
        </div>

      <?php else: ?>

        <div class="item">
          <div class="label"><?php echo $this->translate('Date')?></div>
          <div class="value">
          <?php echo $this->translate('%1$s at %2$s', $this->locale()->toDate($this->startDateObject), $this->locale()->toTime($this->startDateObject))?> -
          <?php echo $this->translate('%1$s at %2$s', $this->locale()->toDate($this->endDateObject), $this->locale()->toTime($this->endDateObject))?>
          </div>
          <div class="clr"></div>
        </div>

      <?php endif;?>

      <?php if (!empty($this->subject->location)):?>

        <div class="item">
          <div class="label"><?php echo $this->translate('PAGEEVENT_WHERE')?></div>
          <div class="value">
            <?php echo $this->subject->location?>
            <?php echo $this->htmlLink('http://maps.google.com/?q='.urlencode($this->subject->location), $this->translate('PAGEEVENT_MAP'), array('target' => 'blank'))?>
          </div>
          <div class="clr"></div>
        </div>

      <?php endif;?>

    </div>

    <div class="description"><?php echo nl2br($this->subject->description) ?></div>

    <br />


<?php if (Engine_Api::_()->getDbTable('modules' ,'core')->isModuleEnabled('wall') && Engine_Api::_()->getDbTable('modules', 'hecore')->findByName('wall')): ?>
  <?php echo $this->wallComments($this->subject, $this->viewer()); ?>
<?php else: ?>
  <div class="comments" id="pageevent_comments"></div>
<?php endif;?>


  </div>

  <div class="sidebar">

    <div class="photo">
      <?php echo $this->itemPhoto($this->subject)?>
    </div>

    <div class="options" id="profile_options">
      <ul>
        <?php if ($this->isTeamMember || $this->isOwner):?>
          <li><a href="javascript:Pageevent.formEvent(<?php echo $this->event_id?>);" class="buttonlink edit"><?php echo $this->translate('PAGEEVENT_EDIT')?>---------</a></li>
          <li><a href="javascript:Pageevent.remove(<?php echo $this->event_id?>);" class="buttonlink remove"><?php echo $this->translate('PAGEEVENT_DELETE')?></a></li>
        <?php endif;?>
        <?php if ((($this->isTeamMember || $this->isOwner) || ($this->subject->invite && $this->member && $this->member->active)) && $this->isFriends):?>
          <li><a href="javascript:Pageevent.invite(<?php echo $this->event_id?>);" class="buttonlink invite"><?php echo $this->translate('PAGEEVENT_INVITE')?></a></li>
        <?php endif;?>
        <?php if (($this->isTeamMember || $this->isOwner) && $this->count_waiting):?>
          <li><a href="javascript:Pageevent.waiting(<?php echo $this->event_id?>);" class="buttonlink waiting"><?php echo $this->translate('PAGEEVENT_WAITING')?> (<?php echo $this->count_waiting?>) </a></li>
        <?php endif;?>
        <?php if ($this->member && $this->member->active && !$this->isOwner):?>
          <li><a href="javascript:Pageevent.memberApprove(<?php echo $this->event_id?>, 0);" class="buttonlink leave"><?php echo $this->translate('PAGEEVENT_LEAVE')?></a></li>
        <?php endif;?>
      </ul>
    </div>

    <?php if ($this->attending->getTotalItemCount()):?>
      <div class="members">
        <div class="header">
          <?php $title = $this->translate('PAGEEVENT_MEMBERS_ATTENDING', array($this->attending->getTotalItemCount()))?>
          <div class="title"><?php echo $title?></div>
          <?php if ($this->attending->getTotalItemCount() > $this->attending->getItemCountPerPage()):?>
            <div class="viewall"><a href='javascript:Pageevent.members(<?php echo $this->event_id?>, 2, <?php echo $this->jsonInline($title)?>);'><?php echo $this->translate('PAGEEVENT_VIEWALL')?></a></div>
          <?php endif;?>
          <div class="clr"></div>
        </div>
        <div class="list">
          <?php foreach ($this->attending as $member):?>
            <div class="item">
              <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'))?>
              <div class="user_info"><?php echo $member->__toString()?></div>
            </div>
          <?php endforeach;?>
          <div class="clr"></div>
        </div>

      </div>
    <?php endif;?>

    <?php if ($this->maybe_attending->getTotalItemCount()):?>
      <div class="members">
        <div class="header">
          <?php $title = $this->translate('PAGEEVENT_MEMBERS_MAYBE_ATTENDING', array($this->maybe_attending->getTotalItemCount()))?>
          <div class="title"><?php echo $title?></div>
          <?php if ($this->maybe_attending->getTotalItemCount() > $this->maybe_attending->getItemCountPerPage()):?>
            <div class="viewall"><a href='javascript:Pageevent.members(<?php echo $this->event_id?>, 1, <?php echo $this->jsonInline($title)?>);'><?php echo $this->translate('PAGEEVENT_VIEWALL')?></a></div>
          <?php endif;?>
          <div class="clr"></div>
        </div>
        <div class="list">
          <?php foreach ($this->maybe_attending as $member):?>
            <div class="item">
              <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'))?>
              <div class="user_info"><?php echo $member->__toString()?></div>
            </div>
          <?php endforeach;?>
          <div class="clr"></div>
        </div>

      </div>
    <?php endif;?>


    <?php if ($this->not_attending->getTotalItemCount()):?>
      <div class="members">
        <div class="header">
          <?php $title = $this->translate('PAGEEVENT_MEMBERS_NOT_ATTENDING', array($this->not_attending->getTotalItemCount()))?>
          <div class="title"><?php echo $title?></div>
          <?php if ($this->not_attending->getTotalItemCount() > $this->not_attending->getItemCountPerPage()):?>
            <div class="viewall"><a href='javascript:Pageevent.members(<?php echo $this->event_id?>, 0, <?php echo $this->jsonInline($title)?>);'><?php echo $this->translate('PAGEEVENT_VIEWALL')?></a></div>
          <?php endif;?>
          <div class="clr"></div>
        </div>
        <div class="list">
          <?php foreach ($this->not_attending as $member):?>
            <div class="item">
              <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'))?>
              <div class="user_info"><?php echo $member->__toString()?></div>
            </div>
          <?php endforeach;?>
          <div class="clr"></div>
        </div>

      </div>
    <?php endif;?>

  </div>

  <div class="clr"></div>

</div>


