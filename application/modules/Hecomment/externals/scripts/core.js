/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecomment
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     Bolot
 */
Hecomment = {};
Hecomment.core = {

    baseUrl: false,

    basePath: false,

    loader: false,

    environment: 'production',

    setBaseUrl: function (url) {
        this.baseUrl = url;
        var m = this.baseUrl.match(/^(.+?)index[.]php/i);
        this.basePath = ( m ? m[1] : this.baseUrl );
    },

    subject: {
        type: '',
        id: 0,
        guid: ''
    },

    showError: function (text) {
        Smoothbox.close();
        Smoothbox.instance = new Smoothbox.Modal.String({
            bodyText: text
        });
    }

};
Hecomment.core.comments = {
    loadComments: function (type, id, page) {
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'hecomment/comment/list',
            data: {
                format: 'html',
                type: type,
                id: id,
                page: page
            }
        }), {
            'element': $('hecomments')
        });
    },

    attachCreateComment: function (formElement) {
        var bind = this;
        formElement.addEvent('submit', function (event) {
            event.stop();
            if (!formElement.getElement('textarea').get('value') || !formElement.getElement('textarea').get('value').trim().length) {
                if(!formElement.getElement('.comment_attach_preview_image_wall').innerHTML)return;
            }

            var comment_id = formElement.get('id').split('-').pop();
            var comment_container = $('comment_attach_preview_image_wall' + comment_id);
            comment_container.getChildren('a').removeClass('wp_init');
            if (comment_container.get('html').length > 0) {
                comment_container.getChildren('a').removeClass('wp_init');
                if (comment_container.getChildren('#delete_' + comment_id)[0]) {
                    var elem = comment_container.getChildren('#delete_' + comment_id)[0];
                    elem.parentNode.removeChild(elem);
                }
                var img = comment_container.get('html');
                if ($('select_photo_' + comment_id)) {
                    $('select_photo_' + comment_id).setStyle('display', 'block');
                }
            }
            else {
                var img = '';
            }

            var form_values = formElement.toQueryString();
            form_values += '&format=json';
            form_values += '&id=' + formElement.identity.value;
            form_values += '&img=' + img;
            en4.core.request.send(new Request.JSON({
                url: en4.core.baseUrl + 'hecomment/comment/create',
                data: form_values
            }), {
                'element': $('hecomments')
            });
            //bind.comment(formElement.type.value, formElement.identity.value, formElement.body.value);
        })
    },

    comment: function (type, id, body) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'hecomment/comment/create',
            data: {
                format: 'json',
                type: type,
                id: id,
                body: body
            }
        }), {
            'element': $('hecomments')
        });
    },
    reply: function (element) {
        var reply_comment_id = element.get('comment');

    },

    like: function (type, id, comment_id) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'hecomment/comment/like',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id
            }
        }), {
            'element': $('hecomments')
        });
    },

    unlike: function (type, id, comment_id) {
        en4.core.request.send(new Request.JSON({
            url: en4.core.baseUrl + 'hecomment/comment/unlike',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id
            }
        }), {
            'element': $('hecomments')
        });
    },

    showLikes: function (type, id) {
        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'hecomment/comment/list',
            data: {
                format: 'html',
                type: type,
                id: id,
                viewAllLikes: true
            }
        }), {
            'element': $('hecomments')
        });
    },

    editComment: function (id) {

        if ($$('#comment-' + id + ' .comments_body').length) {
            var preview_container = $('comment_attach_preview_image_wall' + id);
            var comment_form = $('comment-form-reply-' + id);
            var comment_clone = $$('#comment-' + id + ' .comments_body')[0].clone();

            var self = this;

            self.show_hide_comment_form(id);

            var comment_form = $('hecomment-form-' + id);
            var comment_clone = $$('#comment-' + id + ' .comments_body')[0].clone();

            if (comment_form.getElement('#submit')) {
                comment_form.getElement('#submit').set('html', en4.core.language.translate('Edit Comment'));
            }

            if (comment_form.getElement('#is_edit_comment')) {
                comment_form.getElement('#is_edit_comment').remove();
            }

            if (comment_form.getElement('#cancel_' + id)) {
                comment_form.getElement('#cancel_' + id).remove();
            }

            if (comment_clone.getElements('.view_more').length) {
                comment_clone = comment_clone.getElements('.view_more').getLast();
                if (comment_clone.getElements('.view_less_link').length) {
                    comment_clone.getElements('.view_less_link').each(function (el) {
                        el.remove();
                    });
                }
            }

            if (comment_form.getElement('#select_photo_' + id)) {
                comment_form.getElement('#select_photo_' + id).show();
            }

            /* Search and replace wall smiles*/

            if (comment_clone.getElements('.wall_smile').length) {
                comment_clone.getElements('.wall_smile').each(function (el) {
                    var smile_alt = el.get('alt');
                    var smile_tag = window.Wall_smiles.filter(function (elem) {
                        if (elem.title.toLowerCase() == smile_alt.toString().toLowerCase()) {
                            return elem;
                        }
                    });
                    var textnode = document.createTextNode(smile_tag[0].tag);

                    comment_clone.replaceChild(textnode, el);
                });
            }

            /* Search and replace people tags*/

            if (comment_clone.getElements('.tag_people').length) {
                comment_clone.getElements('.tag_people').each(function (el) {
                    var textnode = document.createTextNode('@' + el.get('rev'));

                    comment_clone.replaceChild(textnode, el);
                });
            }

            /* Search and replace hashtags */

            if (comment_clone.getElements('.comment_hashtag').length) {
                comment_clone.getElements('.comment_hashtag').each(function (el) {
                    var textnode = document.createTextNode(el.get('html'));

                    comment_clone.replaceChild(textnode, el);
                });
            }

            /* Move comment photo to preview container */

            if (comment_clone.getElement('.comment_photo')) {
                var comment_photo = comment_clone.getElement('.comment_photo').clone();
                var photo_id = comment_photo.get('href').split('/').pop();
                var delete_btn = new Element('div', {
                    'id': 'delete_' + id,
                    'class': 'wpClose hei hei-times delete_photo_in_comment_button'
                });

                delete_btn.addEvent('click', function () {
                    self.deleteImage(id);
                    comment_form.getElement('#select_photo_' + id).show();
                });

                comment_photo.inject(preview_container);
                delete_btn.inject(preview_container);
                preview_container.setStyle('display', 'block');
                comment_clone.getElement('.comment_photo').remove();
                comment_form.getElement('#select_photo_' + id).hide();
            }

            /* Move comment sticker to preview container */

            if (comment_clone.getElement('.smiles_NEW')) {
                var Heemoticons = new Heemoticon();
                var comment_sticker = comment_clone.getElement('.smiles_NEW').clone();
                var used_id = comment_sticker.getElement('img').get('src').split('=').pop();
                var sticker_id = comment_sticker.getElement('img').get('sticker_id');
                var delete_btn = new Element('div', {
                    'id': 'delete_' + id,
                    'class': 'wpClose hei hei-times delete_photo_in_comment_button'
                });

                delete_btn.addEvent('click', function (e) {
                    Heemoticons.deleteImage(used_id, sticker_id, id);
                    comment_form.getElement('#select_photo_' + id).show();
                });

                comment_sticker.inject(preview_container);
                delete_btn.inject(preview_container);
                preview_container.setStyle('display', 'block');
                comment_clone.getElement('.smiles_NEW').remove();
                comment_form.getElement('#select_photo_' + id).hide();
            }

            /* Remove comment link attachment preview */

            if (comment_clone.getElement('.hecomment-attached-link-body')) {
                comment_clone.getElements('.hecomment-attached-link-body').each(function (el) {
                    el.remove();
                });
            }

            /* Remove comment links */

            if (comment_clone.getChildren('a')) {
                var i = null;
                comment_clone.getChildren('a').each(function (el) {
                    if (i != el.get('href')) {
                        i = el.get('href');
                        var textnode = document.createTextNode(i);
                        comment_clone.replaceChild(textnode, el);
                    } else {
                        i = null;
                        el.remove();
                        /* Remove consecutive link*/
                    }
                });
            }

            /* Populate comment form values */

            var comment_text = comment_clone.innerHTML.trim().replace('&nbsp;', '').replace(/\s{2,}/g, ' ').replace(/\<br\>/g, " ");

            comment_form.getElement('textarea').value = comment_text;
            comment_form.getElement('textarea').focus();
            comment_form.getElement('button').show();

            var cancel_btn = new Element('div', {
                'id': 'cancel_' + id,
                'class': 'cancel_edit_in_hecomment',
                'html': en4.core.language.translate('or Cancel')
            });

            cancel_btn.addEvent('click', function () {
                comment_form.getElement('#submit').set('html', en4.core.language.translate('Post Comment'));
                self.show_hide_comment_form(id);
            }).injectAfter(comment_form.getElement('#submit'));

            var is_edit = new Element('input', {
                'id': 'is_edit_comment',
                'type': 'hidden',
                'name': 'is_edit',
                'value': id
            }).inject(comment_form);
        }
    },

    deleteComment: function (type, id, comment_id) {
        if (!confirm(en4.core.language.translate('Do you want to delete this?'))) {
            return;
        }
        (new Request.JSON({
            url: en4.core.baseUrl + 'hecomment/comment/delete',
            data: {
                format: 'json',
                type: type,
                id: id,
                comment_id: comment_id
            },
            onComplete: function () {
                if ($('comment-' + comment_id)) {
                    $('comment-' + comment_id).destroy();
                }
                try {
                    var commentCount = $$('.comments_options span')[0];
                    var m = commentCount.get('html').match(/\d+/);
                    var newCount = ( parseInt(m[0]) != 'NaN' && parseInt(m[0]) > 1 ? parseInt(m[0]) - 1 : 0 );
                    commentCount.set('html', commentCount.get('html').replace(m[0], newCount));
                } catch (e) {
                }
            }
        })).send();
    },

    attachBtnEvent: function (id, sec) {
        if(ImageLayout) ImageLayout.run();
        this.setSmile();
        setTimeout(function () {
            var file_button = $('photo_comment_' + id);
            file_button.addEvent('change', function (e) {
                if (window.change_select == 1) {
                    return;
                }
                window.change_select = 1;
                var container = $('comment_attach_preview_image_wall' + id);
                var loading = $('comment_attach_loading_wall' + id);
                if(this.value){
                    var extension = this.value.split('.');
                    var last = extension.pop();
                    var ext = ['png', 'jpeg', 'jpg', 'gif', 'bmp'];
                    if (!ext.contains(last)) {
                        window.comment_photo_query = 0;
                        window.change_select = 0;
                        return;
                    }
                }
                loading.setStyle('display', 'block');
                var url = en4.core.baseUrl + 'wall/index/commentphoto?action_id=' + id;

                var data = new FormData();

                if(this.files[0]){
                    data.append('photo_comment', this.files[0]);
                }
                var request = new XMLHttpRequest();
                request.onreadystatechange = function () {
                    if (request.readyState == 4) {
                        try {
                            var resp = request.response;
                        } catch (e) {

                        }
                    }
                    if (resp) {
                        container.set('html', resp);
                        var delete_button = new Element('div', {
                            'id': 'delete_' + id,
                            'class': 'wpClose hei hei-times delete_photo_in_comment_button'
                        }).inject(container);

                        delete_button.addEvent('click', function (e) {
                            deleteImage(id);
                        });

                        loading.setStyle('display', 'none');
                        container.setStyle('display', 'block');
                        $('select_photo_' + id).setStyle('display', 'none');
                        window.comment_photo_query = 0;
                        window.change_select = 0;
                    }
                };
                request.open('POST', url);
                request.send(data);

            });
        }, sec ? sec : 1000);
    },

    show_hide_comment_form: function (id) {
        var form_id = 'hecomment-form-' + id;
        var parent = $(form_id).getParent('#hecomments');
        parent.getElements('.hecomment-form-class').each(function (el) {
            if (el.get('id') == form_id) {
                if (el.getStyle('display') == 'none') {
                    if (el.getElement('#is_edit_comment')) {
                        el.getElement('#is_edit_comment').remove();
                    }
                    if (el.getElement('.comment_attach_preview_image_wall')) {
                        el.getElement('.comment_attach_preview_image_wall').set('html', '');
                    }
                    if (el.getElement('#cancel_' + id)) {
                        el.getElement('#cancel_' + id).remove();
                    }
                    el.getElement('textarea').value = '';
                    el.show();
                    el.focus();
                } else {
                    el.hide();
                }
            } else {
                el.hide();
            }
        });
    },

    deleteImage: function (id) {
        var photo_id = 0;
        var container = $('comment_attach_preview_image_wall' + id);
        if (container) {
            photo_id = container.getChildren('a').get('href')[0].split('/').pop();
        }
        if (!photo_id) {
            return;
        }
        if (window.load_image_deletes_comment == 1) {
            return;
        }
        var loading = $('comment_attach_loading_wall' + id);
        loading.setStyle('display', 'block');
        container.setStyle('display', 'none');
        window.load_image_deletes_comment = 1;
        var req = new Request({
            method: 'get',
            url: en4.core.baseUrl + 'hecomment/comment/delete-image',
            data: {
                'do': '1',
                'photos_id_del': photo_id
            },
            onComplete: function (response) {
                container.set('html', '');
                loading.setStyle('display', 'none');
                $('select_photo_' + id).setStyle('display', 'block');
                if ($('photo_comment_' + id)) $('photo_comment_' + id).value = '';
                window.load_image_deletes_comment = 0;
            }
        }).send();
    },

    setSmile: function(){
        setInterval(function(){
            if(ImageLayout) ImageLayout.run();
            mention.second_activate();
        },1000);
    }
};

