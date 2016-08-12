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
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

?>

<?php if ($this->step == 'thumb'){ ?>

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;width:48px;height:48px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.icon'); ?>
  </a>

<?php }elseif($this->step == 'details'){ ?>
  <div>
    <a href="<?php  echo $this->item->getHref(); ?>" style="text-decoration:none;">
      <?php echo $this->item->getTitle(); ?>
    </a>
  </div>

  <div>
    <b>
      <span><?php echo $this->locale()->toCurrency((double)$this->item->price, $currency); ?></span>
    </b>
  </div>
  
  <div style="font-size: 10px">
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

<?php }elseif($this->step == 'more_link'){ ?>

  <div align="right">
    <a href="<?php echo $this->url(array('module'=>'sitereview', 'controller'=>'index', 'action'=>'home','listingtype_id'=>$this->item->listingtype_id), 'default', true) ?>" style="text-decoration:underline;padding-top: 5px;color:<?php echo $this->linkColor;?> ">
      <?php echo $this->translate('UPDATES_More products...');?>
    </a>
  </div>

<?php }; ?>