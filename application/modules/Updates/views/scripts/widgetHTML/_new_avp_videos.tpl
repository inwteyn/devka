<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2012-05-14 16:23 ratbek $
 * @author     Ratbek
 */
?>
<?php if ($this->step == 'thumb'): ?>

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;height:90px;overflow:hidden">
    <img src="<?php echo $this->item->getPhotoUrl('thumb.normal'); ?>" width='140' height='90' border='0'/>
  </a>

<?php elseif($this->step == 'details'): ?>

  <div style="margin-top:4px;">

    <span style="font-weight:bold;font-size:12px">
      <a href="<?php echo $this->item->getHref(); ?>" style="text-decoration:none; color:<?php echo $this->linkColor; ?>;">
        <?php echo $this->substr($this->item->getTitle()); ?>
      </a>
    </span><br/>

    <?php echo $this->translate('UPDATES_By'); ?>
    <a href="<?php echo $this->item->getOwner()->getHref(); ?>" style="text-decoration:none; color:<?php echo $this->linkColor;?>">
      <?php echo $this->item->getOwner()->getTitle()?>
    </a>
  </div>

<?php elseif($this->step == 'more_link'): ?>
  <div align="right">
    <a href="<?php echo $this->url(array(), 'avp_general', true); ?>" style="text-decoration:underline;padding-top: 5px;color:<?php echo $this->linkColor; ?>;">
      <?php echo $this->translate('UPDATES_More videos...');?>
    </a>
  </div>
<?php endif; ?>