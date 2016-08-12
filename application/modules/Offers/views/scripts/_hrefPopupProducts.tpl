<?php

  if (Engine_Api::_()->core()->hasSubject()) {
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->page_id) {
      $type = 'page';
      $id = $subject->page_id;
    } else {
      $type = 'user';
      $id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
  } else {
    $type = 'user';
    $id = Engine_Api::_()->user()->getViewer()->getIdentity();
  }

?>

<div id="popup_products-wrapper" class="form-wrapper">
  <div id="popup_products-label" class="form-label">
    <label class="optional" for="popup_products">
      <?php echo $this->translate("OFFERS_form_products"); ?>
    </label>
  </div>
  <div id="popup_products-description" class="form-element">
    <p><?php echo $this->translate('OFFERS_form_products_desc'); ?></p>
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('OFFERS_form_add_products'), array('onclick' => 'Offers.chooseProducts("'.$type.'", '.$id.');', 'id' => 'link_add_product')); ?>

      <div class="popup_product_selected"></div>
  </div>
</div>