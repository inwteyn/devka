<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Offers
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: gateway.tpl 9637 2012-09-01 22:37:58Z taalay $
 * @author     TJ
 */
?>

<script type="text/javascript">
  function purchaseViaCredit() {
    var $url = '<?php echo $this->url(array('module' => 'credit', 'controller' => 'buy-offer', 'action' => 'index', 'offer_id' => $this->offer->getIdentity()), 'default', true)?>';
    window.location.href = $url;
  }
</script>

<form method="get" action="<?php echo $this->escape($this->url(array('action' => 'process'))) ?>"
      class="global_form" enctype="application/x-www-form-urlencoded">
  <div>
    <div>
      <h3>
        <?php echo $this->translate('OFFERS_Purchase Offer') ?>
      </h3>
      <p class="form-description">
        <?php echo $this->translate('OFFERS_Purchase Offer Gateways Description') ?>
      </p>
      <div class="form-elements">
        <div id="buttons-wrapper" class="form-wrapper">
          <?php foreach( $this->gateways as $gatewayInfo ):
            $gateway = $gatewayInfo['gateway'];
            $first = ( !isset($first) ? true : false ); ?>
            <?php if( !$first ): ?>
              <?php echo $this->translate('or') ?>
            <?php endif; ?>
            <button type="submit" name="execute" onclick="$('gateway_id').set('value', '<?php echo $gateway->gateway_id ?>')">
              <?php echo $this->translate('Pay with %1$s', $this->translate($gateway->getTitle())) ?>
            </button>
          <?php endforeach; ?>
          <?php if ($this->offer->isOfferCredit()) : ?>
            <?php if (isset($first)) : ?>
              <?php echo $this->translate('or') ?>
            <?php endif; ?>
            <button type="submit" name="execute" onclick="purchaseViaCredit(); return false;">
              <?php echo $this->translate('Pay with %1$s', $this->translate('Credits')) ?>
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <input type="hidden" name="gateway_id" id="gateway_id" value="" />
</form>