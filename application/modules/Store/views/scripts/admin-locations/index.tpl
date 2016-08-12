<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  3/22/12 2:03 PM mt.uulu $
 * @author     Mirlan
 */

echo $this->render('application/modules/Store/views/scripts/admin-locations/filter_script.tpl');
?>

<script type="text/javascript">

  function switchForm(flag) {
    if (!$('ww_check').checked) {
      $('ww_check').disabled = !$('ww_check').disabled;
      return;
    }

    $('admin-ww-table').getElements('input').each(function (el) {
      el.disabled = !el.disabled;
    });

  }
  function showLoader(loader, el) {
    switchForm(false);
    loader.setStyle('display', 'inline-block');
    el.setStyle('display', 'none');
  }
  function hideLoader(loader, el) {
    switchForm(true);
    loader.setStyle('display', 'none');
    el.setStyle('display', 'inline-block');
  }
  function markField(id) {
    var field = $(id);
    field.setStyle('background-color', 'salmon');
    setTimeout(function () {
      field.setStyle('background-color', '');
    }, 2000);
  }
  function showMessage(type) {
    var id = '';
    switch (type) {
      case 1:
        id = 'ww_message_success';
        break;
      case 2:
        id = 'ww_message_error';
        break;
      default:
        return;
    }

    var el = $(id);
    var parent = el.getParent();
    el.setStyle('display', '');
    parent.setStyle('display', '');

    setTimeout(function () {
      el.setStyle('display', 'none');
      parent.setStyle('display', 'none');
    }, 5000);
  }


  window.addEvent('domready', function () {
    $('ww_check').addEvent('click', function () {
      $('admin-ww-table').getElements('input[type=text]').each(function (el) {
        el.disabled = !el.disabled;
      });
    });

    $('ww_submit').addEvent('click', function () {
      var isValid = true;
      var self = this;
      var loader = $('ww-loader');

      var ww_price = Number($('ww_price').value);
      var ww_days = Number($('ww_days').value);
      var ww_tax = Number($('ww_tax').value);

      if (isNaN(ww_price) || ww_price == 0) {
        isValid = false;
        markField('ww_price');
      }
      if (isNaN(ww_days) || ww_days == 0) {
        isValid = false;
        markField('ww_days');
      }
      if (isNaN(ww_tax)) {
        isValid = false;
        markField('ww_tax');
      }

      if (!isValid) {
        return;
      }

      showLoader(loader, self);

      new Request.JSON({
        format: 'json',
        url: '<?php echo $this->url(array('module'=>'store', 'controller'=>'locations', 'action'=>'save-ww'), 'admin_default', 1); ?>',
        data: {
          ww_enabled: ($('ww_check').checked) ? 1 : 0,
          ww_price: ww_price,
          ww_days: ww_days,
          ww_tax: ww_tax
        },
        onSuccess: function (response) {
          showMessage(1);
        },
        onComplete: function () {
          hideLoader(loader, self);
        },
        onFailure: function (err) {
          showMessage(2);
        }

      }).send();
    });
  });
</script>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php echo $this->content()->renderWidget('store.admin-main-menu', array('active'=>$this->activeMenu)); ?>

<?php echo $this->render('admin/_locationsMenu.tpl'); ?>

