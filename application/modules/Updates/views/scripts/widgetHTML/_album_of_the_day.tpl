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

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;height:90px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.normal'); ?>
  </a>

<?php elseif($this->step == 'details'): ?>
  <div style="margin-top:4px;">
    <span style="font-weight:bold;font-size:12px">

    <a href="<?php echo $this->item->getHref()?> " style="text-decoration:none; color:<?php echo $this->linkColor ?>">
      <?php echo $this->substr($this->item->getTitle()); ?>
    </a>
    </span> - <?php echo $this->translate(array('UPDATES_ %s photo', '%s photos', $this->item->count()), $this->locale()->toNumber($this->item->count()))?> <br/>

    <?php echo $this->translate('UPDATES_By'); ?>
    <a href="<?php echo $this->item->getOwner()->getHref(); ?>" style="text-decoration:none; color:<?php echo $this->linkColor; ?>">
      <?php echo $this->item->getOwner()->getTitle(); ?>
    </a>
  </div>

<?php elseif($this->step == 'more_link'): ?>
 <div align="right">
   <a id="new_albums_more_link" href="<?php echo $this->url(array('module'=>'albums'), 'default', true); ?>" style="text-decoration:underline;padding-top: 5px;color:<?php echo $this->linkColor; ?>">
     <?php echo $this->translate('UPDATES_More albums...'); ?>
   </a>
 </div>
<?php endif; ?>