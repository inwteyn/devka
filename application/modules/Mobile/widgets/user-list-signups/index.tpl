<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

?>
<ul class="items">
  <?php foreach( $this->paginator as $user ): ?>
    <li>
			<div class="item_photo">
      	<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle())) ?>
			</div>

      <div class='item_body'>
        <div class='item_title'>
							<?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
        </div>
        <div class='item_date'>
          <?php echo $this->timestamp($user->creation_date) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>