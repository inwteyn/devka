<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    AvatarStyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: choose-offer.php 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    AvatarStyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>

<script type="text/javascript">
  var changePage = function(page)
  {
    $('socialboost_admin_loader').setStyle('display', 'block');
    var request = new Request.HTML({
      url: '<?php echo $this->url(array('module' => 'social-boost', 'controller' => 'index', 'action' => 'choose-offer'), 'admin_default')?>',
      data: {page:page},
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
        var tElement = new Element('div', {'html': responseHTML});
        console.log(tElement);
        var el = tElement.getElement('div.socialboost_middle_container');
        console.log(tElement.getElement('div.socialboost_middle_container'));
        $('socialboost_middle_container').innerHTML = el.innerHTML;
      }
    }).get();
  }
</script>

<?php if (count($this->paginator) > 0): ?>
  <div id="socialboost_middle_container" class="socialboost_middle_container">
    <form class="socialboost_offers" id="socialboost_offers" method="post" xmlns="http://www.w3.org/1999/html">
      <ul class="admin_offers">
        <?php foreach($this->paginator as $offer): ?>
          <input type="radio" value="<?php echo $offer->getIdentity();?>" name="offer_id" class="offers_radio" <?php if($offer->getIdentity() == $this->offer_id) echo ' checked'?>>
          <li id="offer_id_<?php echo $offer->offer_id;?>" class="offer_item">
            <div class="offer_photo">
              <?php echo $this->itemPhoto($offer, 'thumb.icon')?>
            </div>
            <div class="offer_info">
              <div class="offer_title">
                <h3><?php echo $this->htmlLink($offer->getHref(), $this->string()->truncate($offer->getTitle(), 20)); ?></h3>
              </div>
            </div>
            <div class="offer_options">
              <div class="offer_discount"><label><?php echo $this->translate('OFFERS_offer_discount'); ?></label> <span><?php echo $this->getOfferDiscount($offer); ?></span></div>
                <?php if(Engine_Api::_()->offers()->availableOffer($offer, true) != 'Unlimit'): ?>
                  <div class="offer_time_left"><label><?php echo $this->translate('OFFERS_offer_time_left'); ?></label> <span><?php echo Engine_Api::_()->offers()->availableOffer($offer, true);?></span></div>
                <?php else: ?>
                  <?php if(!$offer->coupons_unlimit): ?>
                    <div class="offer_count"><label><?php echo $this->translate('OFFERS_offer_available'); ?></label><span> <?php echo $this->translate('%s coupons', $offer->coupons_count); ?></span></div>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <button type="submit"><?php echo $this->translate('Save');?></button>
    </form>
    <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl","social-boost")); ?>
    <div class="socialboost_admin_loader" id="socialboost_admin_loader">
      <span style="background-image: url('application/modules/Core/externals/images/loading.gif')"></span>
    </div>
  </div>
<?php else:?>
  <div>
     <span>
       <?php echo $this->translate('OFFERS_No upcoming items');?>
     </span>
  </div>
<?php endif;?>