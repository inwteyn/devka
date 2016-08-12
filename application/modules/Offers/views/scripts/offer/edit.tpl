<script type="text/javascript">
  window.edit_offer = true;
  en4.core.runonce.add(function(){

    var oftype = "<?php echo $this->type ?>";
    var element = $('oftype_'+ oftype);
    var enableUniqueCode = <?php echo $this->enable_unique_code; ?>;

    if ($('coupons_count').get('value') > '0') {
      $('enable_coupon_count').set('checked', 'checked');
      $('coupons_count-wrapper').setStyle('display', 'block');
    }

    if (!enableUniqueCode) {
      var elementRadioBt = $('type_code-offercode');
      elementRadioBt.set('checked', true);
      selectTypeCode(elementRadioBt);
    }

    if ($('starttime').get('value').indexOf('000') === -1 && $('starttime').get('value')) {
      $('enable_time_left').set('checked', 'checked');
      $('starttime-wrapper').setStyle('display', 'block');
      $('endtime-wrapper').setStyle('display', 'block');
    }

    if ($('redeem_starttime').get('value').indexOf('000') === -1 && $('redeem_starttime').get('value')) {
      $('enable_redeem_time').set('checked', 'checked');
      $('redeem_starttime-wrapper').setStyle('display', 'block');
      $('redeem_endtime-wrapper').setStyle('display', 'block');
    }

    $('enable_coupons_code').set('checked', 'checked');
    $('coupons_code-wrapper').setStyle('display', 'block');

    Offers.list_param.checked_products = "<?php echo (isset($this->products_ids)) ? $this->products_ids : '' ?>";

      Offers.formFilter(element, oftype);
  });
</script>

<div id="offer_edit">
  <div class="offers_form_container">
    <?php echo $this->render('form.tpl'); ?>
  </div>
  <div class="offers_navigation_editor tabs">
    <?php
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation_edit)
        ->setPartial(array('_navIcons.tpl', 'core'))
        ->render();
    ?>
  </div>
</div>