<?php if (count($this->paginator)): ?>
  <?php foreach ($this->paginator as $item): ?>
    <?php $canEdit = ($this->viewer->getIdentity() == $item->owner_id); $editLink = ''; $copyLink = '';?>
    <?php $titleLink = $item->getHref(); ?>
    <?php if ($canEdit): ?>
      <?php if ($item->hasStore()): ?>
        <?php
        $titleLink = $this->url(array('action' => 'edit', 'page_id' => $item->getStore()->getIdentity(), 'product_id' => $item->getIdentity()), 'store_products');
        $editLink = $this->htmlLink(
          $this->url(array('action' => 'edit', 'page_id' => $item->getStore()->getIdentity(), 'product_id' => $item->getIdentity()), 'store_products'),
          '<img title="' . $this->translate('Edit') . '" class="product-icon" src="application/modules/User/externals/images/edit.png">',
          array('target' => '_blank')
        );
        $copyLink = $this->htmlLink(
          $this->url(array('action' => 'copy', 'page_id' => $item->getStore()->getIdentity(), 'product_id' => $item->getIdentity()), 'store_products'),
          '<img title="' . $this->translate('STORE_Copy Product') . '" class="product-icon" src="application/modules/Store/externals/images/copy_product.png">',
          array('target' => '_blank')
        );
        ?>
      <?php else : ?>
        <?php
        $titleLink = $this->url(array('action' => 'edit', 'product_id' => $item->getIdentity()));
        $editLink = $this->htmlLink(
          $this->url(array('action' => 'edit', 'product_id' => $item->getIdentity())),
          '<img title="' . $this->translate('Edit') . '" class="product-icon" src="application/modules/User/externals/images/edit.png">',
          array('target' => '_blank')
        );
        $copyLink = $this->htmlLink(
          $this->url(array('action' => 'copy', 'product_id' => $item->getIdentity())),
          '<img title="' . $this->translate('STORE_Copy Product') . '" class="product-icon" src="application/modules/Store/externals/images/copy_product.png">',
          array('target' => '_blank')
        );
        ?>
      <?php endif; ?>
    <?php endif; ?>

    <tr class="<?php if (($item->hasStore() && !$item->getStore()->isStore()) || !$item->getQuantity())
      echo 'disabled-product' ?>">
      <td>
        <input
          name="modify[]"
          value=<?php echo $item->getIdentity(); ?>
          type='checkbox' class='checkbox'>
      </td>
      <td>
        <div class="store-products-list-thumb"
             style="background-image: url('<?php $product = Engine_Api::_()->getItem('store_product', $item->getIdentity()); echo $product->getPhotoUrl(); ?>');">
      </td>
      <td class='admin_table_bold'>
        <?php echo $this->htmlLink($titleLink,
          $this->string()->truncate($item->getTitle(), 15),
          array('title' => $item->getTitle(), 'target' => '_blank'))?>
      </td>
      <td><?php echo($item->category ? $item->category : ("<i>" . $this->translate("Uncategorized") . "</i>")); ?></td>
      <?php if ($this->storeEnabled): ?>
        <td
          class='admin_table_store'><?php if ($item->hasStore()): ?><?php echo $this->htmlLink($item->getStore()->getHref(), $item->getStore()->getTitle(), array('target' => '_blank')) ?><?php endif; ?></td>
      <?php endif; ?>
      <td
        class='admin_table_owner'><?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target' => '_blank')) ?></td>
      <td class="center"><?php echo $this->getPrice($item); ?></td>
      <td
        class='center'><?php echo ($item->getQuantity() === true) ? $this->translate('STORE_Digital') : $item->getQuantity(); ?></td>
      <td class="center"><?php echo $product->creation_date ?></td>
      <td class='center admin_table_options'>
        <img data-type="sponsored"
             data-id="<?php echo $item->getIdentity(); ?>"
             title="<?php $this->translate('STORE_sponsored' . $item->sponsored); ?>"
             class="options_img product-icon"
             src="application/modules/Store/externals/images/admin/sponsored<?php echo $item->sponsored; ?>.png">

        <img data-type="featured"
             data-id="<?php echo $item->getIdentity(); ?>"
             title="<?php $this->translate('STORE_featured' . $item->featured); ?>"
             class="options_img product-icon"
             src="application/modules/Store/externals/images/admin/featured<?php echo $item->featured; ?>.png">

        <?php if ($canEdit): ?>
          <?php echo $editLink; echo $copyLink; ?>
        <?php endif; ?>
        <?php echo $this->htmlLink(
          'javascript:void(0)',
          '<img title="' . $this->translate('Delete') . '" class="product-icon" src="application/modules/Core/externals/images/delete.png">',
          array('onClick' => "confirmDelete({$item->getIdentity()})"))
        ?>
      </td>
    </tr>
  <?php endforeach; ?>
  <tr>
    <td colspan="9">
      <?php echo $this->paginationControl(
        $this->paginator,
        null,
        array("admin-products/pagination.tpl", "store")
      ); ?>
      <input type="hidden" id="page" value="<?php /*echo $this->page; */?>">
    </td>
  </tr>
<?php endif; ?>