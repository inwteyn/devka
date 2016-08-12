<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */


?>

	<div class="search">
		<?php echo $this->paginationControl(
				$this->members,
				null,
				array('pagination/filter.tpl', 'touch'),
				array(
					'search'=>$this->form->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Members'),
					'filterUrl' => $this->url(array(
            'id' => $this->event->getIdentity(),
            'waiting' => $this->waiting,
          ), 'event_profile', true) . '?tab=' . $this->identity,
          'pageUrlParams' => array(
            'route' => 'event_profile',
            'reset' => true,
            'id' => $this->event->getIdentity(),
            'waiting' => $this->waiting,
          ),
          'pageUrlQuery' => '?tab=' . $this->identity
				)

		); ?>
	</div>

<div id="filter_block">
<div class="event_members_info">
  <div class="touch_box">
    <?php if ($this->waiting_count):?>

      <?php if ($this->waiting):?>

        <a href="<?php echo $this->url(array(
            'id' => $this->event->getIdentity(),
            'waiting' => 0
          ), 'event_profile', true) . '?tab=' . $this->tab ?>" class="touchajax">
          <?php echo $this->translate('View all approved members'); ?>
        </a>

      <?php else:?>

        <a href="<?php echo $this->url(array(
            'id' => $this->event->getIdentity(),
            'waiting' => 1
          ), 'event_profile', true) . '?tab=' . $this->tab ?>" class="touchajax">
          <?php echo $this->translate('See Waiting'); ?>
        </a>

      <?php endif;?>

    <?php endif; ?>
  </div>

  <div class="event_members_total touch_box">
    <?php if( '' == $this->search ): ?>
      <?php echo $this->translate(array('This event has %1$s guest.', 'This event has %1$s guests.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
    <?php else: ?>
      <?php echo $this->translate(array('This event has %1$s guest that matched the query "%2$s".', 'This event has %1$s guests that matched the query "%2$s".', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->search) ?>
    <?php endif; ?>
  </div>
</div>

<?php if( $this->members->getTotalItemCount() > 0 ): ?>
  <ul class='items'>
    <?php foreach( $this->members as $member ):
      if( !empty($member->resource_id) ) {
        $memberInfo = $member;
        $member = $this->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $this->event->membership()->getMemberInfo($member);
      }
      ?>

      <li>

        <div class="item_photo">
          <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'event_members_icon touchajax')) ?>
        </div>

        <div class='item_body'>

          <div>
            <span class='event_members_status'>
              <?php echo $this->htmlLink($member->getHref(), $member->getTitle(), array('class' => 'touchajax')) ?>

              <?php // Titles ?>
              <?php if( $this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                (<?php echo ( $memberInfo->title ? $memberInfo->title : 'owner' ) ?>)
              <?php endif; ?>

            </span>
            <span>
              <?php echo $member->status; ?>
            </span>
          </div>
          <div class="event_members_rsvp">
            <?php if( $memberInfo->rsvp == 0 ): ?>
              <?php echo $this->translate('Not Attending') ?>
            <?php elseif( $memberInfo->rsvp == 1 ): ?>
              <?php echo $this->translate('Maybe Attending') ?>
            <?php elseif( $memberInfo->rsvp == 2 ): ?>
              <?php echo $this->translate('Attending') ?>
            <?php else: ?>
              <?php echo $this->translate('Awaiting Reply') ?>
            <?php endif; ?>

            <?php // Add/Remove Friend ?>
            <?php if( $this->viewer()->getIdentity() && !$this->viewer()->isSelf($member) ): ?>
              <?php if( !$this->viewer()->membership()->isMember($member) ): ?>
                - <?php echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $member->getIdentity()), $this->translate('Add Friend'), array('class' => 'smoothbox')) ?>
              <?php else: ?>
                - <?php echo $this->htmlLink(array('route' => 'user_extended', 'controller'=>'friends', 'action' => 'remove', 'user_id' => $member->getIdentity()), $this->translate('Remove Friend'), array('class' => 'smoothbox')) ?>
              <?php endif; ?>
            <?php endif; ?>
            <?php // Remove/Promote/Demote member ?>
            <?php if( $this->event->isOwner($this->viewer())): ?>
              <?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
                - <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Approve Request'), array('class' => 'smoothbox')) ?>
                - <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Reject Request'), array('class' => 'smoothbox')) ?>
              <?php endif; ?>
              <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true ): ?>
                - <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity()), $this->translate('Cancel Invite'), array('class' => 'smoothbox')) ?>
              <?php endif; ?>
            <?php endif; ?>

          </div>

        </div>

      </li>

    <?php endforeach;?>

  </ul>
 </div>

<?php endif; ?>