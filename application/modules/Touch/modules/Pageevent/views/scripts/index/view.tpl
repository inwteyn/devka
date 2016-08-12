<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>



<div class="layout_content">
<ul class="items subcontent">
<li class="touch_page_event_view_right_column">
  <div class="item_photo">
    <?php echo $this->itemPhoto($this->event, 'thumb.profile')?>
  </div>
  <div class="options touch_page_event_rsvp_options">

      <?php if ($this->member && !$this->member->resource_approved):?>

      <div class="item">
          <span><?php echo $this->translate('PAGEEVENT_MEMBER_WAITING')?></span>
      </div>
      <div class="item">
          <form method="post"
                action="<?php echo $this->url(array('action' => 'member-approve', 'event_id' => $this->event_id, 'approve' => 0), 'page_event', true)?>">
              <button class="<?php if ($this->member && $this->member->rsvp == 2):?>active<?php endif;?>"
                      type="submit"><?php echo $this->translate('PAGEEVENT_CANCEL')?>
              </button>
          </form>
      </div>

      <?php elseif ($this->isLogin):?>

      <?php if ($this->event->approval && !$this->member):?>

      <div class="item">
          <form method="post"
                action="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 0), 'page_event', true)?>">
              <button class="<?php if ($this->member && $this->member->rsvp == 0):?>active<?php endif;?>"
                      type="submit"><?php echo $this->translate('PAGEEVENT_REQUEST_INVITE')?>
              </button>
          </form>
      </div>

      <?php else:?>

      <form method="post"
            action="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id), 'page_event', true)?>">
          <div id = '_rsvp_loading' style="display: none; position: absolute; "></div>
        <div  id = '_rsvp_'>
          <div class="item_options">
            <?php if ($this->member && $this->member->rsvp == 2):?>
            <a class="active"><?php echo $this->
                translate('PAGEEVENT_ATTENDING').' ('.$this->attending->getTotalItemCount().')'?>
            </a>
            <?php else: ?>
            <a href="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 2), 'page_event', true)?>"
               onclick = 'Touch.navigation.subRequest($(this), "_rsvp_loading", "_rsvp_"); return false;'><?php echo $this->translate('PAGEEVENT_ATTENDING').' ('.$this->attending->getTotalItemCount().')'?></a>
            <?php endif;?>
            <?php if ($this->member && $this->member->rsvp == 1):?>
            <a class="active"><?php echo $this->
              translate('PAGEEVENT_MAYBEATTENDING').' ('.$this->maybe_attending->getTotalItemCount().')'?>
            </a>
            <?php else: ?>
            <a href="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 1), 'page_event', true)?>"
             onclick = 'Touch.navigation.subRequest($(this), "_rsvp_loading", "_rsvp_"); return false;'><?php echo $this->translate('PAGEEVENT_MAYBEATTENDING').' ('.$this->maybe_attending->getTotalItemCount().')'?></a>
            <?php endif;?>
            <?php if ($this->member && $this->member->rsvp == 0):?>
            <a class="active"><?php echo $this->
              translate('PAGEEVENT_NOTATTENDING').' ('.$this->not_attending->getTotalItemCount().')'?>
            </a>
            <?php else: ?>
            <a href="<?php echo $this->url(array('action' => 'rsvp', 'event_id' => $this->event_id, 'rsvp' => 0), 'page_event', true)?>"
            onclick = 'Touch.navigation.subRequest($(this), "_rsvp_loading", "_rsvp_"); return false;'><?php echo $this->translate('PAGEEVENT_NOTATTENDING').' ('.$this->not_attending->getTotalItemCount().')'?></a>
            <?php endif;?>
          </div>
          <div class="item_options">
          <?php if ($this->attending->getTotalItemCount()):?>
            <div class="members">
              <div class="header">
                <a class="title"><?php echo $this->translate('PAGEEVENT_ATTENDING'); ?></a>
                <?php if ($this->attending->getTotalItemCount() > $this->attending->getItemCountPerPage()):?>
                  <div class="viewall"><a href='javascript:Pageevent.members(<?php echo $this->event_id?>, 2, <?php echo $this->jsonInline($title)?>);'><?php echo $this->translate('PAGEEVENT_VIEWALL')?></a></div>
                <?php endif;?>
                <div class="clr"></div>
              </div>
              <div class="list">
                <?php foreach ($this->attending as $member):?>
                  <div class="item">
                    <div class="userinfo">
                      <?php echo $this->htmlLink($member->getHref(), $member->getTitle())?>
                    </div>
                  </div>
                <?php endforeach;?>
                <div class="clr"></div>
              </div>

            </div>
          <?php endif;?>

          <?php if ($this->maybe_attending->getTotalItemCount()):?>
            <div class="members">
              <div class="header">
                <a class="title"><?php echo $this->translate('PAGEEVENT_MAYBEATTENDING'); ?></a>
                <?php if ($this->maybe_attending->getTotalItemCount() > $this->maybe_attending->getItemCountPerPage()):?>
                  <div class="viewall"><a href='javascript:Pageevent.members(<?php echo $this->event_id?>, 1, <?php echo $this->jsonInline($title)?>);'><?php echo $this->translate('PAGEEVENT_VIEWALL')?></a></div>
                <?php endif;?>
                <div class="clr"></div>
              </div>
              <div class="list">
                <?php foreach ($this->maybe_attending as $member):?>
                  <div class="item">
                    <div class="userinfo">
                      <?php echo $this->htmlLink($member->getHref(), $member->getTitle())?>
                    </div>
                  </div>
                <?php endforeach;?>
                <div class="clr"></div>
              </div>

            </div>
          <?php endif;?>


          <?php if ($this->not_attending->getTotalItemCount()):?>
            <div class="members">
              <div class="header">
                <a class="title"><?php echo $this->translate('PAGEEVENT_NOTATTENDING'); ?></a>
                <?php if ($this->not_attending->getTotalItemCount() > $this->not_attending->getItemCountPerPage()):?>
                  <div class="viewall"><a href='javascript:Pageevent.members(<?php echo $this->event_id?>, 0, <?php echo $this->jsonInline($title)?>);'><?php echo $this->translate('PAGEEVENT_VIEWALL')?></a></div>
                <?php endif;?>
                <div class="clr"></div>
              </div>
              <div class="list">
                <?php foreach ($this->not_attending as $member):?>
                  <div class="item">
                    <div class="userinfo">
                      <?php echo $this->htmlLink($member->getHref(), $member->getTitle())?>
                    </div>
                  </div>
                <?php endforeach;?>
                <div class="clr"></div>
              </div>

            </div>
          <?php endif;?>
        </div>
          </div>
      </form>

      <?php endif;?>

      <?php endif;?>

      <div class="clr"></div>
  </div>

