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

<nobr>
	<div class="mode-switcher">
		<?php if ($this->isTouch()): ?>

			<a class="standard" href="<?php echo $this->url(array('mode'=>'standard'), 'touch_mode_switch'); ?>?return_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" onclick="if(Touch.getHash()!=''){this.href='<?php echo $this->url(array('mode'=>'standard'), 'touch_mode_switch'); ?>?return_url='+Touch.getHash();}"><?php echo $this->translate($this->standard); ?></a>
			<?php if ( $this->isMobileEnabled ): ?>
				<a class="mobile" href="<?php echo $this->url(array('mode'=>'mobile'), 'touch_mode_switch'); ?>?return_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" onclick="if(Touch.getHash()!=''){this.href='<?php echo $this->url(array('mode'=>'mobile'), 'touch_mode_switch'); ?>?return_url='+Touch.getHash();}"><?php echo $this->translate($this->mobile); ?></a>
			<?php endif; ?>

		<?php else: ?>

			<?php if ( $this->isMobileEnabled): ?>
				<?php if ( $this->isMobile() ): ?>
					<a href="<?php echo $this->url(array('mode'=>'standard'), 'touch_mode_switch'); ?>?return_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"><?php echo $this->translate($this->standard); ?></a>
					&#8212;
					<a href="<?php echo $this->url(array('mode'=>'touch'), 'touch_mode_switch'); ?>?return_url=<?php echo urlencode((($this->viewer()->getIdentity())? $this->url(array('action' => 'home'), 'user_general', true) : $this->url(array(), 'home', true)).'#'.$_SERVER['REQUEST_URI']); ?>"><?php echo $this->translate($this->touch); ?></a>
					&#8212;
					<?php echo $this->translate($this->mobile); ?>
				<?php else: ?>
					<?php echo $this->translate($this->standard); ?>
					&#8212;
					<a href="<?php echo $this->url(array('mode'=>'touch'), 'touch_mode_switch'); ?>?return_url=<?php echo urlencode((($this->viewer()->getIdentity())? $this->url(array('action' => 'home'), 'user_general', true) : $this->url(array(), 'home', true)).'#'.$_SERVER['REQUEST_URI']); ?>"><?php echo $this->translate($this->touch); ?></a>
					&#8212;
					<a href="<?php echo $this->url(array('mode'=>'mobile'), 'touch_mode_switch'); ?>?return_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"><?php echo $this->translate($this->mobile); ?></a>
				<?php endif; ?>
			<?php else: ?>
				<?php echo $this->translate($this->standard); ?>
				&#8212;
				<a href="<?php echo $this->url(array('mode'=>'touch'), 'touch_mode_switch'); ?>?return_url=<?php echo urlencode((($this->viewer()->getIdentity())? $this->url(array('action' => 'home'), 'user_general', true) : $this->url(array(), 'home', true)).'#'.$_SERVER['REQUEST_URI']); ?>"><?php echo $this->translate($this->touch); ?></a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</nobr>