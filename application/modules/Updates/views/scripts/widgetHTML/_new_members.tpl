<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>

<?php if ($this->step == 'thumb'): ?>

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;padding:4px;vertical-align:bottom;text-decoration:none;display:inline-block;width:48px;height:48px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.icon'); ?>
  </a>

<?php elseif($this->step == 'details'): ?>
  <div style="margin-top:4px;font-size:10px;width: 50px">
    <a href="<?php echo $this->item->getHref(); ?>" style="text-decoration:none; color:<?php echo $this->linkColor;?>">
      <?php echo $this->item->getTitle(); ?>
    </a>
  </div>

<?php elseif($this->step == 'more_link'): ?>
  <div align="right">
    <a href="<?php echo $this->url(array('module'=>'members'), 'default', true) ?>" style="text-decoration:underline;padding-top: 5px;color:<?php echo $this->linkColor;?> ">
      <?php echo $this->translate('UPDATES_More members...');?>
    </a>
  </div>
<?php endif; ?>