<?php

$this->headTranslate(array(
    'OFFERS_form_validate_desc'
)); ?>

<script type="text/javascript">

    en4.core.runonce.add(function () {
        <?php echo $this->getOfferEditorInit(); ?>

        $('form-upload-offers').getElements('div[style*=height]').setStyle('height', '100% !important');
        $('buttons-wrapper').setProperty('id', 'submit-wrapper');
        $('choose_oftype').inject($('form-upload-offers').getElement('.form-elements').getParent(), 'top');
        $('generate_code').inject($('coupons_code-element'), 'bottom');
        $('discount_type_container').inject($('discount-wrapper', 'left'));

        var element = 'oftype_free';
        var offertype = 'free';
        if(window.edit_offer){
            offertype = '<?php echo $this->type ?>';
            element = 'oftype_'+ offertype;
        }
        Offers.init();
        Offers.formFilter($(element), offertype);

        $('offers').getElementById('submit').addEvent('click', function(e) {
            if(!tinymce.activeEditor.getContent({format:'text'}).trim().length){
                alert(en4.core.language.translate('OFFERS_form_validate_desc'));
                e.preventDefault();
                return false;
            }
        });

    });


    function selectTypeCode($el) {
        if ($el.get('value') == 'offer_code') {
            $('coupons_code-wrapper').setStyle('display', 'block');
            $('coupons_code').setStyle('display', 'block').set('disabled', false);
            $('generate_code').setStyle('display', 'block').set('disabled', false);
        } else {
            $('coupons_code-wrapper').setStyle('display', 'none');
            $('coupons_code').setStyle('display', 'none').set('disabled', true);
            $('generate_code').setStyle('display', 'none').set('disabled', true);
        }
    }

    function changeTimeLimit($el) {
        if ($el.get('name') == 'enable_time_left') {
            if ($el.get('checked')) {
                $$('.offers_form_container #starttime-wrapper')[0].setStyle('display', 'block');
                $$('.offers_form_container #endtime-wrapper')[0].setStyle('display', 'block');
                $('starttime').set('disabled', false);
                $('endtime').set('disabled', false);
            } else {
                $$('.offers_form_container #starttime-wrapper')[0].setStyle('display', 'none');
                $$('.offers_form_container #endtime-wrapper')[0].setStyle('display', 'none');
                $('starttime').set('disabled', true);
                $('endtime').set('disabled', true);
            }
        } else if ($el.get('name') == 'enable_redeem_time') {
            if ($el.get('checked')) {
                $('redeem_starttime-wrapper').setStyle('display', 'block');
                $('redeem_endtime-wrapper').setStyle('display', 'block');
                $('redeem_starttime').set('disabled', false);
                $('redeem_endtime').set('disabled', false);
            } else {
                $('redeem_starttime-wrapper').setStyle('display', 'none');
                $('redeem_endtime-wrapper').setStyle('display', 'none');
                $('redeem_starttime').set('disabled', true);
                $('redeem_endtime').set('disabled', true);
            }
        }
    }

    function enableCouponsCount($el) {
        if ($el.getProperty('checked')) {
            $('coupons_count-wrapper').setStyle('display', 'block');
            $('coupons_count').setStyle('display', 'block');
        } else {
            $('coupons_count-wrapper').setStyle('display', 'none');
            $('coupons_count').setStyle('display', 'none');
        }
    }

    function generateCouponsCode() {
        var request = new Request.JSON({
            secure: false,
            url: '<?php  echo $this->url(array("module" => "offers", "controller" => "index", "action" => "generate-coupons-code"), "offers_general", true)?>',
            method: 'post',
            data: {
                'format': 'json'
            },
            'onRequest': function () {
                $('generateCode_loading').setStyle('display', 'block');
                $('generate_code').set('disabled', true);
            },
            onSuccess: function (response) {
                $('coupons_code').setProperty('value', response.code);
            },
            'onComplete': function () {
                $('generateCode_loading').setStyle('display', 'none');
                $('generate_code').set('disabled', false);
            }
        }).send();
    }

    function checkInput(input, point) {
        if (point == true) {
            input.value = input.value.replace(/[^\d.]/g, '');
        } else {
            input.value = input.value.replace(/[^\d]/g, '');
        }
    }

    function showMoreProducts(){
        $$('.offer_store_product_list').setStyle('height', 'auto');
        $('offer_store_product_less').show();
        $('offer_store_product_more').hide();
    }

    function showLessProducts(){
        $$('.offer_store_product_list').setStyle('height', '145px');
        $('offer_store_product_more').show();
        $('offer_store_product_less').hide();
    }

</script>

<div class="offers_form_container">
    <div id="choose_oftype" class="choose_oftype">
        <div class="oftype_free" id="oftype_free" onclick="Offers.formFilter(this, 'free')">
            <img
                src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Offers/externals/images/oftype_free.png"/>
            <span class="title_oftype"><?php echo $this->translate('OFFERS_form_free'); ?></span>
        </div>
        <div class="oftype_paid" id="oftype_paid" onclick="Offers.formFilter(this, 'paid')">
            <img
                src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Offers/externals/images/oftype_paid.png"/>
            <span class="title_oftype"><?php echo $this->translate('OFFERS_form_paid'); ?></span>
        </div>
        <div class="oftype_reward" id="oftype_reward" onclick="Offers.formFilter(this, 'reward')">
            <img
                src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Offers/externals/images/oftype_reward.png"/>
            <span class="title_oftype"><?php echo $this->translate('OFFERS_form_reward'); ?></span>
        </div>
        <?php if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')): ?>
            <div class="oftype_store" id="oftype_store" onclick="Offers.formFilter(this, 'store')">
                <img
                    src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Offers/externals/images/oftype_store.png"/>
                <span class="title_oftype"><?php echo $this->translate('OFFERS_form_store'); ?></span>
            </div>
        <?php endif; ?>
        <div style="clear: both;"></div>
        <div class="offer_type_desc">

            <div id="oftype_free_desc">
                <p><?php echo $this->translate('OFFERS_form_free_desc') ?></p>

                <p class="form_oftype_help"><?php echo $this->translate('OFFERS_form_oftype_free_help'); ?></p>
            </div>

            <div id="oftype_paid_desc">
                <p><?php echo $this->translate('OFFERS_form_paid_desc') ?></p>

                <p class="form_oftype_help"><?php echo $this->translate('OFFERS_form_oftype_paid_help'); ?></p>
            </div>

            <div id="oftype_reward_desc">
                <p><?php echo $this->translate('OFFERS_form_reward_desc'); ?></p>

                <p class="form_oftype_help"><?php echo $this->translate('OFFERS_form_oftype_reward_help'); ?></p>
            </div>

            <?php if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')): ?>
                <div id="oftype_store_desc">
                    <p><?php echo $this->translate('OFFERS_form_store_desc') ?></p>

                    <p class="form_oftype_help"><?php echo $this->translate('OFFERS_form_oftype_store_help'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div style="clear: both;"></div>
    <div id="form-offers">
        <?php echo @$this->form->render($this); ?>
    </div>
</div>