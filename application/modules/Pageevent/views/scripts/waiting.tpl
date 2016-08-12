
<?php

 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: waiting.tpl 2010-07-02 17:53 michael $
 * @author     Michael
 */

?>

<?php if (count($this->members)):?>

<div class="pageevent_waiting">

  <div class="header">
    <div class="title">
      <?php echo $this->translate('PAGEEVENT_WATINGS')?> (<span id="pageevent_waiting_list_count"><?php echo count($this->members)?></span>)
     </div>
    <div class="backlink">
      <a href="javascript:Pageevent.view(<?php echo $this->event_id?>);" class="buttonlink back"><?php echo $this->translate('PAGEEVENT_BACK')?></a>
    </div>
    <div class="clr"></div>
  </div>

  <div class="members">
    <?php foreach ($this->members as $member):?>
      <div class="item">
        <div class="photo"><?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'))?></div>
        <div class="user_info">
          <div class="username"><?php echo $member->__toString()?></div>
          <div class="options">
            <ul>
              <?php if (!$member->user_approved):?>
                <li><a href="javascript:void(0);" onclick="Pageevent.resourceApprove(<?php echo $this->event_id?>, <?php echo $member->getIdentity();?>, 0, this)" class="buttonlink cancel"><?php echo $this->translate('PAGEEVENT_INVITE_CANCEL');?></a></li>
              <?php else:?>
                <li><a href="javascript:void(0);" onclick="Pageevent.resourceApprove(<?php echo $this->event_id?>, <?php echo $member->getIdentity();?>, 1, this)" class="buttonlink accept"><?php echo $this->translate('PAGEEVENT_APPROVE');?></a></li>
                <li><a href="javascript:void(0);" onclick="Pageevent.resourceApprove(<?php echo $this->event_id?>, <?php echo $member->getIdentity();?>, 0, this)" class="buttonlink reject"><?php echo $this->translate('PAGEEVENT_REJECT');?></a></li>
              <?php endif;?>
            </ul>
          </div>
        </div>
        <div class="clr"></div>
      </div>
    <?php endforeach;?>
    <div class="clr"></div>
  </div>

</div>

<?php else: ?>

  <div class="tip"><span><?php echo $this->translate('PAGEEVENT_NOMEMBERS', array('javascript:Pageevent.view('.$this->event_id.')'))?></span></div>

<?php endif;?>
