/* $Id: core.js 2010-05-25 01:44 idris $ */

var suggestPageLike = function (item) {
    var element = item.target;
    var id = element.get('id');
    var url = en4.core.baseUrl;
    url += (element.hasClass('suggest_page_like')) ? 'like/like/format/json/object/page/object_id/' + id : 'like/unlike/format/json/object/page/object_id/' + id;
    var request = new Request.JSON({
        url: url,
        onComplete: function () {
            $$('a[id=' + id + ']').each(function (items) {
                if (items.hasClass('suggest_page_like')) {
                    items.removeClass('suggest_page_like');
                    items.addClass('suggest_page_unlike');
                    items.set('html', en4.core.language.translate('Unlike'));
                } else if (items.hasClass('suggest_page_unlike')) {
                    items.removeClass('suggest_page_unlike');
                    items.addClass('suggest_page_like');
                    items.set('html', en4.core.language.translate('Like'));
                }
            });
        }
    });
    request.send();
};

en4.core.runonce.add(function () {
    $$('a[class*=suggest_page_]').addEvent('click', suggestPageLike);
    $('suggest-invite-friends').addEvent('click', function (e) {
        e.preventDefault();
        $('suggest-search-input').setStyle('display', 'block');
        var self = this, offset;
        offset = self.get('data-offset');
        HESuggest.getFriends(this, offset, 'load', '');

    });
    $('suggest-search-input').addEvent('input', function (e) {
        HESuggest.getFriends($('suggest-invite-friends'), 0, 'search', this.value.trim());
    });
});

var HESuggest = {

    url: '',
    $spec: null,
    he_contacts: null,
    options: {},
    object_type: '',
    object_id: 0,
    $link: null,
    exceptIds: {},
    scriptpath: null,

    init: function (url, options) {
        this.url = url;
        this.options = options;
        this.object_id = options.params.object_id;
        this.object_type = options.params.object_type;
    },

    initLink: function () {
        if (this.object_type == 'album'
            || this.object_type == 'album_photo'
            || this.object_type == 'music_playlist'
            || this.object_type == 'video'
            || this.object_type == 'poll'
            ) {
            this.generateLink();
            this.placeLink();
        }
    },

    generateLink: function () {
        this.$link = new Element('a', {'href': 'javascript:HESuggest.open()', 'class': 'buttonlink suggest-item-link', 'html': 'Suggest To Friends'});
        return this.$link;
    },

    placeLink: function () {

    },

    open: function () {
        this.he_contacts = new HEContacts(this.options);
        this.he_contacts.box();
    },

    suggest: function (uids) {
        var self = this;
        new Request.JSON({
            'url': self.url,
            'method': 'post',
            'data': {
                'uids': uids,
                'format': 'json'
            },
            onSuccess: function (response) {
                he_show_message(response.message, response.type, 5000);
            }
        }).send();
    },

    share: function (html) {
        var cont = new Element('ul', {'html': html, 'class': 'share_container'}),
            cont2 = new Element('div', {'id': 'he_share_container'});

        cont2.appendChild(cont);
        cont2.inject($('global_content'), 'top');

        var top = cont2.getStyle('top');

        Smoothbox.bind();
    },

    suggestItem: function (url, options) {
        var self = this;
        self.init(url, options);
        self.open();
    },
    getFriends: function (link, offset, type, keyword) {
        new Request.HTML({
            'url': link.href,
            'data': {
                format: 'html',
                limit: 4,
                offset: offset,
                keyword: keyword
            },
            'method': 'post',
            onSuccess: function (responseTree, responseElements, responseHTML) {

                if (type === "load") {
                    $('suggest-friend-list').getElement('tbody').innerHTML += responseHTML;
                    if (responseHTML.trim().length === 0) {
                        link.setStyle('display', 'none');
                    } else {
                        link.innerHTML = "Load more";
                        link.set('data-offset', parseInt(offset) + 4);
                    }
                } else if (type === "search") {
                    if (!keyword) {
                        link.setStyle('display', 'block');
                    } else {
                        link.innerHTML = "Load more";
                        link.setStyle('display', 'none');
                    }

                    link.set('data-offset', 4);
                    $('suggest-friend-list').getElement('tbody').innerHTML = responseHTML;
                }
            }
        }).send();
    },
    invite: function (button) {
        this.suggest([button.get('data-id')]);
    }

};

