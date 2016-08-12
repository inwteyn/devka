<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7907 2010-12-03 21:26:02Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>
<style>
  .store-show-gateway {
    cursor: pointer;
  }
  .store-gateway-instructions {
    padding-top: 10px;
    padding-left: 5px;
  }

  .gateway_description ul.form-errors {
    margin: 0;
  }
  .store_admin_gateways {
    width: 25%;
  }

  table.store_admin_gateways tbody tr:nth-child(even) {
    background-color: transparent;
  }
  .store_admin_gateways td {
    vertical-align: middle !important;
  }
  .store_admin_gateways i {
    font-size: 20px;
  }
  .store_admin_gateways i.green {
    color: #32BE6E;
  }
  #pp_email-element p.description {
    font-size: 11px;
    font-weight: bold;
  }

  .gateway_label {
    background-color: #FFF;
    float: left;
    font-weight: 700;
    margin-top: -22px;
  }

  .gateway_description {
    margin-bottom: 15px;
    padding: 10px 10px 5px;
    padding-left: 0;
    position: relative;
    max-width: 45%;
    width: 45%;
  }

  .gateway_description .form-label {
    display: none;
  }

  .gateway_description .form-wrapper {
    border-top-style: none;
    padding: 10px 0;
    float: none;
  }

  .gateway-fieldset .form-wrapper {
    float: none;
  }

  .gateway-fieldset {
    margin: 0;
    border: none;
    padding-top: 0;
  }

  .gateway-action-button {
    margin-top: 30px;
  }

  .gateway-action-button-right {
    float: right;
  }

  .admin-loader-animation {
    -moz-animation: spin 0.75s infinite linear;
    -webkit-animation: spin 0.75s infinite linear;
    animation: spin 0.75s infinite linear;

    -moz-transform: rotate(360deg);
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }

  .admin-loader {
    border-color: #677e87 #677e87 #677e87 rgba(103, 126, 135, 0.2);
    border-image: none;
    border-radius: 36px;
    border-style: solid;
    border-width: 1px;
    display: none;
    cursor: default;
    height: 32px;
    width: 32px;
  }

  .store-gateway-loader {
    position: absolute;
    left: 46%;
    top: 87%;
  }
</style>


<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php echo $this->content()->renderWidget('store.admin-main-menu', array('active' => $this->activeMenu)); ?>

<h2>
  <?php echo $this->translate("Manage Payment Gateways") ?>
</h2>

<div class='admin_results'>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>


<table class='store_admin_gateways admin_table'>
  <thead>
  </thead>
  <tbody>
  <?php if (count($this->paginator)): ?>
    <?php foreach ($this->paginator as $item): ?>
      <?php if ($item->title == 'Credit' && !Engine_Api::_()->store()->isCreditEnabled()) : ?>
        <?php continue; ?>
      <?php endif; ?>
      <tr class="store-show-gateway" data-id="<?php echo $item->getIdentity(); ?>"
          style="<?php echo ($item->getIdentity() == 1) ? 'background-color:#eaeaea' : ''?>">
        <td class='admin_table_bold' style='font-size: 14px;'>
          <?php echo $item->title ?>
        </td>
        <td class='center'>
          <i id="gtw-icon-<?php echo $item->getIdentity(); ?>" class="hei-check-circle-o <?php echo $item->enabled ? 'green' : ''; ?>"></i>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>
<br><br><br>


<script type="text/javascript">
  var pp_slide = {};
  var c_slide = {};

  en4.core.runonce.add(function () {
    pp_slide = new Fx.Slide($('gtw-inst-2'));
    pp_slide.hide();
    c_slide = new Fx.Slide($('gtw-inst-1'));
    c_slide.hide();


    $('gtw-wrapper-2').setStyle('display', 'none');
    $('gtw-wrapper-2').setStyle('opacity', '1');

    $$('.store-show-gateway').addEvent('click', function() {
      var id = $(this).get('data-id');

      $$('.store-show-gateway').setStyle('background-color', 'transparent');
      $(this).setStyle('background-color', '#eaeaea');

      $$('.gateway_description').setStyle('display', 'none');
      $('gtw-wrapper-'+id).setStyle('display', 'block');
    });

  });

  function toggleInstructions(el) {
    var id = $(el).get('data-id');
    if(id == '1') {
      c_slide.toggle();
    } else {
      pp_slide.toggle();
    }
  }

  function showLoader(el) {
    var parent = $(el).getParent('.gateway_description');
    var inputs = $(parent).getElements('input');
    var buttons = $(parent).getElements('button');

    inputs.each(function (el) {
      $(el).disabled = true;
    });
    buttons.each(function (el) {
      $(el).disabled = true;
    });

    var loader = parent.getElement('.admin-loader');
    loader.setStyle('display', 'inline-block');
  }

  function hideLoader(el) {
    var parent = $(el).getParent('.gateway_description');
    var inputs = $(parent).getElements('input');
    var buttons = $(parent).getElements('button');

    inputs.each(function (el) {
      $(el).disabled = false;
    });
    buttons.each(function (el) {
      $(el).disabled = false;
    });

    var loader = parent.getElement('.admin-loader');
    loader.setStyle('display', 'none');
  }

  function save_data(gateway, values, el, clear) {
    var params = {};
    for (i = 0; i < values.length; i++) {
      var name = values[i].substr(3);
      if (clear == 1) {
        params[name] = $(values[i]).value = '';
      } else {
        params[name] = $(values[i]).value;
      }
    }
    params['format'] = 'json';
    params['id'] = gateway;
    showLoader(el);

    var r = new Request.JSON({
      url: en4.core.baseUrl + 'admin/store/gateway/index',
      data: params/*{
        format: 'json',
        id: gateway,
        values: params
      }*/,
      onSuccess: function (response) {
        var parent = $(el).getParent('.gateway_description');
        var wrapper = parent.getElement('.form-errors');
        wrapper.setStyle('display', 'none');
        wrapper.set('html', '');
        if (response.status) {

        } else {
          if (response.errors) {
            var errors = JSON.parse(response.errors);
            for (var i = 0; i < errors.length; i++) {
              var error = errors[i];
              var li = new Element('li', {html: '<p>' + error[0] + '</p>' + error[1]});
              li.inject(wrapper);
            }
            wrapper.setStyle('display', 'block');
          }
        }
      },
      onFailure: function () {
        hideLoader(el);
      },
      onCancel: function () {
        hideLoader(el);
      },
      onException: function () {
        hideLoader(el);
      },
      onComplete: function () {
        hideLoader(el);
      }
    });
    r.send();
  }


  function editGateway(id, action, el) {
    showLoader(el);

    var r = new Request.JSON({
      url: en4.core.baseUrl + 'admin/store/gateway/edit-gateway',
      data: {
        format: 'json',
        id: id,
        type: action
      },
      onSuccess: function (response) {
        if (response.error) {
        } else {
          if(action == 'enabled') {
            var icon = $('gtw-icon-'+id);
            if(icon.hasClass('green')) {
              icon.removeClass('green');
            } else {
              icon.addClass('green');
            }
          }
        }
      },
      onFailure: function () {
        hideLoader(el);
      },
      onCancel: function () {
        hideLoader(el);
      },
      onException: function () {
        hideLoader(el);
      },
      onComplete: function () {
        hideLoader(el);
      }
    });
    r.send();
  }

</script>

<?php echo $this->form->render($this); ?>