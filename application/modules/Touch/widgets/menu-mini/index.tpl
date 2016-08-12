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

<div id='touch_menu_mini_menu'>
  <?php
    // Reverse the navigation order (they're floating right)
    $count = count($this->navigation);
    foreach( $this->navigation->getPages() as $item ) $item->setOrder(--$count);
		if (isset($this->more))		$this->navigation->addPage($this->more);
  ?>
  <ul>
    <?php foreach( $this->navigation as $item ): ?>
      <li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel())) ?></li>
    <?php endforeach; ?>

		<li>
			<a href="<?php echo $this->url(array('controller' => 'index'), 'touch_dashboard', true) ?>" class="touchajax" id="dashboard">
				<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/dashboard.png" border="0" alt="<?php echo $this->translate('Dashboard'); ?>" style="vertical-align:bottom;"/>
			</a>
		</li>

		<?php if( $this->viewer->getIdentity()) :?>
		<li>
			<a href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'notifications'), 'default', true) ?>" class="touchajax">
				<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/<?php if ($this->notificationCount > 0):?>updates_active.png<?php else: ?>updates.png<?php endif; ?>" border="0" alt="<?php echo $this->translate('Updates'); ?>" style="vertical-align:bottom;"/>
			</a>
		</li>
		<?php endif; ?>

    <?php if($this->search_check):?>
      <li>
				<a href="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" class="touchajax">
					<img src="application/modules/Touch/themes/<?php echo $this->touchActiveTheme(); ?>/images/search.png" border="0" alt="<?php echo $this->translate('Search'); ?>" style="vertical-align:bottom;"/>
				</a>
  		</li>
    <?php endif;?>
</div>