</li>
<li class="touch_page_event_view_left_column">
  <div class="touch_page_event_view_title">
    <h4>    <span><?php echo $this->translate('PAGEVENT_ONWER', $this->event->getOwner()->__toString())?></span>
<?php echo $this->event->getTitle() ?></h4>
  </div>
  <div class="touch_page_event_view_info">
        <div class="item">
            <div class="label"><b><?php echo $this->translate('Posted')?> :</b>
                <?php echo $this->timestamp($this->event->creation_date)?>
            </div>
            <div class="clr"></div>
        </div>

        <?php if ($this->event->starttime == $this->event->endtime ): ?>

        <div class="item">
            <div class="label"><b><?php echo $this->translate('Date')?> :</b>
                <?php echo $this->locale()->toDate($this->startDateObject)?> <?php echo $this->
                locale()->toTime($this->startDateObject) ?>
            </div>
            <div class="clr"></div>
        </div>

        <?php elseif( $this->startDateObject->toString('y-MM-dd') == $this->endDateObject->toString('y-MM-dd') ): ?>

        <div class="item">
            <div class="label"><b><?php echo $this->translate('Date')?> :</b>
                <?php echo $this->locale()->toDate($this->startDateObject) ?>
            </div>
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
            <div class="label"><b><?php echo $this->translate('Date')?> :</b>
                <?php echo $this->translate('%1$s at %2$s', $this->locale()->toDate($this->startDateObject),
                $this->locale()->toTime($this->startDateObject))?> -
                <?php echo $this->translate('%1$s at %2$s', $this->locale()->toDate($this->endDateObject),
                $this->locale()->toTime($this->endDateObject))?>
            </div>
            <div class="clr"></div>
        </div>

        <?php endif;?>

        <?php if (!empty($this->event->location)):?>

        <div class="item">
            <div class="label"><b><?php echo $this->translate('PAGEEVENT_WHERE')?> :</b>
                <?php echo $this->event->location?>
                <?php echo $this->htmlLink('http://maps.google.com/?q='.urlencode($this->event->location),
                $this->translate('PAGEEVENT_MAP'), array('target' => 'blank'))?>
            </div>
            <div class="clr"></div>
        </div>

        <?php endif;?>

        <?php if (
            $this->attending->getTotalItemCount() ||
        $this->maybe_attending->getTotalItemCount() ||
        $this->not_attending->getTotalItemCount()
        ):?>

        <div class="item">
            <div class="label">
                <b><?php echo $this->translate('TOUCH_PAGE_EVENT_MEMBERS');?> </b>
                <?php if ($this->attending->getTotalItemCount()):?>
                <?php
                    $title = $this->translate('TOUCH_PAGE_EVENT_MEMBERS_ATTENDING');
                ?>
                <?php echo $title .' ('.$this->attending->getTotalItemCount().')'; ?>

                <br/>

                <?php endif;?>
                <?php if ($this->maybe_attending->getTotalItemCount()):?>

                <?php

                $title = $this->translate('TOUCH_PAGE_EVENT_MEMBERS_MAYBE_ATTENDING');

                ?>
                <?php echo $title .' ('.$this->maybe_attending->getTotalItemCount().')';?>

                <br/>

                <?php endif;?>


                <?php if ($this->not_attending->getTotalItemCount()):?>

                <?php

                $title = $this->translate('TOUCH_PAGE_EVENT_MEMBERS_NOT_ATTENDING');

                ?>
                <?php echo $title .' ('.$this->not_attending->getTotalItemCount().')' ?>

                <br/>

                <?php if (($this->isTeamMember || $this->isOwner) && $this->count_waiting):?>

                <?php echo $this->htmlLink(array(
                'route' => 'page_event',
                'action' => 'waiting',
                'event_id' => $this->event->getIdentity(),
                ), $this->translate('TOUCH_PAGE_EVENT_MEMBERS_WAITING') . '')?>

                <?php endif;?>

                <?php endif;?>

            </div>
            <div class="clr"></div>
        </div>

        <?php endif;?>
  </div>
  <div class="touch_page_event_view_description">
    <p class="item_body">
        <?php echo $this->event->getDescription() ?>
    </p>
  </div>
</li>
</ul>
</div>
<div style="padding-bottom: 5px;"></div>

<?php echo $this->touchAction("list", "comment", "core", array("type"=>"pageevent", "id"=>$this->event->getIdentity(),
'viewAllLikes' => true)) ?>

</div>