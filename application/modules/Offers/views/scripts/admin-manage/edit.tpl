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

<script type="text/javascript">

en4.core.runonce.add(function() {

  if ($('time_limit').value == 'unlimit') {
    $('starttime-hour').setProperty('value', '');
    $('starttime-minute').setProperty('value', '');
    if ($('starttime-ampm') != null) {
    $('starttime-ampm').setProperty('value', '');
    }
    $('starttime-wrapper').setStyle('display', 'none');

    $('endtime-hour').setProperty('value', '');
    $('endtime-minute').setProperty('value', '');
    if ($('endtime-ampm') != null) {
    $('endtime-ampm').setProperty('value', '');
    }
    $('endtime-wrapper').setStyle('display', 'none');
  }
  else if ($('time_limit').value == 'limit') {
    $('starttime-wrapper').setStyle('display', 'block');
    $('endtime-wrapper').setStyle('display', 'block');
  }

  if ($('type').value == 'paid') {
    $('price-wrapper').setStyle('display', 'block');
    $$('.admin_offers_require').setStyle('display', 'none');
  }
  else if ($('type').value == 'free') {
    $('price-wrapper').setStyle('display', 'none');
    $$('.admin_offers_require').setStyle('display', 'none');
  }
  else if ($('type').value == 'condition') {
    $('price-wrapper').setStyle('display', 'none');
    $$('.admin_offers_require').setStyle('display', 'block');
  }
});

function changeType($el)
{
  if ($el.value == 'paid') {
    $('price-wrapper').setStyle('display', '');
    $$('.admin_offers_require').setStyle('display', 'none');
  }
  else if ($el.value == 'free') {
    $('price-wrapper').setStyle('display', 'none');
    $$('.admin_offers_require').setStyle('display', 'none');
  }
  else if ($el.value == 'condition') {
    $('price-wrapper').setStyle('display', 'none');
    $$('.admin_offers_require').setStyle('display', 'block');
  }
}

function changeTimeLimit($el)
{
  if ($el.value == 'unlimit') {
    $('starttime-wrapper').setStyle('display', 'none');
    $('endtime-wrapper').setStyle('display', 'none');
  }
  else if ($el.value == 'limit') {
    $('starttime-wrapper').setStyle('display', 'block');
    $('endtime-wrapper').setStyle('display', 'block');
  }
}

function enableCouponsCount($el)
{
  if ($el.getProperty('checked')) {
    $('coupons_count').setProperty('disabled', '');
  }
  else {
    $('coupons_count').setProperty('disabled', 'disabled');
  }
}

function generateCouponsCode()
{
  var request = new Request.JSON (
  {
    secure: false,
    url: '<?php  echo $this->url(array("action" => "generate-coupons-code"), "offer_admin_manage", true)?>',
    method: 'post',
    data: {
      'format': 'json'
    },
    'onRequest':function() {
			$('generateCode_loading').setStyle('display', 'block');
		},
    onSuccess: function(response) {
      $('coupons_code').setProperty('value', response.code);
    },
    'onComplete':function() {
			$('generateCode_loading').setStyle('display', 'none');
		}
  }).send();
}

function checkInput(input, point)
{
  if (point == true) {
    input.value = input.value.replace(/[^\d.]/g, '');
  }
  else {
    input.value = input.value.replace(/[^\d]/g, '');
  }
}

function clearInput(element)
{
  element.set('value', '');
}
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
  <a class="offers_admin_manage_menu_items" href="<?php echo $this->url(array('action' => 'index'), 'offer_admin_manage', true);?>"><?php echo $this->translate('OFFERS_View Offers'); ?></a>
  <a class="offers_admin_manage_menu_items" href="<?php echo $this->url(array('action' => 'create'), 'offer_admin_manage', true);?>"><?php echo $this->translate('OFFERS_Create Offer');?></a>
  <span class="offers_admin_manage_menu_items active_item"><?php echo $this->translate('OFFERS_Edit Offer'); ?></span>
</div>
<div class="offers_create_container">
  <?php echo $this->form->render() ?>
</div>

<div id="editphotos-wrapper" class="form-wrapper">
<?php echo $this->htmlLink($this->url(array('action'=>'manage-photos', 'offer_id' => $this->offer_id), 'offer_admin_manage', true), $this->translate('OFFERS_Manage Photos'), array(
  'class' => 'buttonlink offer_photos_manage',
  'id' => 'editphotos',
)) ?>
</div>