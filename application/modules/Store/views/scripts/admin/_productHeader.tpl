<?php $product = $this->product; ?>
<script type="text/javascript">
  window.addEvent('domready', function() {
    //$('admin-product-section-title').set('html', '<?php //echo $this->section_title; ?>');
  });
</script>

<?php echo $this->content()->renderWidget('store.admin-main-menu', array('active'=>$this->activeMenu)); ?>

<table class="admin-edit-product-header">
  <tr>
    <td id="product_preview" width=120px"
        class="product_preview"><?php echo $this->htmlLink($product->getHref(), $this->itemPhoto($product, 'thumb.normal')) ?></td>
    <td class="product-header-title">
      <h3><?php echo $product->getTitle(); ?></h3>
      <!--<h4 id="admin-product-section-title"></h4>-->
    </td>
    <td>
      <table class="product-nav-controls" align="right">
        <tr>
          <td style="text-align: right;">
            <button onclick="location.href='<?php echo $this->url(array(
              'module' => 'store',
              'controller' => 'products',
              'action' => 'copy',
              'product_id' => $product->getIdentity()
            ), 'admin_default', 1); ?>';">
              <?php echo $this->translate('STORE_Copy product'); ?>
            </button>
            <button onclick="window.open('<?php echo $product->getHref(); ?>');"
                    title="<?php echo $product->getTitle(); ?>">
              <?php echo $this->translate('STORE_View Product'); ?>
            </button>

          </td>
        </tr>
        <tr>
          <td style="text-align: right; padding-top: 10px;">
            <?php if ($this->prev): ?>
              <button title="<?php echo $this->translate('STORE_Prev product %s', $this->prev->getTitle()); ?>"
                      onclick="location.href='<?php echo $this->prevHref; ?>'">
                <i class="hei-chevron-left"></i>
              </button>
            <?php endif; ?>

            <?php if ($this->next): ?>
              <button title="<?php echo $this->translate('STORE_Next product %s', $this->next->getTitle()); ?>"
                      onclick="location.href = '<?php echo $this->nextHref; ?>';">
                <i class="hei-chevron-right"></i>
              </button>
            <?php endif; ?>
          </td>
        </tr>
      </table>


    </td>
  </tr>
</table>