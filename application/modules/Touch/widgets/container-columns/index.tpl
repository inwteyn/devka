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
<table><tr>
	<td style="float:left; padding: 3px" valign="top">
		<?php foreach($this->element->getElements() as $child): ?>
			<?php if ($child->getParam('column') == 'left'): ?>
				<div><?php echo $child->render(); ?></div>
			<?php endif; ?>
		<?php endforeach; ?>
	</td>

	<td style="padding: 3px" valign="top">
		<?php foreach($this->element->getElements() as $child): ?>
			<?php if ($child->getParam('column') == 'right'): ?>
				<div><?php echo $child->render(); ?></div>
			<?php endif; ?>
		<?php endforeach; ?>
	</td>
</tr></table>