<div class="admin_home_middle">
  <div class="settings">
    <h2>
      <?php echo $this->translate("STORE_Manage Supported Shipping Locations") ?>
    </h2>

    <p>
      <?php echo $this->translate('STORE_ADMIN_LOCATIONS_SUPPORTED_DESCRIPTION'); ?>
    </p>
    <br/>
    <br/>


    <?php if ($this->parent != null): ?>
      <div class="locations-tree">
      <span style="float:left">
      <?php echo $this->htmlLink(array(
        'reset' => true,
        'route' => 'admin_default',
        'module' => 'store',
        'controller' => 'locations',
      ), $this->translate('Locations')); ?>
      </span>

        <?php
        /**
         * @var $location Store_Model_Location
         */
        $location = $this->parent;
        do {
        ?>
        <span style="float:left">
      &nbsp;&#187;&nbsp;
          <?php echo $this->htmlLink(array(
            'reset' => true,
            'route' => 'admin_default',
            'module' => 'store',
            'controller' => 'locations',
            'parent_id' => $location->getIdentity(),
          ), $this->truncate($location->location)); ?>
          <?php $location = $location->getParent(); ?>
          <?php } while ($location != null); ?>
      </span>

      </div>
      <br/>
      <br/>
    <?php endif; ?>


    <div class="admin-ww-wrapper">
      <h3>
        <?php echo $this->translate('STORE_WW Location'); ?>
      </h3>

      <p><?php echo $this->translate('STORE_WW Location Description'); ?></p>

      <div style="display: none;">
        <ul class="success form-notices" id="ww_message_success" style="display: none;">
          <li>
            <?php echo $this->success_message; ?>
          </li>
        </ul>
        <ul class="error form-errors" id="ww_message_error" style="display: none;">
          <li>
            <?php echo $this->error_message; ?>
          </li>
        </ul>
      </div>

      <table class="admin-ww-table" id="admin-ww-table">
        <tr>
          <td><label for="ww_check"><?php echo $this->translate("STORE_WW Enabled") ?></label></td>
          <td><input type="checkbox" <?php echo ($this->ww_enabled) ? 'checked' : ''; ?> id="ww_check"></td>
        </tr>

        <tr>
          <td><?php echo $this->translate("STORE_Shipping Price") ?></td>
          <td><input type="text" name="ww_price" value="<?php echo $this->ww_price; ?>"
                     id="ww_price" <?php echo ($this->ww_enabled) ? '' : 'disabled'; ?>></td>
        </tr>

        <tr>
          <td><?php echo $this->translate("STORE_Shipping Days") ?></td>
          <td><input type="text" name="ww_days" value="<?php echo $this->ww_days; ?>"
                     id="ww_days" <?php echo ($this->ww_enabled) ? '' : 'disabled'; ?>></td>
        </tr>

        <tr>
          <td><?php echo $this->translate("STORE_Shipping Tax") ?></td>
          <td><input type="text" name="ww_tax" value="<?php echo $this->ww_tax; ?>"
                     id="ww_tax" <?php echo ($this->ww_enabled) ? '' : 'disabled'; ?>></td>
        </tr>

        <tr>
          <td colspan="2" style="text-align: right; height: 40px;">
            <div id="ww-loader" style="display: none;"></div>
            <button id="ww_submit">
              <?php echo $this->translate('STORE_WW Save'); ?>
            </button>
          </td>
        </tr>
      </table>
    </div>

    <?php if ($this->paginator->count() > 0): ?>

      <table class='admin_table'>
        <thead>
        <tr>
          <th style="width: 500px;"><?php echo $this->translate("STORE_Location Name") ?></th>
          <?php if ($this->parent_id == 0): ?>
            <th><?php echo $this->translate("STORE_Sub-Locations") ?></th>
          <?php endif; ?>
          <th><?php echo $this->translate("STORE_Shipping Price") ?></th>
          <th><?php echo $this->translate("STORE_Shipping Days") ?></th>
          <th><?php echo $this->translate("STORE_Shipping Tax") ?></th>
          <th class="center"><?php echo $this->translate("Options") ?></th>
        </tr>
        </thead>
        <tbody class="only-locations" id="only-locations">
        <?php foreach ($this->paginator as $item): ?>
          <tr>
            <td class='admin_table_bold' style="width: 500px;">
              <?php echo $this->truncate($item->location, 160); ?>
            </td>
            <?php if ($this->parent_id == 0): ?>
              <td class="center">
                <a href="<?php echo $this->url(array(
                  'module' => 'store',
                  'controller' => 'locations',
                  'action' => 'index',
                  'parent_id' => $item->getIdentity()), 'admin_default', true); ?>"><?php echo (int)$item->sub_locations; ?></a>
              </td>
            <?php endif; ?>
            <td class="center">
              <?php if (is_null($item->shipping_amt)): ?>
                <?php echo $this->translate('STORE_Only Sub-Locations'); ?>
              <?php else: ?>
                <span
                  class="store-price"><?php echo $this->locale()->toCurrency($item->shipping_amt, $this->settings('payment.currency', 'USD')) ?></span>
              <?php endif; ?>
            </td>
            <td class="center">
              <?php echo $this->locale()->toNumber($item->shipping_days) ?>
            </td>
            <td class="center">
              <?php echo $this->locale()->toNumber($item->shipping_tax) ?>
            </td>
            <td class='center'>
              <a
                href="<?php echo $this->url(array('action' => 'edit-supported', 'location_id' => $item->getIdentity())); ?>"
                class="smoothbox">
                <img title="<?php echo $this->translate('STORE_Edit Location') ?>" class="product-icon"
                     src="application/modules/User/externals/images/edit.png"></a>
              <a
                href="<?php echo $this->url(array('action' => 'remove-supported', 'location_id' => $item->getIdentity())); ?>"
                class="smoothbox">
                <img title="<?php echo $this->translate('STORE_Delete Location') ?>" class="product-icon"
                     src="application/modules/Core/externals/images/delete.png"></a>
              <?php if (!$this->parent_id): ?>
                <a class="smoothbox"
                   href="<?php echo $this->url(array(
                     'module' => 'store',
                     'controller' => 'locations',
                     'action' => 'all',
                     'parent_id' => $item->getIdentity()), 'admin_default', true); ?>?nc=1">
                  <img title="<?php echo $this->translate('STORE_Add Sub-location') ?>" class="product-icon"
                       src="application/modules/Store/externals/images/admin/add_location.png"></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <br/>

      <div>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
          'query' => $this->filterValues,
          'pageAsQuery' => true,
        )); ?>
      </div>

    <?php else: ?>
      <div style="font-weight: bold; color: #e57c26">
        <?php echo $this->translate("STORE_No locations found."); ?>
      </div>
    <?php endif; ?>

  </div>
</div>