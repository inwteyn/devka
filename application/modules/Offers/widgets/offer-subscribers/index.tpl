<div>
  <?php if(count($this->subscribers)): ?>
    <?php
      $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Offers/externals/scripts/core.js');

      $this->headTranslate(array(
        'OFFERS_change_status_coupon'
      ));
    ?>

    <script type="text/javascript">
      en4.core.runonce.add(function(){
        offers_manager.changeStatusCouponUrl = '<?php  echo $this->url(array("action" => "change-status-coupon"), "offers_general")?>';
      });
    </script>
    <div class="list_subscribers">
      <table class="table_list_subscribers">
        <thead>
          <tr>
            <th class="title_name_subscriber"><?php echo $this->translate('OFFERS_name_subscribe'); ?></th>
            <th class="title_time_acquisition table_list_subscribers_centered"><?php echo $this->translate('OFFERS_time_acquisition'); ?></th>
            <th class="title_status_coupon table_list_subscribers_centered"><?php echo $this->translate('OFFERS_title_status_coupon'); ?></th>
            <th class="title_coupon_code table_list_subscribers_centered"><?php echo $this->translate('OFFERS_title_coupon_code'); ?></th>
            <th class="offer_subscriber_option table_list_subscribers_centered"><?php echo $this->translate('OFFERS_option_subscribe'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($this->subscribers as $subscriber): ?>
            <tr>
              <td><?php echo $this->htmlLink($subscriber->getHref(), $subscriber->getTitle()); ?></td>
              <td class="table_list_subscribers_centered"><?php echo $subscriber->creation_date; ?></td>
              <td class="table_list_subscribers_centered"><?php echo $subscriber->status; ?></td>
              <td class="table_list_subscribers_centered"><?php echo $this->offer->getCouponCode($subscriber->getIdentity()); ?></td>
              <td>
                <a href="javascript:void(0);" onclick="offers_manager.changeStatusCoupon('<?php echo $this->offer->getIdentity(); ?>', this, <?php echo $subscriber->getIdentity(); ?>)" class="buttonlink mark_as_used" id="mark_as_used_<?php echo $subscriber->offer_id; ?>">
                  <?php if($subscriber->status == 'active'): ?>
                    <?php echo $this->translate('OFFERS_change_status_coupon', 'Used'); ?>
                  <?php else: ?>
                    <?php echo $this->translate('OFFERS_change_status_coupon', 'Active'); ?>
                  <?php endif; ?>
              </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
  <div class="total_subscribers">
    <span><?php echo $this->translate('OFFERS_total_subscribers'); ?></span>
    <?php echo count($this->subscribers); ?>
  </div>
</div>