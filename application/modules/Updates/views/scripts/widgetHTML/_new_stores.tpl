<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2011-10-06 16:56 mirlan $
 * @author     Mirlan
 */
?>
<?php if ($this->step == 'thumb'): ?>

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;width:48px;height:48px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.icon'); ?>
  </a>

<?php elseif($this->step == 'details'): ?>
  <div>
    <a href="<?php echo $this->item->getHref();?>" style="font-weight: bold; color:<?php echo $this->linkColor;?>; font-size: 12px;text-decoration: none">
      <?php echo $this->item->getTitle(); ?>
    </a>
    <span>
      <?php if ($this->item->sponsored) : ?>
        <img class="icon" src="/application/modules/Store/externals/images/sponsored.png" title="<?php echo $this->translate('STORE_Sponsored'); ?>" height="14px">
      <?php endif; ?>
      <?php if ($this->item->featured) : ?>
        <img class="icon" src="/application/modules/Store/externals/images/featured.png" title="<?php echo $this->translate('STORE_Featured'); ?>" height="14px">
      <?php endif; ?>
    </span>
  </div>

  <div style="font-size: 10px">
    <?php echo $this->translate('UPDATES_Created on').' '.date('d M Y', strtotime($this->item->creation_date)).' '. $this->translate('UPDATES_by'); ?>
    <a href="<?php echo $this->item->getOwner()->getHref();?>" style="color:<?php echo $this->linkColor ?>; text-decoration: none">
      <?php echo $this->item->getOwner()->getTitle(); ?>
    </a>
  </div>
  <?php if (!empty($this->item->description)): ?>
  <div style="margin-top:5px; font-size: 11px;">
    <?php echo Engine_String::substr(Engine_String::strip_tags($this->item->description), 0, 90);
          echo (Engine_String::strlen($this->item->description)>89)? "...":'';
    ?>
  </div>
  <?php endif; ?>

<?php elseif($this->step == 'more_link'): ?>

  <div align="right">
    <a href="<?php echo $this->url(array('action'=>'stores'), 'store_general', true); ?>" style="text-decoration:underline;padding-top: 5px;color:<?php echo $this->linkColor; ?>">
      <?php echo $this->translate('UPDATES_More stores...'); ?>
    </a>
  </div>

<?php endif; ?>