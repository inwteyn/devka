<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

?>
<div id="navigation_content">
  <div	class="search">
  	<?php echo $this->paginationControl(
  			$this->paginator,
  			null,
  			array('pagination/filter.tpl', 'touch'),
  			array(
  				'search'=>$this->formFilter->getElement('search')->getValue(),
  				'filter_default_value'=>$this->translate('STORE_Search Product'),
  				'filterUrl'=>$this->url(array('module'=>'store', 'controller'=>'index', 'action'=>'products', 'filter' => $this->filter), 'store_general', true)
  			)
  	); ?>
  </div>
  <div id="filter_block">
 	<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

     <ul class='items he-item-list'>
       <?php foreach( $this->paginator as $item ): ?>
         <li>
           <div class="item_photo">
             <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('class' => 'touchajax')) ?>
           </div>
           <div class="item_body he-item-info">
             <div class="he-item-title">
               <h4>
                 <span><?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->getTitle(), 20), array('class' => 'touchajax'))?></span>
               </h4>
               <div class="product-sponsored-featured">
                 <span>
                   <?php if ($item->sponsored) : ?>
                     <img class="icon" src="application/modules/Store/externals/images/sponsored.png" title="<?php echo $this->translate('STORE_Sponsored'); ?>">
                   <?php endif; ?>
                   <?php if ($item->featured) : ?>
                     <img class="icon" src="application/modules/Store/externals/images/featured.png" title="<?php echo $this->translate('STORE_Featured'); ?>">
                   <?php endif; ?>
                 </span>
               </div>
             </div>
             <div class="he-item-options store-item-options">
          				<?php echo $this->getPriceBlock( $item ); ?>
       			 </div>
             <div class="rating">
               <?php echo $this->itemRate($item->getType(), $item->getIdentity()); ?>
             </div>
             <div class="he-item-details">
                   <?php echo $this->translate('Category: '); ?>
                   <?php echo ((null !== ($category = $item->getCategory()->category)) ? $this->htmlLink($item->getCategoryHref(array('action'=>'products')), $this->translate($item->getCategory()->category), array('class' => 'touchajax')) : ("<i>".$this->translate("Uncategorized")."</i>")); ?><br>
                   <?php if( $item->hasStore() ): ?>
                     <?php echo $this->translate('STORE_Store').': '; ?>
                     <?php echo $this->htmlLink($item->getStore()->getHref(), $item->getStore()->getTitle(), array('target' => '_blank', 'class' => 'touchajax')) ?><br>
                   <?php endif; ?>
                   <?php echo $this->translate('Posted').': '; ?>
                   <?php echo $this->timestamp($item->creation_date); ?><br>
         		 </div>
             <div class="he-item-desc">
         					<?php echo $this->viewMore(Engine_String::strip_tags($item->getDescription()), 80) ?>
     				 </div>

           </div>
         </li>
       <?php endforeach; ?>
     </ul>

 	<?php else: ?>

     <div class="tip">
       <span>
         <?php echo $this->translate('There are no products.') ?>
       </span>
     </div>

 	<?php endif; ?>
 	</div>

</div>
