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

<ul class="site-map-list">
	<?php foreach( $this->navigation as $item ): ?>
		<li>
			<a href="<?php echo $item->getHref(); ?>" class="touchajax">
				<?php echo $this->translate($item->getLabel()); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>