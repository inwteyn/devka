<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Bolot
 */
?>
<?php if ($this->step == 'thumb'): ?>
    <a href="<?php echo $this->item->getHref(); ?>"
       style="border:1px solid #DDDDDD; float:left;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;width:48px;height:48px;overflow:hidden">
        <?php echo $this->itemPhoto($this->item, 'thumb.icon'); ?>
    </a>
<?php endif; ?>
<?php if ($this->step == 'details'): ?>
    <a href="<?php echo $this->item->getHref() ?>"
       style="font-weight: bold; color:<?php echo $this->linkColor ?>; font-size: 12px;text-decoration: none">
        <font color="<?php echo $this->linkColor ?>">
            <?php echo $this->item->getTitle(); ?>
        </font>
    </a>
    <div style="font-size: 10px">
        <?php echo $this->translate('UPDATES_Posted on ') . ' ' . date('d M  Y', strtotime($this->item->posted_date)) . ' ' . $this->translate('UPDATES_by') ?>
        <a href="<?php echo $this->item->getOwner()->getHref(); ?>"
           style="color:<?php echo $this->linkColor; ?>; text-decoration: none">
            <?php echo $this->item->getOwner()->getTitle(); ?>
        </a>
    </div>
<?php endif; ?>