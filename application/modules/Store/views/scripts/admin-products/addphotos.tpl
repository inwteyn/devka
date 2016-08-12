<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: addphotos.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<?php echo $this->render('admin/_productHeader.tpl'); ?>

<div style="width: 75%; float: left;">
  <?php echo $this->getGatewayState(0); ?>
  <div class="admin_home_middle">
    <div class="settings">
      <?php echo $this->form->render($this); ?>
    </div>
  </div>


</div>

<div style="float: right;">
  <?php echo $this->render('admin/_productsMenu.tpl'); ?>
</div>
