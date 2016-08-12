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
<?php
$active_theme = $this->touchActiveTheme();

?>
<ul class="navigation">
	<?php $i = 0; foreach($this->navigation as $item): $i++	?>
  
		<li <?php if ($item->isActive()) : ?> class="selected" <?php endif; ?>>
			<?php $name = trim(str_replace('menu_core_main ','', $item->getClass()));?>
			<a href="<?php echo $item->getHref(); ?>" class="<?php if($name != 'core_main_home'){?>touchajax<?php } ?> main-menu-item">
				<img src="application/modules/Touch/themes/<?php echo $active_theme; ?>/images/icons/<?php echo $name; ?>.png" alt="<?php echo $this->translate($item->getLabel()); ?>" />
			</a>
		</li>
	<?php endforeach; ?>
</ul>
