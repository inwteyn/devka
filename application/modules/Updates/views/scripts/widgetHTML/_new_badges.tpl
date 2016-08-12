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

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;width:70px;height:70px;overflow:hidden">
    <img src="<?php echo $this->item->getPhotoUrl('thumb.normal'); ?>" width='70' height='70' border='0'/>
  </a>

<?php elseif($this->step == 'details'): ?>
  <a href="<?php echo $this->item->getHref() ?>" style="font-weight: bold; color:<?php echo $this->linkColor; ?>; font-size: 12px;text-decoration: none">
    <font color="<?php echo $this->linkColor?>">
      <?php echo $this->item->getTitle(); ?>
    </font>
  </a>

  <div style="font-size: 10px">
    <?php echo $this->translate('UPDATES_Created on') . ' ' . date('d M  Y', strtotime($this->item->creation_date));?>
  </div>
  <div style="margin-top:5px;">
      <font size="2" style="font-size:12px; font-family: arial; color:<?php echo $this->fontColor; ?>">
        <?php echo Engine_String::substr(Engine_String::strip_tags($this->item->description), 0, 90);
              echo (Engine_String::strlen($this->item->description)>89)? "...":'';
        ?>
     </font>
  </div>

<?php elseif($this->step == 'more_link'): ?>

  <div align="right">
    <a href="<?php echo $this->url(array('module'=>'badges'), 'default', true); ?>" style="text-decoration:underline;color:<?php echo $this->linkColor; ?>">
      <?php echo $this->translate('UPDATES_More badges...'); ?>
    </a>
  </div>

<?php endif; ?>