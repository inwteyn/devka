<h2><?php echo $this->translate("STORE_Store Plugin") ?></h2>
<?php echo $this->content()->renderWidget('store.admin-main-menu', array('active' => $this->activeMenu)); ?>

<div class='clear'>
    <div class='settings store-admin-currency-settings'>
        <form class="global_form">
            <div>
                <h2 style="margin: 0"><?php echo $this->translate("STORE_Currency Settings"); ?></h2>
                <div class="form-wrapper">
                    <div class="form-label"><?php echo $this->translate("STORE_Enable multi currency option on your Store plugin"); ?></div>
                    <div class="form-element" style="min-width: 550px;">
                        <input type="checkbox" id="enable-multicurrency" style="cursor: pointer" <?php if($this->multicurrency == 1) echo 'checked'; ?>>
                    </div>
                </div>

                <table id="store-user-currency-table" class='admin_table' <?php if(!$this->multicurrency) echo 'style="display:none;"'; ?>>
                    <thead>

                    <tr>
                        <th style="width: 10%;"><?php echo $this->translate("STORE_Code") ?></th>
                        <th style="width: 25%;text-align: left;"><?php echo $this->translate("STORE_Name") ?></th>
                        <th style="width: 20%;text-align: left;"><?php echo $this->translate("STORE_Supported by") ?></th>
                        <th style="width: 16%;"><?php echo $this->translate("STORE_Conversion rate") ?></th>
                        <th style="width: 15%;"><?php echo $this->translate("STORE_Status") ?></th>
                        <th style="width: 14%;"><?php echo $this->translate("STORE_Default currency") ?></th>
                    </tr>

                    </thead>
                    <tbody>
                    <?php foreach ($this->currencies as $currency): ?>

                        <tr identity="<?php echo $currency->getIdentity(); ?>" class="<?php if($this->defaultcurrency == $currency->currency) echo 'c_default '; echo $currency->enabled ? 'c_enabled' : 'c_disabled'; ?>">
                            <td class="currency-code"><?php echo $currency->currency; ?></td>
                            <td class="currency-name"><?php echo $currency->name; ?></td>
                            <td class="currency-supported"><?php echo $currency->supported; ?></td>
                            <td class="currency-value">
                                <input class="currency-conversion-rate" type="text" value="<?php echo $currency->value; ?>" <?php if(!$currency->enabled) echo 'disabled'; ?>>
                                <i class="hei hei-refresh hei-lg refresh-currency" title="<?php echo $this->translate("STORE_Update")?>"></i>
                                <div class="default-currency-info">1</div>
                            </td>
                            <td class="currency-status">
                                <a href="javascript:void(0)" class="currency-main-status">
                                    <?php echo $currency->enabled ? $this->translate("STORE_Enabled") : $this->translate("STORE_Disabled");?>
                                </a>
                                <span class="default-currency-info"><?php echo $this->translate("STORE_Enabled"); ?></span>
                            </td>
                            <td class="currency-default">
                                <div class="default-currency-info"><?php echo $this->translate("STORE_Default"); ?></div>
                                <a href="javascript:void(0)"><?php echo $this->translate("STORE_Make default"); ?></a>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    $$('.currency-status a').addEvent('click', function() {
        var self = this;
        var parent_el = self.getParent('tr');
        toggleLoader();
        request = new Request.JSON({
            'format': 'json',
            'url': '<?php echo $this->url(array('controller' => 'admin-currency','action' => 'change-status'), 'store_admin_extended') ?>',
            'data': collectData(parent_el),
            'onSuccess': function (responseJSON) {
                if (responseJSON['new_status'].toInt() == 0) {
                    self.set('html', '<?php echo $this->translate("STORE_Disabled")?>');
                    parent_el.getElement('.currency-conversion-rate').set('disabled','true');
                    parent_el.removeClass('c_enabled').addClass('c_disabled');
                } else {
                    self.set('html', '<?php echo $this->translate("STORE_Enabled")?>');
                    parent_el.getElement('.currency-conversion-rate').removeAttribute('disabled');
                    parent_el.removeClass('c_disabled').addClass('c_enabled');
                }
                toggleLoader();
            }
        }).send();
    });

    $$('.currency-default a').addEvent('click', function() {
        var self = this;
        var parent_el = self.getParent('tr');
        toggleLoader();
        request = new Request.JSON({
            'format': 'json',
            'url': '<?php echo $this->url(array('controller' => 'admin-currency','action' => 'set-default-payment-currency'), 'store_admin_extended') ?>',
            'data': collectData(parent_el),
            'onSuccess': function (responseJSON) {
                toggleLoader();
                if(responseJSON.success == true) {
                    parent_el.getParent('tbody').getElement('tr.c_default').removeClass('c_default');
                    parent_el.addClass('c_default');
                    parent_el.removeClass('c_disabled').addClass('c_enabled');
                    parent_el.getElement('.currency-main-status').set('html', '<?php echo $this->translate("STORE_Enabled")?>');
                    parent_el.getElement('.currency-conversion-rate').removeAttribute('disabled');
                }
            }
        }).send();
    });

    $$('.refresh-currency').addEvent('click', function () {
        var self = this;
        toggleLoader();
        var parent_el = self.getParent('tr');
        if(!parseFloat(parent_el.getElement('.currency-conversion-rate').value)) {
            toggleLoader();
            alert('<?php echo $this->translate("STORE_Conversion rate cannot be null"); ?>');
            return;
        } else {
            request = new Request.JSON({
                'format': 'json',
                'url': '<?php echo $this->url(array('controller' => 'admin-currency','action' => 'update-currency'), 'store_admin_extended') ?>',
                'data': collectData(parent_el),
                'onSuccess': function (responseJSON) {
                    toggleLoader();
                }
            }).send();
        }
    });

    $$('#enable-multicurrency').addEvent('click', function() {
        var checked = this.checked;
        toggleLoader();
        request = new Request.JSON({
            'format': 'json',
            'url': '<?php echo $this->url(array('controller' => 'admin-currency','action' => 'set-multi-currency-setting'), 'store_admin_extended') ?>',
            'data': {'enabled': checked ? 1 : 0},
            'onSuccess': function () {
                toggleLoader();
                if(checked) {
                    $$('#store-user-currency-table').show();
                } else {
                    $$('#store-user-currency-table').hide();
                }
            }
        }).send();
    });

    function collectData(el) {
        return {
            'id': el.get('identity'),
            'value': el.getElement('.currency-conversion-rate').value,
            'status': el.get('class').indexOf('enabled') < 0 ? 0 : 1,
            'currency': el.getElement('.currency-code').innerHTML
        };
    }

    $$('.currency-conversion-rate').addEvent('keyup', function() {
        this.value = this.value.replace(/([^\d.]+)?((\d*\.?\d*)(.*)?$)/, "$3");
    });

    function toggleLoader () {
        if (!window.toogleLoaderEnabled) {
            var screen = new Element('div', {'class': 'store-admin-loader-screen'});
            var loader = new Element('div', {'class': 'store-admin-loader store-admin-loader-circle'});

            screen.injectBefore($$('.store-admin-currency-settings')[0]);
            loader.injectAfter($$('.store-admin-currency-settings')[0]);
        } else {
            $$('.store-admin-loader-screen').destroy();
            $$('.store-admin-loader-circle').destroy();
        }
        window.toogleLoaderEnabled = !window.toogleLoaderEnabled;
    }
