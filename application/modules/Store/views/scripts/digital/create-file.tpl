<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create-file.tpl  21.09.11 16:47 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
en4.core.runonce.add(function(){
  $('form-upload-file').setStyle('clear', 'none');

    $('link-wrapper').setStyle('display', 'none');
    $('file-wrapper').setStyle('display', 'none');

    var file = $$('.description')[0];
    var link = $$('.description')[1];
    file.addEvent('click', function(){
        link.removeClass('active');
        file.addClass('active');
        $('file-wrapper').setStyle('display', 'block');
        $('link-wrapper').setStyle('display', 'none');
        $('submit-wrapper').setStyle('display', 'none');
    });
    link.addEvent('click', function(){
        file.removeClass('active');
        link.addClass('active');
        $('link-wrapper').setStyle('display', 'block');
        $('file-wrapper').setStyle('display', 'none');
        $('submit-wrapper').setStyle('display', 'block');
    });

});
</script>

<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline store">
  <h2><?php echo $this->translate('STORE_Manage Products');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>


<div class="he-items" style="float: right; margin: 30px 0">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'page_id' => $this->page->getIdentity(),'product_id' => $this->product->getIdentity()), 'store_products'), $this->translate('Back'), array(
            'class' => 'buttonlink product_back',
            'id' => 'store_product_editsettings',
          )) ?>
          <br>
      </div>
    </li>
  </ul>
</div>

<?php echo $this->form->render($this) ?>