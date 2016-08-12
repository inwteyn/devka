<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

$this->headTranslate(array(
  '%s items selected'
));
?>
<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';

  var changeOrder = function (order, default_direction) {
    var direction = '';
    if (order == currentOrder) {
      direction = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      direction = default_direction;
    }
    currentOrder = order;
    currentOrderDirection = direction;

    $('order_direction').value = direction;
    $('search').click();
  }

  function multiModify() {
    return confirm('<?php echo $this->string()->
      escapeJavascript($this->translate("Are you sure you want to delete the selected pages?")) ?>');
  }

  function selectAll() {
    var i;
    var multimodify_form = $('multimodify_form');
    var inputs = multimodify_form.elements;
    var cnt = 0;
    for (i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        inputs[i].checked = inputs[0].checked;
      }
    }
    $('selected-label').set('html',
      en4.core.language.translate(['%s item selected', '%s items selected', cnt], cnt)
    );
  }
  function getSelectedCount() {
    var multimodify_form = $('multimodify_form');
    var inputs = multimodify_form.elements;
    var cnt = 0;
    for (var i = 1; i < inputs.length; i++) {
      if (!inputs[i].disabled) {
        if (inputs[i].checked)
          cnt++;
      }
    }
    return cnt;
  }
  function updateSelected() {
    var cnt = getSelectedCount();
    var text = (cnt > 0) ? en4.core.language.translate(['%s item selected', '%s items selected', cnt], cnt) : '';
    $('selected-label').set('html', text);
  }

  function confirmDelete(product_id) {
    if (confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this product?")) ?>')) {
      window.location.href = '<?php echo $this->url(array('module' => 'store', 'controller' => 'products', 'action' => 'delete'),
        'admin_default', true); ?>/product_id/' + product_id;
    } else {
      return false;
    }
  }

  function confirmActions() {
    var cnt = getSelectedCount();
    if (cnt <= 0) return;

    var action = $('products-actions').value;
    var formData = collectFormData('multimodify_form');
    formData.act = action;

    if (action == 5) {
      if (confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete this product?")) ?>')) {
        updatePage(formData);
      } else {
        return false;
      }
    } else {
      updatePage(formData);
    }
  }
  function collectFormData(id) {
    try {
      var data = $(id).toQueryString().parseQueryString();
      if ($('page')) {
        data.page = $('page').value;
      }
      data.format = 'json';
      return data;
    } catch (e) {
      console.log('Collect search arams error - ' + e);
      return false;
    }
  }

  function changePage(page) {
    var data = collectFormData('filter_form');
    data.page = page;

    updatePage(data);
  }

  function showLoader(id) {
    $(id).setStyle('display', '');
  }
  function hideLoader(id) {
    $(id).setStyle('display', 'none');
  }

  function updatePage(data) {
    showLoader('actions-loader');

    new Request.JSON({
      url: '<?php echo $this->url(array('module'=>'store', 'controller'=>'products'), "admin_default", 1)?>',
      method: 'post',
      data: data,
      evalScripts: false,
      //onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
      onSuccess: function (response) {
        $('store-products-list').set('html', response.html);

        hideLoader('actions-loader');
        initEvents();

        $('selectAll_check').checked = false;
        updateSelected();
      },
      onError: function (text, error) {
        hideLoader('actions-loader');
      },
      onFailure: function (response) {
        hideLoader('actions-loader');
      }
    }).send();
  }

  en4.core.runonce.add(function () {
    initEvents();
    $('search').removeEvent('click').addEvent('click', function () {
      var formData = collectFormData('filter_form');
      if (formData) {
        updatePage(formData);
      }
      return false;
    });

  });

  function initEvents() {
    var size = $('multimodify_form').getSize();
    $('actions-loader').setStyle('width', size.x);
    $('actions-loader').setStyle('height', size.y);

    $$('.checkbox').removeEvent('click').addEvent('click', function () {
      updateSelected();
    });

    $$('.options_img').removeEvent('click').addEvent('click', function () {
      var self = this;
      var id = $(this).get('data-id');
      var type = $(this).get('data-type');
      new Request.JSON({
        url: '<?php echo $this->url(array('module'=>'store', 'controller'=>'products', 'action'=>'options'), 'admin_default', 1); ?>',
        data: {
          product_id: id,
          type: type,
          format: 'json'
        },
        onSuccess: function (response) {
          if (response.status) {
            $(self).set('src', 'application/modules/Store/externals/images/admin/' + type + response.result + '.png');
          }
        }
      }).send();
    });
  }

</script>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php echo $this->content()->renderWidget('store.admin-main-menu', array('active' => $this->activeMenu)); ?>
<p>
  <?php echo $this->translate("STORE_VIEWS_SCRIPTS_ADMINPINDEX_PRODUCTS_DESCRIPTION") ?>
</p>

<br/>

<div class='admin_search'>
  <?php echo $this->filterForm->render($this); ?>
</div>

<br/>

<div>
  <select onchange="confirmActions();" id="products-actions" name="actions">
    <option value="0"></option>
    <option value="1">Set selected as featured</option>
    <option value="2">Set selected as sponsored</option>
    <option value="3">Set selected as not featured</option>
    <option value="4">Set selected as not sponsored</option>
    <option value="5">Delete selected</option>
  </select>
  <label for="products-actions" id="selected-label"></label>
</div>
<br/>

<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action' => 'multi-modify')); ?>"
      onSubmit="return multiModify()">
  <div id="actions-loader" style="display: none;"></div>
  <table class='admin_table page_packages'>
    <thead>
    <tr>
      <th><input id="selectAll_check" onclick="selectAll()" type='checkbox' class='checkbox'></th>
      <th class='admin_table_short'>
        <a id="products-order" href="javascript:void(0);"
           onclick="javascript:changeOrder('p.product_id', 'DESC');"><?php echo $this->translate("Product") ?>
        </a>
        <!--<span id="products-order-icon" class="hei-sort-<?php /*echo strtolower($this->order_direction); */?>"></span>-->
      </th>
      <th><a href="javascript:void(0);"
             onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Title") ?></a></th>
      <th><a href="javascript:void(0);"
             onclick="javascript:changeOrder('category', 'ASC');"><?php echo $this->translate("STORE_Category") ?></a>
      </th>
      <?php if ($this->storeEnabled): ?>
        <th><a href="javascript:void(0);"
               onclick="javascript:changeOrder('s.title', 'ASC');"><?php echo $this->translate("Store") ?></a></th>
      <?php endif; ?>
      <th><a href="javascript:void(0);"
             onclick="javascript:changeOrder('u.username', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>
      <th class="admin_table_centered"><a href="javascript:void(0);"
                                          onclick="javascript:changeOrder('p.price', 'ASC');"><?php echo $this->translate("Price") ?></a>
      </th>
      <th class="admin_table_centered"><a href="javascript:void(0);"
                                          onclick="javascript:changeOrder('p.quantity', 'ASC');"><?php echo $this->translate("Amount") ?></a>
      </th>
      <th class="center"><a href="javascript:void(0);"
                            onclick="javascript:changeOrder('p.creation_date', 'DESC');"><?php echo $this->translate("Date") ?></a>
      </th>
      <th class='center admin_table_options'><?php echo $this->translate("Options") ?></th>
    </tr>
    </thead>
    <tbody id="store-products-list">
    <?php echo $this->render('admin-products/_store_list_edit.tpl'); ?>
    </tbody>
  </table>
  <br/>
</form>