</script>


<style type="text/css">

    #global_page_store-admin-currency-index .global_form div.form-label {
        width: 300px;
        text-align: left;
    }

    .store-admin-currency-settings table.admin_table thead tr th,
    .store-admin-currency-settings td.currency-status,
    .store-admin-currency-settings td.currency-default,
    .store-admin-currency-settings td.currency-code,
    .store-admin-currency-settings td.currency-value {
        text-align: center;
    }

    .store-admin-currency-settings td a:hover {
        text-decoration: none;
    }

    td.currency-value {
        position: relative;
    }

    .store-admin-currency-settings tr.c_default.c_enabled {
        background-color: #3ABCD6;
        height: 46px;
    }

    input.currency-conversion-rate {
        width: 35px;
        text-align: center;
    }

    i.refresh-currency {
        cursor: pointer;
        margin: 8px 0 0 10px;
        position: absolute;
    }

    .store-admin-currency-settings tr.c_default.c_enabled * {
        font-weight: bold;
        color: #FFF;
    }

    td.currency-default {
        text-align: center;
    }

    .c_default .currency-conversion-rate,
    .c_default .refresh-currency,
    .c_disabled .refresh-currency,
    .default-currency-info,
    .c_default .currency-main-status,
    .c_default .currency-default > a {
        display: none;
    }

    .c_default .default-currency-info {
        display: block;
        text-align: center;
    }

    .store-admin-loader {
        border-color: #fff #677e87 #fff #677e87;
        border-image: none;
        border-radius: 36px;
        border-style: solid;
        border-width: 2px;
        display: block;
        height: 32px;
        width: 32px;
        position: fixed;
        left: 50%;
        top: 40%;
        z-index: 99;
    }

    .store-admin-loader-screen {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: #000;
        opacity: 0.25;
        z-index: 999;
    }

    .store-admin-loader-circle {
        -moz-animation: spin 0.75s infinite linear;
        -webkit-animation: spin 0.75s infinite linear;
        animation: spin 0.75s infinite linear;
        -moz-transform: rotate(360deg);
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
    }
</style>