Hecomment.mention = new Class({

    options: {

        'suggestUsers': [],

        'suggestProto': 'request.json',
        requestOptions: {},
        'suggestOptions': {
            'minLength': 0,
            'maxChoices': 100,
            'delay': 250,
            'selectMode': 'pick',
            'multiple': false,
            'filterSubset': true,
            'tokenFormat': 'object',
            'tokenValueKey': 'label',
            'injectChoice': $empty,
            'onPush': $empty,

            'prefetchOnInit': true,
            'alwaysOpen': false,
            'ignoreKeys': false

        }
    },

    setOptions: function (options) {
        this.options.suggestUsers = options.suggestUsers;
        this.options.suggestProto = options.suggestProto;
    },

    linkAttached: false,


    second_activate: function () {
        var self = this;
        this.activateComment();
        if(ImageLayout) ImageLayout.run();
        setTimeout(function () {
            self.activateComment();
        }, 1000);
    },
    getTagsFromComposer: function () {
        var tags = new Hash();

        this.getComposer().elements.body.getElements('.wall_tag').each(function (item) {
            tags[item.get('rev')] = item.get('text');
        });

        return tags;

    },

    checkTagsFromComposer: function () {
        this.getComposer().elements.body.getElements('.wall_tag').each(function (item) {
            if (item.get('text') != item.get('rel')) {
                item.removeClass('wall_tag');
            }
        });
    },

    activateComment: function () {
        var self = this;

        $$('#hecomments #body').each(function (textarea) {
            if (textarea) {
                textarea.addEvent('keyup', function () {
                    var text = this.value;
                    var pos = self.doGetCaretPosition(this);

                    var s = self.getWordTest(text, pos);
                    var re = /(?:^|\W)@(\w+)(?!\w)/g, match, matches = [];
                    while (match = re.exec(s)) {
                        matches.push(match[1]);
                    }
                    if (matches.length) {
                        self.getSuggestComment(matches, this);
                    }
                })
            }
        })
    },
    doGetCaretPosition: function (ctrl) {
        var CaretPos = 0;   // IE Support
        if (document.selection) {
            ctrl.focus();
            var Sel = document.selection.createRange();
            Sel.moveStart('character', -ctrl.value.length);
            CaretPos = Sel.text.length;
        }
        // Firefox support
        else if (ctrl.selectionStart || ctrl.selectionStart == '0')
            CaretPos = ctrl.selectionStart;
        return (CaretPos);
    },
    getWordTest: function (str, pos) {

        // Perform type conversions.
        str = String(str);
        pos = Number(pos) >>> 0;

        // Search for the word's beginning and end.
        var left = str.slice(0, pos + 1).search(/\S+$/),
            right = str.slice(pos).search(/\S+\s*/);

        // The last word in the string is a special case.
        if (right < 0) {
            return str.slice(left);
        }

        // Return the word, using the located bounds to extract it from the string.
        return str.slice(left, right + pos);

    },
    getWordTestall: function (str, pos) {

        // Perform type conversions.
        str = String(str);
        pos = Number(pos) >>> 0;

        // Search for the word's beginning and end.
        var left = str.slice(0, pos + 1).search(/\S+$/),
            right = str.slice(pos).search(/\s/);

        // The last word in the string is a special case.
        if (right < 0) {
            return str.slice(left);
        }

        // Return the word, using the located bounds to extract it from the string.
        return str.slice(left, right + pos);

    },

    monitorKey: function (e) {

        if (e && (e.key == 'enter' || e.key == 'up' || e.key == 'down' || e.key == 'esc')) {
            return;
        }

        var monitor = function () {

            var info = this.getComposer().editor.getCaretAndText();
            var caret = info.caret;
            var value = info.text;

            this.checkTagsFromComposer();

            if (e && e.key == 'space') {
                this.checkLink();
            }

            this.endSuggest();
            var segment = this.detectTag(caret, value);

            if (segment && this.getTagsFromComposer().getLength() <= 10) {
                this.doSuggest(segment);
            }

        }.bind(this);

        window.clearTimeout(this.keyTimeOut);
        this.keyTimeOut = window.setTimeout(monitor, 100);

    },

    doSuggest: function (text) {
        this.currentText = text;
        var suggest = this.getSuggest();
        var input = this.getHiddenInput();
        input.set('value', text);
        input.value = text;
    },

    endSuggest: function () {
        this.currentText = '';
        this.positions = {};
        if (this.suggest) {
            this.getSuggest().destroy();
            delete this.suggest;
        }
    },

    getHiddenInput: function () {
        if (!this.hiddenInput) {
            this.hiddenInput = new Element('input', {
                'type': 'text',
                'styles': {
                    'display': 'none'
                }
            }).inject(this.getComposer().container.getElement('.wallTextareaContainer'));
        }
        return this.hiddenInput;
    },

    getSuggest: function () {

        if (!this.suggest) {

            this.choices = new Element('ul', {
                'class': 'wall-autosuggestion',
                'styles': {
                    'position': 'absolute',
                    'width': this.getComposer().container.getElement('.wallComposerContainer').getCoordinates().width - 2 // 2px borders
                }
            }).inject(this.getComposer().container.getElement('.wallComposerContainer'), 'bottom');

            var self = this;
            var options = $merge(this.options.suggestOptions, {
                'customChoices': this.choices,
                'injectChoice': function (token) {
                    if (self.getTagsFromComposer().has(token.guid)) {
                        return;
                    }
                    if (!token.guid) {
                        return;
                    }
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices',
                        //'value': token.id,
                        'html': token.photo || '',
                        'id': token.guid
                    });
                    new Element('div', {
                        'html': this.markQueryValue(token.label),
                        'class': 'autocompleter-choice'
                    }).inject(choice);
                    new Element('input', {
                        'type': 'hidden',
                        'value': JSON.encode(token)
                    }).inject(choice);
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
                },
                'onChoiceSelect': function (choice) {

                    var data = JSON.decode(choice.getElement('input').value);
                    var body = self.getComposer().elements.body;

                    var replaceString = '@' + self.currentText;
                    var newString = '<span rev="' + data.guid + '" rel="' + data.label + '" class="wall_tag">' + data.label + '</span>&nbsp;';
                    var content = body.get('html');

                    content = content.replace(/\<span\>\<\/span\>/ig, ''); // IE
                    content = content.replace(new RegExp(replaceString, 'i'), newString);
                    body.set('html', content);

                    var lastElement = null;
                    body.getElements('.wall_tag').each(function (item) {
                        if (item.get('text') == data.label.replace(/&#039;/ig, '\'')) {
                            lastElement = item;
                        }
                    });

                    self.getComposer().editor.setCaretAfterElement(lastElement);

                },
                'emptyChoices': function () {
                    this.fireEvent('onHide', [this.element, this.choices]);
                },
                'onCommand': function (e) {
                    switch (e.key) {
                        case 'enter':
                            break;
                    }
                }
            });

            if (this.options.suggestProto == 'request.json') {
                this.suggest = new Wall.Autocompleter.Request.JSON(this.getHiddenInput(), en4.core.baseUrl + 'wall/index/suggest?includeSelf=true ', options);
            }
        }

        return this.suggest;

    },

    getSuggestComment: function (text, elem) {
        var self = this;

        $$('body')[0].removeEvents().addEvent('click', function (e) {
            if (!e.target.getParent('#wall-autosuggestion-contaner-for-usercomment')) {
                self.hideComposerTags();
            }
        });
        if (this.options.suggestProto == 'request.json' && !this.options.suggestUsers.length) {
            var users = [];
            var req = new Request({
                method: 'get',
                url: en4.core.baseUrl + 'wall/index/suggestuser?includeSelf=true',
                onComplete: function (response) {
                    var jsonParse = JSON.parse(response);
                    jsonParse.each(function (a) {
                        if (a.username.indexOf(text) > -1 || a.label.indexOf(text) > -1) {
                            users.push(a);
                        }
                    });
                    self.options.suggestUsers = users;
                    if ($('wall-autosuggestion-contaner-for-usercomment')) {
                        var contaner = $('wall-autosuggestion-contaner-for-usercomment');
                        contaner.set('html', '');
                    } else {
                        var contaner = new Element('ul', {
                            'class': 'wall-autosuggestion-contaner-comment',
                            'id': 'wall-autosuggestion-contaner-for-usercomment',
                            'styles': {
                                'position': 'absolute',
                                'width': (elem.getSize().x - 2) + 'px',
                                'border': '1px solid #666'
                            }
                        });
                        self.injectAbsoluteCommentSuggest(elem, contaner);

                    }

                    for (var t in users) {
                        if (users[t].type == 'user') {
                            var choice = new Element('li', {
                                'class': 'autocompleter-choices',
                                'html': users[t].photo || '',
                                'id': users[t].guid,
                                'rev': users[t].username
                            }).inject(contaner);
                            choice.addEvent('click', function () {
                                elem.focus();
                                var pos = self.doGetCaretPosition(elem);
                                var s = self.getWordTestall(text, pos);


                                elem.value = elem.value.replaceAt(elem.value.slice(0, pos).search(/\S+$/), ' @' + this.get('rev') + ' ');
                                self.hideComposerTags();
                            });
                            new Element('div', {
                                'html': '<a class="autocompleter-queried" href="javascript:void(0)">' + users[t].label + '</a>',
                                'class': 'autocompleter-choice'
                            }).inject(choice);
                        }
                    }
                }
            }).send();

        }
        if (this.options.suggestProto == 'local' || this.options.suggestUsers.length) {
            var users = this.findUser(text);

            if ($('wall-autosuggestion-contaner-for-usercomment')) {
                var contaner = $('wall-autosuggestion-contaner-for-usercomment');
                contaner.set('html', '');
            } else {
                var contaner = new Element('ul', {
                    'class': 'wall-autosuggestion-contaner-comment',
                    'id': 'wall-autosuggestion-contaner-for-usercomment',
                    'styles': {
                        'position': 'absolute',
                        'width': (elem.getSize().x - 2) + 'px',
                        'border': '1px solid rgba(0, 0, 0, 0.3)'
                    }
                });
                this.injectAbsoluteCommentSuggest(elem, contaner);

            }

            for (var t in users) {
                if (users[t].type == 'user') {
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices heemoticon',
                        'html': users[t].photo || '',
                        'id': users[t].guid,
                        'rev': users[t].username
                    }).inject(contaner);
                    choice.addEvent('click', function () {
                        elem.focus();
                        var pos = self.doGetCaretPosition(elem);
                        var s = self.getWordTestall(text, pos);


                        elem.value = elem.value.replaceAt(elem.value.slice(0, pos).search(/\S+$/), ' @' + this.get('rev') + ' ');
                        self.hideComposerTags();
                    });
                    new Element('div', {
                        'html': '<a class="autocompleter-queried" href="javascript:void(0)">' + users[t].label + '</a>',
                        'class': 'autocompleter-choice'
                    }).inject(choice);
                }
            }


        }

    },
    hideComposerTags: function () {
        if ($('wall-autosuggestion-contaner-for-usercomment')) {
            var elem = document.getElementById("wall-autosuggestion-contaner-for-usercomment");
            elem.parentNode.removeChild(elem);

        }
    },
    injectAbsoluteCommentSuggest: function (element, container, plus) {
        element = $(element);
        container = $(container);

        if ($type(element) != 'element' || $type(container) != 'element') {
            return;
        }

        var build = function () {
            var pos = element.getCoordinates();
            var form = element.getParent('form');
            container
                .setStyle('position', 'absolute')
                .setStyle('top', pos.top + pos.height)
                .setStyle('min-height', '50px')
                .setStyle('z-index', '999')
                .setStyle('right', ($$('body')[0].getCoordinates().width - pos.left - pos.width));


        };

        container.inject(Wall.externalDiv(), 'bottom');
        build();

        return container;

    },
    findUser: function (text) {

        var search = text.toString().toLowerCase();

        var users = this.options.suggestUsers;
        var rArr = [];

        for (var t in users) {
            if (t.toInt() > 0) {
                if (users[t].username && (users[t].username.toLowerCase().indexOf(search) > -1 || users[t].label.toLowerCase().indexOf(search) > -1)) {
                    rArr.push(users[t]);
                }
            }
        }
        return (rArr);
    },
    checkLink: function (text) {
        if (this.linkAttached) {
            return;
        }

        var caret = 0;
        var value = '';

        if (text) {

            caret = text.length;
            value = text;

        } else {
            var info = this.getComposer().editor.getCaretAndText();
            caret = info.caret;
            value = info.text;
        }


        value = value.replace('&amp;', '&');
        var link_matches = this.detectLink(caret, value);

        if (link_matches) {

            var video = this.getComposer().getPlugin('video');
            var link = this.getComposer().getPlugin('link');
            var avp = this.getComposer().getPlugin('avp');

            if ((link_matches[0].match(/(http|https)\:\/\/(www\.|)youtube\.com\/watch/ig) || link_matches[0].match(/(http|https)\:\/\/(www\.|)youtu\.be/ig)) && video && video.options.autoDetect) {

                try {
                    this.linkAttached = true;

                    video.activate(true);
                    this.getComposer().container.getElement('#wall-compose-youtube-video').click();
                    video.updateVideoFields.bind(video)();
                    this.getComposer().container.getElement('.wall-compose-video-form-input, #compose-video-form-input').value = link_matches[0];
                    video.doAttach();

                    this.getComposer().editor.moveCaretToEnd();

                } catch (e) {

                }


            } else if (link_matches[0].match(/(http|https)\:\/\/(www\.|)vimeo\.com\/[0-9]{1,}/ig) && video && video.options.autoDetect) {

                try {

                    this.linkAttached = true;

                    video.activate(true);
                    this.getComposer().container.getElement('#wall-compose-vimeo-video').click();
                    video.updateVideoFields.bind(video)();
                    this.getComposer().container.getElement('.wall-compose-video-form-input, #compose-video-form-input').value = link_matches[0];
                    video.doAttach();

                    this.getComposer().editor.moveCaretToEnd();

                } catch (e) {

                }


            } else if ((link_matches[0].match(/(http|https)\:\/\/(www\.|)youtube\.com\/watch/ig) || link_matches[0].match(/(http|https)\:\/\/(www\.|)youtu\.be/ig)) && avp) {

                try {

                    this.linkAttached = true;

                    var a = new Element('a', {'href': en4.core.baseUrl + 'vids/feed-import/?format=smoothbox'});
                    Smoothbox.open(a);

                    (function () {

                        for (var i = 0; i < window.frames.length; i++) {
                            var item = window.frames[i];
                            if (item && item.location && item.location.href.indexOf('vids/feed-import') != -1) {
                                item.onload = function () {
                                    item.$$('input[name=url]').set('value', link_matches[0].replace('&amp;', '&'));
                                };
                            }
                        }
                    }).delay(2000);


                    this.getComposer().editor.moveCaretToEnd();

                } catch (e) {
                }

            } else if (link_matches[0].match(/(http|https)\:\/\/(www\.|)vimeo\.com\/[0-9]{1,}/ig) && avp) {

                try {

                    this.linkAttached = true;

                    var a = new Element('a', {'href': en4.core.baseUrl + 'vids/feed-import/?format=smoothbox'});
                    Smoothbox.open(a);

                    for (var i = 0; i < window.frames.length; i++) {
                        var item = window.frames[i];
                        if (item && item.location && item.location.href.indexOf('vids/feed-import') != -1) {
                            item.onload = function () {
                                item.$$('input[name=url]').set('value', link_matches[0]);
                            };
                        }
                    }
                    this.getComposer().editor.moveCaretToEnd();

                } catch (e) {
                }
            }
            else if (link && link.options.autoDetect) {
                try {
                    this.linkAttached = true;
                    link.activate(true);
                    this.getComposer().container.getElement('.wall-compose-link-form-input').value = link_matches[0];
                    link.doAttach();
                    this.getComposer().editor.moveCaretToEnd();
                } catch (e) {

                }
            }
        }
    },

    detectTag: function (caret, value) {
        if (!caret || !value) {
            return;
        }

        var pre_value = value.substr(0, caret);

        if (!pre_value) {
            return;
        }

        var last_index = pre_value.lastIndexOf('@');
        if (last_index == -1 || caret <= last_index || caret >= last_index + 10) {
            return;
        }
        var segment = pre_value.substr(last_index + 1); // after @
        if (!segment || segment.lastIndexOf(' ') != -1) {
            return;
        }

        return segment;

    },

    detectLink: function (caret, value) {
        if (!caret || !value) {
            return;
        }

        var pre_value = value.substr(0, caret);
        if (!pre_value) {
            return;
        }
        var last_index = pre_value.substr(0, pre_value.length - 1).lastIndexOf(' ');
        if (last_index == -1) {
            last_index = 0;
        } else {
            last_index++;
        }
        var segment = value.substr(last_index, (caret - last_index));

        var matches = segment.match(/(https?\:\/\/|www\.)+([a-zA-Z0-9._-]+\.[a-zA-Z.]{2,5})?[^\s]*/i);
        if (!matches) {
            return;
        }
        if (matches.length != 3) {
            return;
        }
        if (!matches[0] || !matches[1] || !matches[2]) {
            return;
        }
        return matches;
    }


});

String.prototype.replaceAt = function (index, character) {
    return this.substr(0, index) + character + this.substr(index + character.length);
};

Hecomment.showReplyComments = function(comment_id, elem_this){
  elem_this.hide();
  $$('.hecomment_reply_comment_id-' + comment_id).setStyle('display','inline-block');
  $('hide_reply_comments_button-' + comment_id).show();
};

Hecomment.hideReplyComments = function(comment_id, elem_this){
  elem_this.hide();
  $$('.hecomment_reply_comment_id-' + comment_id).hide();
  $('show_reply_comments_button-' + comment_id).setStyle('display','block');
};
