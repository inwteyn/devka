<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>
<div class="profile_photo_page" style="position: relative;">
  <?php echo $this->htmlLink( $this->subject()->getHref(), $this->itemPhoto($this->subject(), 'thumb.profile') ); ?>
    <?php if($this->subject()->featured):?>
        <div class="page_featured" >
            <span><?php echo $this->translate('Featured')?></span>
        </div>
    <?php endif;?>
    <?php if( $this->subject()->sponsored ) :?>
        <div class="sponsored_page"><?php echo $this->translate('SPONSORED')?></div>
    <?php endif;?>

</div>
<div class="icons">
    <?php if ( $this->subject()->isStore()) : ?>
        <img class="page-icon" src="application/modules/Page/externals/images/store_mini.png" title="<?php echo $this->translate('STORE_Store'); ?>">
    <?php endif; ?>
    <!--	--><?php //echo $this->htmlImage("application/modules/Page/externals/images/featured".$this->subject()->featured.".png",
    //		$this->translate('PAGE_page_featured'.$this->subject()->featured),
    //		array(
    //		 'class'=>'page-icon',
    //			'title' => $this->translate('PAGE_page_featured'.$this->subject()->featured)
    //		)); ?>


    <!--	--><?php //echo $this->htmlImage("application/modules/Page/externals/images/sponsored".$this->subject()->sponsored.".png",
    //		$this->translate('PAGE_page_sponsored'.$this->subject()->sponsored),
    //		array(
    //		 'class'=>'page-icon',
    //			'title' => $this->translate('PAGE_page_sponsored'.$this->subject()->sponsored)
    //		)); ?>
</div>