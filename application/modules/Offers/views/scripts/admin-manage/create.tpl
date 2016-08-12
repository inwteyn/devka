<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-06-06 17:01 ratbek $
 * @author     Ratbek
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'/application/modules/Offers/externals/scripts/datepicker.js'); ?>
<h2><?php echo $this->translate("OFFERS_Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("OFFERS_ADMIN_CREATE_DESCRIPTION"); ?>
</p>
<br />
<div class="offers_admin_manage_menu">
  <a class="offers_admin_manage_menu_items" href="<?php echo $this->url(array('action' => 'index'), 'offer_admin_manage');?>"><?php echo $this->translate('OFFERS_View Offers'); ?></a>
  <span class="offers_admin_manage_menu_items active_item"><?php echo $this->translate('OFFERS_Create Offer');?></span>
</div>
<?php if(isset($this->message) && !empty($this->message)): ?>
  <div class="hidden message tip"><?php echo $this->render('message.tpl'); ?></div>
<?php endif; ?>
<div class="settings offers_admin_create_offer"><?php echo $this->render('form.tpl'); ?></div>