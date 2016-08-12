<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $id: create-audio.tpl  09.09.11 17:39 taalay $
 * @author     Taalay
 */
?>


<?php

$modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
$coreItem = $modulesTbl->getModule('core')->toArray();
if(version_compare($coreItem['version'], '4.8.10')>=0){
    $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.13.min.js');

}else{
    $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
}

	$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')

?>

<script type="text/javascript">
var product_id = <?php echo $this->product->getIdentity() ?>;

if (product_id > 0) {
  $$('#product_id option').each(function(el, index) {
    if (el.value == product_id)
      $('product_id').selectedIndex = index;
  });
}

en4.core.runonce.add(function(){
  $('form-upload-audio').setStyle('clear', 'none');
});
</script>

<?php echo $this->render('admin/_productHeader.tpl'); ?>

<div>

  <div style="float: left;">
    <?php echo $this->getGatewayState(0); ?>

    <?php
    /* Include the common user-end field switching javascript */
    echo $this->partial('_jsSwitch.tpl', 'fields', array(
      //'topLevelId' => (int) @$this->topLevelId,
      //'topLevelValue' => (int) @$this->topLevelValue
    ))
    ?>

    <div class="settings">
      <?php echo $this->form->render($this); ?>
    </div>
  </div>
  <div style="float: right;">
    <?php echo $this->render('admin/_productsMenu.tpl'); ?>
  </div>

</div>