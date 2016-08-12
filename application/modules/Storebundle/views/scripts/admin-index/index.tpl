<?php
  $this->headScript()->prependFile('application/modules/Storebundle/externals/scripts/admin/core.js');
?>
<script>
  en4.core.runonce.add(function () {
    $(document.body).addEventListener('click', function(e) {
      var cl = $(e.target).get('class');
      if(!$(e.target).hasClass('storebundle-completer-item') && !$(e.target).hasClass('item-wrapper')
        && !$(e.target).hasClass('item-title') && !$(e.target).hasClass('item-image')) {
        StorebundleCore.hideCompleter();
      }
    });
    StorebundleCore.init();
    StorebundleCore.products = JSON.parse('<?php echo $this->products; ?>');
    StorebundleCore.productsCount = new Number('<?php echo $this->productsCnt; ?>');

    StorebundleCore.completerUrl = '<?php echo $this->url(array(
    'module'=>'storebundle', 'controller'=>'index', 'action'=>'completer'), 'admin_default', 1); ?>';

    StorebundleCore.enableUrl = '<?php echo $this->url(array(
    'module'=>'storebundle', 'controller'=>'index', 'action'=>'enable'), 'admin_default', 1); ?>';

    StorebundleCore.createUrl = '<?php echo $this->url(array(
    'module'=>'storebundle', 'controller'=>'index', 'action'=>'create'), 'admin_default', 1); ?>';

    StorebundleCore.editUrl = '<?php echo $this->url(array(
    'module'=>'storebundle', 'controller'=>'index', 'action'=>'edit'), 'admin_default', 1); ?>';

    StorebundleCore.deleteUrl = '<?php echo $this->url(array(
    'module'=>'storebundle', 'controller'=>'index', 'action'=>'delete'), 'admin_default', 1); ?>';

    StorebundleCore.deleteProductUrl = '<?php echo $this->url(array(
    'module'=>'storebundle', 'controller'=>'index', 'action'=>'delete-product'), 'admin_default', 1); ?>';


    StorebundleCore.listUrl = '<?php echo $this->url(array(
    'module'=>'storebundle', 'controller'=>'index'), 'admin_default', 1); ?>';

    $('create-bundle').addEventListener('click', function () {
      StorebundleCore.showCreateForm();
    });
  });

 
</script>

<h2><?php echo $this->translate("Store Addons") ?></h2>

<?php echo $this->content()->renderWidget('store.admin-main-menu', array('active' => $this->activeMenu)); ?>

<?php echo $this->content()->renderWidget('store.admin-addons-menu', array('active' => $this->activeAddonMenu)); ?>

<div style="float: left; width: 70%;">
  <div class="storebundle-admin-buttons">
    <button class="storebundle-admin-button" id="back-button"
            style="display: none;" onclick="StorebundleCore.list();">
      <?php echo $this->translate('STOREBUNDLE_Back To List'); ?></button>
    <button class="storebundle-admin-button" id="create-bundle"><?php echo $this->translate('STOREBUNDLE_Add'); ?></button>
    <div style="clear: both"></div>
  </div>

  <div id="storebundle-content-wrapper">
    <?php echo $this->partial('admin-index/list.tpl', array('items' => $this->items));?>
  </div>
</div>


<?php echo $this->action("frame","index","hecore"); ?>