var FriendSuggest = new Class({

    url: en4.core.baseUrl + 'suggest/index/suggest',

    id: 0,

    initialize: function (id) {
        this.id = id;
    },

    suggest: function (uids) {
        var self = this;
        new Request.JSON({
            'url': self.url,
            'method': 'post',
            'data': {
                'uids': uids,
                'format': 'json',
                'suggest_type': 'link_user',
                'object_type': 'user',
                'object_id': self.id
            },
            onSuccess: function (response) {
                he_show_message(response.message, response.type, 5000);
            }
        }).send();
    }

});
var RecItems = new Class({

    Implements: [Options],

    options: {
        except: [],
        object_type: '',
        format: 'json',
        widgetId: 0,
        url: ''
    },

    block: false,
    fxDuration: 600,

    initialize: function (options) {
        this.setOptions(options);
        this.init();
    },

    init: function () {
        var select = '.' + this.options.widgetId + '__reject';
        var rejectBtns = $$(select);

        if (!rejectBtns) {
            return;
        }

        var self = this;
        rejectBtns
            .removeEvents('click')
            .addEvent('click', function () {
                var info = this.id.split('--');
                info = info.pop();
                info = info.split('_');

                var id = parseInt(info.pop());
                var type = info.join('_');

                self.reject(id, type);
            });
    },

    reject: function (id, type) {
        var self = this;

        if (!type) {
            type = this.options.object_type;
        }

        if (this.block) {
            return;
        }

        this.block = true;

        var wid = this.options.widgetId;

        var select = wid + '__suggest-item-' + type + '_' + id;
        var $node = $(select);
        var $container = $node.getParent('.generic_layout_container');


        this.inactivate(id, type);

        new Request.JSON({
            'method': 'post',
            'url': self.options.url,
            'data': {
                'object_type': type,
                'object_id': id,
                'format': 'json',
                'wid': self.options.widgetId,
                'except': self.options.except
            },
            onSuccess: function (response) {
                self.block = false;
                if (response.html) {
                    var $next = $node.getNext();
                    var $div = new Element('div', {'html': response.html});
                    $div = $div.getChildren()[0];
                    $div.setStyle('opacity', 0);
                    $div.setStyle('display', 'none');
                    $div.inject($next, 'before');
                    Smoothbox.bind();
                }

                self.fadeOut($node);

                if (!response.noRec) {

                    self.fadeIn($div);
                    self.options.except.push(response.object_id);
                    self.init();
                    // Enabled Like Plugin Requires
                    if (window.initLikeHintTips) {
                        window.initLikeHintTips($($div).getElements('.he_tip_link'));
                    }

                } else {

                    if ($container.getElements('.suggest-item').length <= 1) {
                        self.fadeOut($container);
                    }
                }

                if (response.object_type == 'friend') {
                    if (!window.friends) {
                        window.friends = {};
                    }

                    var options = {
                        c: "window.friends.callback_" + response.object_id + ".suggest",
                        listType: "all",
                        m: "suggest",
                        l: "getSuggestItems",
                        t: en4.core.language.translate('Suggest %s to your friends', response.title),
                        ipp: 30,
                        nli: 0,
                        params: {
                            scriptpath: HESuggest.scriptpath,
                            suggest_type: 'link_user',
                            object_type: 'user',
                            object_id: response.object_id
                        }
                    };

                    window["friends"]["callback_" + response.object_id] = new FriendSuggest(response.object_id);
                    window["friends"]["friend_" + response.object_id] = new HEContacts(options);
                }
            }
        }).send();
    },

    inactivate: function (id, type) {
        if (!type) {
            type = this.options.object_type;
        }
        var wid = this.options.widgetId;
        var $node = $(wid + '__suggest-item-' + type + '_' + id);
        $node.setStyle('opacity', 0.5);
    },

    fadeOut: function ($node) {
        $node = $($node);
        $node.set('tween', {duration: this.fxDuration});
        $node.tween('opacity', 0);
        window.setTimeout(function () {
            $node.dispose();
        }, this.fxDuration);
    },

    fadeIn: function ($node) {
        $node = $($node);
        $node.set('tween', {duration: this.fxDuration});
        window.setTimeout(function () {
            $node.setStyle('display', 'block');
            $node.tween('opacity', 1);
        }, this.fxDuration);
    }

});