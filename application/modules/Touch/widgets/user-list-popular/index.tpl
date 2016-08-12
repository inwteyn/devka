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

<ul class="items">
  <?php foreach( $this->paginator as $user ): ?>
    <li>
			<div class="item_photo">
      	<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'popularmembers_thumb touchajax')) ?>
			</div>

      <div class='item_body'>
        <div class='item_title'>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('class' => 'touchajax')) ?>
        </div>
        <div class='item_date'>
          <?php echo $this->translate(array('%s friend', '%s friends', $user->member_count),$this->locale()->toNumber($user->member_count)) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
