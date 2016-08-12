<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>


<div class="item_photo">
  <?php echo $this->itemPhoto($this->subject(), 'thumb.profile')?>
</div>

<div class="item_body">
  <div class="item_title"><a href="<?php echo $this->subject()->getHref()?>"><?php echo $this->subject()->getTitle();?></a></div>
  <div class="item_description">
    <a href="<?php echo $this->url(array('action' => 'view', 'id' => $this->subject()->getIdentity(), 'tab' => 'members'), 'hebadge_profile', true)?>">
      <?php echo $this->translate(array('%1$s member', '%1$s members', $this->subject()->member_count), $this->subject()->member_count);?>
    </a>
  </div>
</div>
