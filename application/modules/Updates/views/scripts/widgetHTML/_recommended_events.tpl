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

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;width:48px;height:48px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.icon'); ?>
  </a>

<?php elseif($this->step == 'details'): ?>

  <a href="<?php echo $this->item->getHref();?>" style="font-weight: bold; color:<?php echo $this->linkColor;?>; font-size: 12px;text-decoration: none">
    <?php echo $this->item->getTitle(); ?>
  </a>
  <div style="font-size: 10px">
    <?php echo $this->translate('UPDATES_Created on').' '.date('d M Y', strtotime($this->item->creation_date)).' '. $this->translate('UPDATES_by'); ?>
    <a href="<?php echo $this->item->getOwner()->getHref()?>" style="color:<?php echo $this->linkColor ?>; text-decoration: none">
      <?php echo $this->item->getOWner()->getTitle(); ?>
    </a><br/>
    <?php echo $this->translate('UPDATES_Time').': '.date('d M Y', strtotime($this->item->starttime)).' '. $this->translate('UPDATES_at').' '.date('H:i', strtotime($this->item->creation_date)); ?>
    <?php if (!empty($this->item->description)): ?>
    <div style="margin-top:5px; font-size: 11px;">
      <?php echo Engine_String::substr(Engine_String::strip_tags($this->item->description), 0, 90);
            echo (Engine_String::strlen($this->item->description)>89)? "...":'';
      ?>
    </div>
    <?php endif;?>
  </div>

<?php elseif($this->step == 'more_link'): ?>

  <div align="right">
    <a id="new_events_more_link" href="<?php echo $this->url(array('module'=>'events'), 'default', true); ?>" style="text-decoration:underline;color:<?php echo $this->linkColor; ?>">
      <?php echo $this->translate('UPDATES_More events...'); ?>
    </a>
  </div>

<?php endif; ?>