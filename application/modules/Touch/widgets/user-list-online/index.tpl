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

<!--
<h3><?php echo $this->translate('%d Members Online', $this->count)?></h3>
-->

<ul class="items">
  <?php foreach( $this->paginator as $user ): ?>
	<li class='item_photo' style="padding: 3px;">
     <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle(), array('class' => 'touchajax'))) ?>
  <?php endforeach; ?>
	</li>
</ul>
<div class="clr"></div>
