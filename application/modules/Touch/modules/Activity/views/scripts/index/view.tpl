<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<div class="layout_content">
	<ul class='items subcontent'>
		<?php echo $this->touchActivity($this->action, array(
			'action_id' => $this->action->action_id,
			'viewAllComments' => true,
			'viewAllLikes' => true,
		), 'comment') ?>
	</ul>
</div>
