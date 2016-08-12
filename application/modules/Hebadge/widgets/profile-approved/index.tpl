<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<?php if ($this->member):?>
    
  <a href="javascript:void(0)" class="hebadge-button hebadge-button-approved <?php if($this->member->approved):?>active<?php endif;?>" onclick="Hebadge.request(en4.core.baseUrl+'badges/index/approved',{'approved':($(this).hasClass('active'))?0:1,'badge_id':<?php echo $this->subject()->getIdentity();?>},function(){});if($(this).hasClass('active')){$(this).removeClass('active');}else{$(this).addClass('active');}">
    <span class="wall_icon"></span>
    <?php echo $this->translate('HEBADGE_APPROVED')?>
  </a>

<?php endif; ?>