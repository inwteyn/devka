<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: more-main.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?><?php if ( isset($this->navigation) ): ?>
	<div id='touch_profile_options'>
		<ul>
			<?php foreach($this->navigation as $item): ?>
				<li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array('class'=>'buttonlink')) ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>