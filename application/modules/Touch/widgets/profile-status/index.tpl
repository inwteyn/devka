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
<h3 class="like_status_header">
	<?php echo $this->subject()->getTitle() ?>
</h3>

<?php if ($this->isLikeEnabled): ?>
	<?php echo $this->touchLikeButton($this->subject); ?>
	<div class="clr"></div>
<?php endif;?>