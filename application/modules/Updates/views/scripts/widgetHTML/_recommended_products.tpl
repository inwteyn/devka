<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2011-10-06 16:56 mirlan $
 * @author     Mirlan
 */
?>
<?php
  $priceColor = Engine_Api::_()->getApi('settings', 'core')->__get('store.price.color');
  $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>

<?php if ($this->step == 'thumb'): ?>

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;width:48px;height:48px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.icon'); ?>
  </a>

<?php elseif($this->step == 'details'): ?>
  <div>
    <a href="<?php echo $this->item->getHref(); ?>" style="text-decoration:none; color:<?php echo $this->linkColor;?>">
      <?php echo $this->item->getTitle(); ?>
    </a>
    <span>
      <?php if ( $this->item->sponsored) : ?>
        <img src="/application/modules/Store/externals/images/sponsored.png" title="<?php echo $this->translate('STORE_Sponsored'); ?>" height="14px"/>
      <?php endif; ?>
      <?php if ($this->item->featured) : ?>
        <img src="/application/modules/Store/externals/images/featured.png" title="<?php echo $this->translate('STORE_Featured'); ?>" height="14px"/>
      <?php endif; ?>
    </span>
  </div>

  <div>
    <b><?php echo $this->translate('STORE_Price'); ?>:
      <?php if ( isset($this->item->price_type) && $this->item->price_type == 'discount'): ?>
        <span style='text-decoration: line-through'><?php echo $this->locale()->toCurrency((double)$this->item->list_price, $currency); ?></span>
      <?php endif; ?>
      <span style='color: <?php echo $priceColor; ?>'><?php echo $this->locale()->toCurrency((double)$this->item->price, $currency); ?></span>
    </b>
  </div>
  
  <div style="font-size: 10px">
    <?php if( $this->item->hasStore() ): ?>
      <?php echo $this->translate('STORE_Store').': '; ?>
      <a href="<?php echo $this->item->getStore()->getHref(); ?>" style="text-decoration:none; color:<?php echo $this->linkColor;?>">
        <?php echo $this->item->getStore()->getTitle(); ?>
      </a>
    <?php endif; ?>
      <?php echo $this->translate('Posted').': '; ?>
    <?php echo $this->timestamp($this->item->creation_date); ?><br>
  </div>

  <?php if (!empty($this->item->description)): ?>
  <div style="margin-top:5px; font-size: 11px;">
    <?php echo Engine_String::substr(Engine_String::strip_tags($this->item->description), 0, 90);
          echo (Engine_String::strlen($this->item->description)>89)? "...":'';
    ?>
  </div>
  <?php endif; ?>

<?php elseif($this->step == 'more_link'): ?>
  
  <div align="right">
    <a href="<?php echo $this->url(array('module'=>'store', 'controller'=>'index', 'action'=>'products'), 'default', true) ?>" style="text-decoration:underline;padding-top: 5px;color:<?php echo $this->linkColor;?> ">
      <?php echo $this->translate('UPDATES_More products...');?>
    </a>
  </div>

<?php endif; ?>