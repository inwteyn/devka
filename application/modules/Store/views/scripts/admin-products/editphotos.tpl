<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: editphotos.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>

<?php echo $this->render('admin/_productHeader.tpl'); ?>

<div>
  <div style="width: 75%; float: left;">
    <?php echo $this->render('admin-products/_photosList.tpl'); ?>
    <?php echo $this->getGatewayState(0); ?>
  </div>
  <div style="float: right;">
    <?php echo $this->render('admin/_productsMenu.tpl'); ?>
  </div>
</div>
