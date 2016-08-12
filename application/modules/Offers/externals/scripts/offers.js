var Offers = {
    url: {
        'create': '',
        'contacts': '',
        'remove_photo': '',
        'manage_photos': '',
        'edit_photos': '',
        'list': '',
        'resource_approve': '',
        'member_approve': '',
        'waiting': ''
    },

    list_param: {
        page_num: 1,
        page_id: 0,
        checked_products: [],
        offer_id: 0,
        oftype: 'free',
        filter: 'upcoming'
    },

    time_out: 500,
    album: 0,

    init: function () {
        var self = this;
        self.$loader = $('offer_loader');
        self.$elm = $('offers');
        self.$form = $('form-upload-offers');
        self.$offer_contacts_form = $('offer_contacts_form');
    },

    goForm: function () {
        this.loadTab('form');
    },

    setCounter: function (count) {
        var $counter = $$('.tab_layout_offers_profile_offers span')[0];
        if ($counter) {
            $counter.set('html', '(' + count + ')');
        }
    },

    loadTab: function (tab) {
        var $tab = this.$elm.getElement('.tab_' + tab);

        this.$elm.getElements('.tab').addClass('hidden');
        $tab.hasClass('hidden') ? $tab.removeClass('hidden') : '';
        return $tab;
    },

    showMessage: function (html) {
        this.loadTab('message').set('html', html);
    },

    list: function (filter, page) {
        var self = this;
        if (filter == 'upcoming' || filter == 'past' || filter == 'mine' || filter == 'manage') {
            this.list_param.filter = filter;
        }
        if (page) {
            this.list_param.page_num = page;
        }

        self.request(self.url.list, self.list_param, function (obj) {
            if (obj.html) {
                if (filter == 'upcoming') {
                    self.setCounter(obj.count);
                }
                self.loadTab('list').set('html', obj.html);
            }
        });
    },

    view: function (url) {
        window.location = url;
    },

    formContacts: function (offer_id) {
        var self = this;
        var scrollExample = new Fx.Scroll(window);
        scrollExample.start(0, 0);
        self.loadTab('contacts');
        self.$offer_contacts_form.removeEvents().addEvent('submit', function (event) {
                event.stop();
                var data = {
                    offer_id: offer_id,
                    format: 'json'
                };
                self.request(self.url.contacts + '?' + self.$offer_contacts_form.toQueryString(), data, function (obj) {
                    if (obj.result) {
                        self.showMessage(obj.html);
                        $$('#contacts_offer input').each(function (element) {
                            element.set('value', '');
                        });
                        setTimeout(function () {
                            self.list(self.list_param.filter, self.list_param.page_num);
                        }, self.time_out);
                    }
                });
            }
        );
        return false;
    },

    formCreate: function (form) {
        var self = this;
        var scrollExample = new Fx.Scroll(window);
        scrollExample.start(0, 0);
        var owner_id = en4.user.viewer.id;

        var data = $(form).toQueryString().parseQueryString();
        data.description = window.tinymce.editors.offer_description.getContent().trim();
        data.page_id = self.list_param.page_id;
        data.owner_id = owner_id;
        console.log(data);
        self.request(
            self.url.create, data, function (obj) {
                if (obj.result) {
                    self.showMessage(obj.html);
                    self.setCounter(obj.count);
                    self.list_param.offer_id = obj.offer_id;
                    self.formReset();
                    setTimeout(function () {
                        self.managePhotos(obj.offer_id);
                    }, self.time_out);
                } else {
                    self.showMessage(obj.html);
                }
            });
        return false;
    },

    formReset: function () {
        this.formClear();
        this.filesDelete();
        this.formFilter($('oftype_free'), 'free');
    },

    formFilter: function (element, oftype) {
        var self = this;
        if (oftype != 'free' && oftype != 'paid' && oftype != 'reward' && oftype != 'store') oftype = 'free';

        $('form-upload-offers').getElement('.form-elements').set('tween', {duration: 1000}).tween('opacity', [0, 1]);
        self.list_param.oftype = oftype;
        $('choose_oftype').getChildren().each(function (elem) {
            elem.hasClass('active') ? elem.removeClass('active') : 0;
        });

        self.$form.getElementById('type').set('value', self.list_param.oftype);

        element.addClass('active');

        switch (self.list_param.oftype) {
            case 'free':
                $('price_offer-wrapper').setStyle('display', 'none');
                $('offers_require').setStyle('display', 'none');
                $('popup_products-wrapper').setStyle('display', 'none');
                $('products_ids').set('disabled', true);
                $('price_offer').set('disabled', true);
                $('price_item-wrapper').setStyle('display', 'block');
                $('price_item').set('disabled', false);
                ($('via_credits-wrapper')) ? $('via_credits-wrapper').setStyle('display', 'none') : '';

                //descriptions
                $('oftype_free_desc').setStyle('display', 'block');
                $('oftype_paid_desc').setStyle('display', 'none');
                $('oftype_reward_desc').setStyle('display', 'none');
                ($('oftype_store_desc')) ? $('oftype_store_desc').setStyle('display', 'none') : '';

                break;
            case 'paid':
                $('offers_require').setStyle('display', 'none');
                $('popup_products-wrapper').setStyle('display', 'none');
                $('products_ids').set('disabled', true);
                $('price_offer').set('disabled', false);
                $('price_item').set('disabled', false);
                $('price_offer-wrapper').setStyle('display', 'block');
                $('price_item-wrapper').setStyle('display', 'block');
                ($('via_credits-wrapper')) ? $('via_credits-wrapper').setStyle('display', 'block') : '';

                //descriptions
                $('oftype_paid_desc').setStyle('display', 'block');
                $('oftype_free_desc').setStyle('display', 'none');
                $('oftype_reward_desc').setStyle('display', 'none');
                ($('oftype_store_desc')) ? $('oftype_store_desc').setStyle('display', 'none') : '';

                break;
            case 'reward':
                $('price_item-wrapper').setStyle('display', 'block');
                $('price_offer-wrapper').setStyle('display', 'none');
                $('popup_products-wrapper').setStyle('display', 'none');
                $('offers_require_enable_box').setStyle('display', 'none');
                $('price_item').set('disabled', false);
                $('price_offer').set('disabled', true);
                $('products_ids').set('disabled', true);
                $('offers_require').setStyle('display', 'block');
                $('offers_list_require').setStyle('display', 'block');
                ($('via_credits-wrapper')) ? $('via_credits-wrapper').setStyle('display', 'none') : '';

                //descriptions

                $('oftype_reward_desc').setStyle('display', 'block');
                $('oftype_paid_desc').setStyle('display', 'none');
                $('oftype_free_desc').setStyle('display', 'none');
                ($('oftype_store_desc')) ? $('oftype_store_desc').setStyle('display', 'none') : '';

                break;
            case 'store':
                $('offers_require_enable').checked ? $('offers_list_require').show() : $('offers_list_require').hide();
                $('price_item-wrapper').setStyle('display', 'none');
                $('price_offer-wrapper').setStyle('display', 'none');
                ($('via_credits-wrapper')) ? $('via_credits-wrapper').setStyle('display', 'none') : '';
                $('price_item').set('disabled', true);
                $('price_offer').set('disabled', true);
                $('products_ids').set('disabled', false);
                $('popup_products-wrapper').setStyle('display', 'block');
                $('offers_require').setStyle('display', 'block');
                $('offers_require_enable_box').setStyle('display', 'block');

                //descriptions

                ($('oftype_store_desc')) ? $('oftype_store_desc').setStyle('display', 'block') : '';
                $('oftype_reward_desc').setStyle('display', 'none');
                $('oftype_paid_desc').setStyle('display', 'none');
                $('oftype_free_desc').setStyle('display', 'none');

                break;
        }
        $('form-upload-offers').getElements('div[style*=height]').setStyle('height', '100% !important');
    },

    filesDelete: function () {
        if ($('offers_fancyuploadfileids'))
            $('offers_fancyuploadfileids').value = '';

        $('offers-demo-list').setStyle('display', 'none').empty();
        $('offers-demo-status').setStyle('display', 'block');
    },

    formClear: function () {
        this.$form.title.set('value', '');
        this.$form.price_offer.set('value', '00.00$');
        this.$form.price_item.set('value', '00.00$');
        this.$form.discount.set('value', '');
        this.$form.coupons_count.set('value', '');
        window.tinyMCE.editors.offer_description.setContent("");
        this.$form.coupons_count.setStyle('display', 'none');
        this.$form.generate_code.setStyle('display', 'none');
        this.$form.coupons_code.setStyle('display', 'none');
        this.$form.category_id.set('value', 3);
        this.$form.coupons_code.set('value', '');
        this.$form.getElements('input[type=checkbox]').each(function (element) {
            element.checked = false;
        });

        $('starttime-wrapper').setStyle('display', 'none');
        $('endtime-wrapper').setStyle('display', 'none');
        $('redeem_starttime-wrapper').setStyle('display', 'none');
        $('redeem_endtime-wrapper').setStyle('display', 'none');
        $('coupons_count-wrapper').setStyle('display', 'none');
        $('coupons_code-wrapper').setStyle('display', 'none');
    },

    formCancel: function () {
        var self = this;

        setTimeout(function () {
            self.formReset();
            self.list();
        }, self.time_out);
    },

    managePhotos: function (offer_id) {
        var url = this.url.manage_photos;
        var self = this;
        self.list_param.offer_id = offer_id;

        self.request(url, 'offer_id=' + offer_id, function (obj) {
            if (obj && obj.result) {
                self.loadTab('message').set('html', obj.html);
                if ($('offers-photo-manage')) {
                    self.$photo_manae_offers = $('offers-photo-manage');
                    self.$photo_manae_offers.removeEvents().addEvent('submit', function (event) {
                        event.stop();
                        var form = this;
                        var data = {};
                        data.offer_id = self.list_param.offer_id;
                        data.action = 'save';
                        data.format = 'json';
                        var url = self.url.manage_photos + '?' + form.toQueryString();
                        self.request(url, data, function () {
                            setTimeout(function () {
                                self.formContacts(offer_id)
                            }, self.time_out);
                        });
                    });
                }
            } else {
                setTimeout(function () {
                    self.formContacts(offer_id)
                }, self.time_out);
            }
        })
    },

    request: function (url, data, callback) {
        var self = this;

        if (typeof(data) == 'string') {
            data += '&format=json&no_cache=' + Math.random();
        }
        else if (typeof(data) == 'object') {
            data.format = 'json';
            data.nocache = Math.random();
        }

        if (self.$loader != null) {
            self.$loader.removeClass('hidden');
        }

        var request = new Request.JSON({
            secure: false,
            url: url,
            method: 'post',
            data: data,
            onSuccess: function (obj) {
                self.$loader.addClass('hidden');
                self.init();
                if (callback) {
                    callback(obj);
                }
            }
        }).send();
    },

    selectProducts: function (items) {
        var self = this;
        self.list_param.checked_products = items;
        $('products_ids').set('value', self.list_param.checked_products);
        var request = new Request.JSON({
            secure: false,
            url: en4.core.baseUrl + 'admin/offers/manage/create',
            method: 'post',
            data: {
                get_products: 1,
                ids: self.list_param.checked_products,
                format: 'json'
            },
            onSuccess: function (obj) {
                $$('.popup_product_selected')[0].set('html', obj.body);
            }
        }).send();
    },

    chooseProducts: function (owner_type, owner_id) {
        var self = this;

        he_contacts.width = 600;
        he_contacts.height = 500;
        he_contacts.myCSS = new Asset.css(en4.core.baseUrl + 'application/css.php?request=application/themes/default/theme.css');

        he_contacts.onLoad = function () {
            Smoothbox.instance.positionWindow();
        }

        he_contacts.onClose = function () {
            if (he_contacts.myCSS) {
                he_contacts.myCSS.destroy();
            }
        }

        he_contacts.box('offers', 'getContentItems', 'Offers.selectProducts', en4.core.language.translate('OFFERS_Choose products'),
            {
                'scriptpath': 'application/modules/Offers/views/scripts',
                'owner_type': owner_type,
                'owner_id': owner_id,
                'checked_products': self.list_param.checked_products
            }, 0
        );
    }
}