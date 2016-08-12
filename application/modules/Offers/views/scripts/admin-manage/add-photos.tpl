<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: add.tpl 2012-07-19 17:52:12 ratbek $
 * @author     Ratbek
 */
?>
<script type="text/javascript">
  en4.core.runonce.add(function(){
    $('form-upload').style.clear = 'none';
  });
</script>

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
  <?php echo $this->translate("OFFERS_ADMIN_MANAGE_DESCRIPTION") ?>
</p>
<br />
<div class="offers_admin_manage_menu">
  <a class="offers_admin_manage_menu_items" href="<?php echo $this->url(array('action' => 'index'), 'offer_admin_manage');?>"><?php echo $this->translate('OFFERS_View Offers'); ?></a>
  <a class="offers_admin_manage_menu_items" href="<?php echo $this->url(array('action' => 'create'), 'offer_admin_manage');?>"><?php echo $this->translate('OFFERS_Create Offer');?></a>
</div>
<div class="offers_manage_links">
  <?php echo $this->htmlLink($this->url(array('action'=>'edit', 'offer_id' => $this->offer->offer_id), 'offer_admin_manage'), $this->translate('OFFERS_Back'), array(
    'class' => 'buttonlink back_to_edit',
  )); ?>
  <br />
  <?php echo $this->htmlLink($this->url(array('action'=>'manage-photos', 'offer_id' => $this->offer->offer_id), 'offer_admin_manage'), $this->translate('OFFERS_Edit photos'), array(
    'class' => 'buttonlink edit_photo',
  )); ?>
  <br>
</div>

<?php echo $this->form->render($this); ?>

  