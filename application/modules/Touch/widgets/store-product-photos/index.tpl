
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
<script type="text/javascript">
  en4.core.runonce.add(function(){
    var myElement = document.body.getElement('.product-item');
    var interv = window.setInterval(function(){
      if(!$type(myElement)){
        myElement = document.body.getElement('.product-item');
      } else{
        clearInterval(interv);
        new TouchImageZoom(myElement);
      }
    }, 500);
    });
</script>

<div>
	<span class="product-item" id="thumbs-photo">
  <?php foreach( $this->paginator as $key => $photo ): ?>
    <a id = 'photo_<?php echo $key+1; ?>' style="text-align: left;" rel="thumbs-photo[<?php echo $this->product->getTitle()?>]" title="<?php echo ($photo->title) ? '<b>'.$photo->title.'</b>: '.$photo->description : 'no title'; ?>" href="<?php echo $photo->getPhotoUrl(); ?>" onclick="return false">
      <img src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" class="thumbs">
    </a>
  <?php endforeach; ?>
	</span>
</div>
<br />