var hecontestCore = {

    targetDate: {},
    currentDate: {},

    contest: 0,
    hash: 0,

    baseUrl: '',

    uploader: {},
    uploadCount: 0,
    extraData: {},

    $form: {},
    $screen: {},
    $body: {},
    $info: {},

    delay: 2000,
    color: '#FFC2C2',
    afterUpload: false,
    allowHide: true,

    isIE: function () {
        if (navigator.userAgent.indexOf('MSIE 9.0') != -1 || navigator.userAgent.indexOf('MSIE 8.0') != -1) {
            return true;
        }
        return false;
    },

    join: function (el, mode) {
        if($('a-wrapper')){
            $('a-wrapper').getElement('.swiff-uploader-box').addEvents({
                  mouseover: function () {
                      $('a-wrapper').getElement('a').setStyle('color', '#333')
                  },
                  mouseout: function () {
                      $('a-wrapper').getElement('a').setStyle('color', '#40A0C9')
                  }
              }
            );
        }
        var self = this;

        self.$body = $('global_content');

        self.$screen = self.$body.getElement('div.hecontest-join-screen');
        self.$form = self.$body.getElement('div.hecontest-widget-join-form-wrapper');

        self.$info = self.$form.getElement('div#hecontest-widget-join-contest-info');

        self.$screen.setStyle('display', 'block');
        self.$form.setStyle('display', 'block');
        if (!hecontestCore.isIE()) {
            self.$form.setStyle('transform', 'translate(240px,-150%)');
            self.$form.setStyle('-webkit-transform', 'translate(240px,-150%)');
            self.$form.setStyle('-ms-transform', 'translate(240px,-150%)');
        }

        setTimeout(function () {
            self.$form.setStyle('transform', 'translate(240px,0)');
            self.$form.setStyle('-webkit-transform', 'translate(240px,0)');
            self.$form.setStyle('-ms-transform', 'translate(240px,0)');

            if (hecontestCore.isIE()) {
                self.$form.setStyle('display', 'block');
                self.$form.setStyle('width', 'auto');
                self.$form.setStyle('height', 'auto');
                self.$form.setStyle('left', '35%');
            }

        }, 10);

        /*if (mode == 2) {
         return;
         }*/

        var t = $$('span.swiff-uploader-box');
        for (var i = 0; i < t.length; i++) {
            t[i].setStyle('display', 'inline');
        }
    },

    createContestInfo: function () {
        var desc_wr = new Element('div');
        var div1 = new Element('div', {'style': 'float:left;'});
        var p1 = new Element('p');
        var div2 = new Element('div', {'style': 'float:right;'});
        var div3 = new Element('div', {'class': 'clear'});
        var img = new Element('img', {'src': 'application/modules/Hecontest/externals/images/hecontest-no-prize-photo.png'});

        p1.set('text', ' Some description here');
        p1.inject(div1);
        img.inject(div2);
        div1.inject(desc_wr);
        div2.inject(desc_wr);
        div3.inject(desc_wr);

        var mySecondElement = new Element('div', {id: 'mySecondElement'});
        var myThirdElement = new Element('div', {id: 'myThirdElement'});
        return desc_wr;
    },

    hideJoinForm: function () {
        var self = this;
        self.$form.setStyle('transform', 'translate(240px, -150%)');
        self.$form.setStyle('-webkit-transform', 'translate(240px, -150%)');
        self.$form.setStyle('-ms-transform', 'translate(240px, -150%)');
        setTimeout(function () {
            self.$screen.setStyle('display', 'none');
            self.$form.setStyle('display', 'none');
        }, 10);
        if (self.$info) {
            //self.$info.set('html', '');
        }
        self.clearForm();
        var t = $$('span.swiff-uploader-box');
        for (var i = 0; i < t.length; i++) {
            t[i].setStyle('display', 'none');
        }
    },

    clearForm: function () {
        var self = this;
        if (self.$form.getElement('input#fancyuploadfileids')) {
            self.$form.getElement('input#fancyuploadfileids').set('value', '');
        }
        var terms = self.$form.getElement('checkbox#terms');
        if (terms) {
            terms.checked = false;
        }

        if (self.$form.getElement('textarea#hecontest-description')) {
            self.$form.getElement('textarea#hecontest-description').set('text', '');
        }

        if (self.afterUpload) {
            self.afterUpload = false;
            self.clearList();
        } else {
            self.removeFiles();
        }
    },
    clearList: function () {
        var self = this;
        var list = self.$form.getElement('ul#demo-list-hecontest');
        list.set('html', '');

        if ($('demo-browse-hecontest'))
            $('demo-browse-hecontest').show();
    },
    removeFiles: function () {
        var self = this;
        var remove = self.$form.getElement('a.file-remove');
        if (remove)
            remove.click();
    },
    hideJoinBtns: function () {
        var btns = $$('.hecontest_join_button');
        for (var i = 0; i < btns.length; i++) {
            btns[i].setStyle('display', 'none');
        }
    },

    joinContest: function (el) {
        var error = false;
        var self = this;

        var file_id = $('fancyuploadfileids').get('value').trim();
        var description = $('hecontestdescription').get('value').trim();
        var contest_id = 0;
        if($$('input#contest_id')[0]) contest_id =$$('input#contest_id')[0].value.trim();
        var params = {
            file_id: file_id,
            description: description,
            contest_id: contest_id,
            format: 'json'
        };

        self.$error = new Element('div', {style: 'background-color:salmon;height: 10px; width: auto;'});
        if (!file_id) {
            self.showError('div#demo-status-hecontest');
            error = true;
        }
        if (!description) {
            self.showError('textarea#hecontestdescription');
            error = true;
        }
        var terms = $('terms');
        if (terms) {
            params.terms = terms.checked;
            if (!terms.checked) {
                self.showError('div#terms-element');
                error = true;
            }
        }

        if (error)
            return;

        new Request.JSON({
            url: en4.core.baseUrl + "contest/join",
            method: 'post',
            data: params,
            onSuccess: function (response) {
                
                if (response.status) {
                    he_show_message("Thank you for your participation!", "", 5000);
                    self.afterUpload = true;
                    self.hideJoinForm();
                    /*self.hideJoinBtns();*/
                    window.location.href = response.redirect;
                } else {

                }
            }
        }).send();
    },

    onAir: false,
    isLiked: false,
    vote: function (el, photo_id) {
        var self = this;
        if (self.onAir) {
            return;
        }
        self.onAir = true;
        var params = {
            format: 'json',
            photo_id: photo_id
        };
        new Request.JSON({
            url: en4.core.baseUrl + "contest/vote",
            method: 'post',
            data: params,
            onSuccess: function (response) {
                self.onAir = false;
                if (response.status) {
                    var aL = $('hecontest-viewer-photo-' + photo_id).getElement('a#another-like');
                    aL.set('html', " " + response.caption);
                    $(el).set('html', response.caption);

                    if (aL.hasClass('hei-thumbs-up')) {
                        aL.addClass('hei-thumbs-down');
                        aL.removeClass('hei-thumbs-up');
                    } else if (aL.hasClass('hei-thumbs-down')) {
                        aL.addClass('hei-thumbs-up');
                        aL.removeClass('hei-thumbs-down');
                    }

                    self.isLiked = true;
                } else {

                }
            }
        }).send();
    },

    $voteScreen: {},
    $voteForm: {},
    hideVoteForm: function () {
        var self = this;
        if (!self.allowHide) {
            return;
        }
        self.$voteForm.setStyle('transform', 'translate(50px, -150%)');
        self.$voteForm.setStyle('-webkit-transform', 'translate(50px, -150%)');
        self.$voteForm.setStyle('-ms-transform', 'translate(50px, -150%)');
        setTimeout(function () {
            self.$voteScreen.setStyle('display', 'none');
            self.hidevForm();
        }, 100);
    },
    showvForm: function () {
        var self = this;
        self.$voteForm.setStyle('display', 'block');

        if (!hecontestCore.isIE()) {
            self.$voteForm.setStyle('transform', 'translate(50px,-150%)');
            self.$voteForm.setStyle('-webkit-transform', 'translate(50px,-150%)');
            self.$voteForm.setStyle('-ms-transform', 'translate(50px,-150%)');
        }

        setTimeout(function () {
            self.$voteForm.setStyle('transform', 'translate(50px,0)');
            self.$voteForm.setStyle('-webkit-transform', 'translate(50px,0)');
            self.$voteForm.setStyle('-ms-transform', 'translate(50px,0)');

            if (hecontestCore.isIE()) {
                self.$voteForm.setStyle('display', 'block');
                self.$voteForm.setStyle('width', 'auto');
                self.$voteForm.setStyle('height', 'auto');
                self.$voteForm.setStyle('left', '25%');
            }

        }, 10);

    },
    hidevForm: function () {
        var self = this;
        self.$voteForm.setStyle('display', 'none');
        self.$voteForm.set('html', '');
    },
    showLoader: function () {
        $('hecontest-vote-loader').setStyle('display', 'block');
    },
    hideLoader: function () {
        $('hecontest-vote-loader').setStyle('display', 'none');
    },
    showCommentForm: function (el) {
        var self = this;
        var form = $(el).getParent('div');
        form.getElement('form#comment-form').setStyle('display', '');
        $('hecontest-vote-comment-form-wrapper').scrollTo(0, 200);
        $('hecontest-vote-comment-form-wrapper').getElement('textarea#body').focus();
        $(el).setStyle('opacity', '0');
        var submit = form.getElement('button#submit');
        submit.set('type', 'button');
        var body = $('body');
        if (!body.hasClass('hecontest-textarea')) {
            body.addClass('hecontest-textarea')
        }

        if (!submit.hasClass('hecontest_widget_button')) {
            submit.addClass('hecontest_widget_button');
            submit.addEventListener('click', function () {
                self.postComment(form, el);
            });
        }
    },
    postComment: function (form, el) {
        var body = form.getElement('textarea#body').get('value');
        var id = form.getElementById('identity').get('value');

        new Request.JSON({
            url: en4.core.baseUrl + "core/comment/create",
            method: 'post',
            evalScripts: true,
            data: {
                body: body,
                type: 'hecontest_photo',
                identity: id,
                format: 'json',
                id: id
            },
            onSuccess: function (response, xml) {
                var start = response.body.search('<ul');
                var end = response.body.search('</ul');
                var ul = response.body.substring(start + 4, end);

                form.getElementById('hecontest-vote-comment-form-wrapper').getElement('ul').set('html', ul);
                $('comment-form').setStyle('display', 'none');
                form.getElement('textarea#body').set('value', '');
                $('hecontest-vote-comment-form-wrapper').scrollTo(0, 1200);
                el.setStyle('opacity', '1');
                $('comments_block_label').setStyle('display', 'block');
            }
        }).send();
        return false;
    },

    showError: function (selector) {
        var self = this;
        self.$form.getElement(selector).setStyle('background-color', self.color);
        setTimeout(function () {
            self.$form.getElement(selector).setStyle('background-color', '');
        }, self.delay);
    },

    getIdFromUrl: function () {
        return document.location.hash.substr(1);
    },

    returnFancyUploadCreate: function (url, path, parent_id) {
        if (!$('demo-status-hecontest')) {
            return null;
        }
        return new FancyUpload2($('demo-status-hecontest'), $('demo-list-hecontest'), {
            verbose: false,
            multiple: false,
            appendCookieData: true,
            url: url,
            path: path,

            typeFilter: {
                'Images (*.jpg, *.png, *.gif, *.jpeg)': '*.jpg; *.png; *.gif; *.jpeg)'
            },
            target: 'demo-browse-hecontest',
            data: {
                format: 'json',
                'parent_id': parent_id
            },
            container: 'a-wrapper',

            onLoad: function () {
                $('demo-status-hecontest').removeClass('hide');
                if ($('demo-fallback-hecontest') != undefined) {
                    $('demo-fallback-hecontest').destroy();
                }
                this.target.addEvents({
                    click: function () {
                        return false;
                    },
                    mouseenter: function () {
                        this.addClass('hover');
                    },
                    mouseleave: function () {
                        this.removeClass('hover');
                        this.blur();
                    },
                    mousedown: function () {
                        this.focus();
                    }
                });

            },

            /**
             * Is called when files were not added, "files" is an array of invalid File classes.
             *
             * This example creates a list of error elements directly in the file list, which
             * hide on click.
             */
            onSelectFail: function (files) {
                files.each(function (file) {
                    new Element('li', {
                        'class': 'validation-error',
                        html: file.validationErrorMessage || file.validationError,
                        title: MooTools.lang.get('FancyUpload', 'removeTitle'),
                        events: {
                            click: function () {
                                this.destroy();
                            }
                        }
                    }).inject(this.list, 'top');
                }, this);
            },

            onComplete: function hideProgress() {
                var demostatuscurrent = document.getElementById("demo-status-hecontest-current");
                var demostatusoverall = document.getElementById("demo-status-hecontest-overall");

                demostatuscurrent.style.display = "none";
                demostatusoverall.style.display = "none";
            },

            onFileStart: function () {
                hecontestCore.uploadCount += 1;
            },

            onFileRemove: function (file) {
                hecontestCore.uploadCount -= 1;
                var file_id = file.photo_id;
                var request = new Request.JSON({
                    format: 'json',
                    method: 'post',
                    url: en4.core.baseUrl + "contest/remove-photo",
                    data: {
                        file_id: file_id
                    },
                    onSuccess: function (responseJSON) {
                        return false;
                    }
                });
                request.send();
                var fileids = document.getElementById('fancyuploadfileids');

                if (hecontestCore.uploadCount == 0) {
                    var demolist = document.getElementById("demo-list-hecontest");
                    var demosubmit = document.getElementById("submit-wrapper");
                    demolist.style.display = "none";
                    demosubmit.style.display = "none";
                }
                if ($('demo-browse-hecontest'))
                    $('demo-browse-hecontest').show();

                fileids.value = fileids.value.replace(file_id, "");
            },

            onSelectSuccess: function (file) {
                $('demo-list-hecontest').style.display = 'block';
                var demostatuscurrent = document.getElementById("demo-status-hecontest-current");
                var demostatusoverall = document.getElementById("demo-status-hecontest-overall");

                demostatuscurrent.style.display = "block";
                demostatusoverall.style.display = "block";
                hecontestCore.uploader.start();
            },
            /**
             * This one was directly in FancyUpload2 before, the event makes it
             * easier for you, to add your own response handling (you probably want
             * to send something else than JSON or different items).
             */
            onFileSuccess: function (file, response) {
                var json = new Hash(JSON.decode(response, true) || {});

                if (json.status == '1') {
                    file.element.addClass('file-success');
                    file.info.set('html', '<span>Upload complete.</span>');
                    var fileids = document.getElementById('fancyuploadfileids');

                    fileids.value = json.file_id;
                    file.photo_id = json.file_id;
                    if ($('demo-browse-hecontest'))
                        $('demo-browse-hecontest').hide();
                } else {
                    console.log("Some error");
                    console.log(response);
                }
            },

            /**
             * onFail is called when the Flash movie got bashed by some browser plugin
             * like Adblock or Flashblock.
             */
            onFail: function (error) {
                console.log("Fancy error - " + error);
            }
        });
    },

    countdown: true,
    finishHim: function () {
        var self = this;
        new Request.JSON({
            url: en4.core.baseUrl + "hecontest/index/finish",
            method: 'post',
            data: {
                format: 'json',
                finish: true
            },
            onSuccess: function (response) {
                if (response.status) {
                    document.location.href = document.location.href;
                }
            }
        }).send();
    },

    scrollDirection: 0,
    loadingMore: false,
    loadMore: function (mode) {
        var self = this;

        if (self.loadingMore) {
            return;
        }
        self.loadingMore = true;

        var url = en4.core.baseUrl + 'contest/index';
        if (mode == 2) {
            url = en4.core.baseUrl + 'contest/recent';
        }

        var page = Number($('hecontest-page').value);
        if (isNaN(page) || page <= 0) {
            return;
        }

        $('hecontest-load-more-loader').setStyle('display', '');

        var r = new Request.HTML({
            url: url,
            method: 'post',
            data: {
                ajax: true,
                page: page,
                contest_id: self.contestId
            },
            onComplete: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                var $container = new Element('div', {'html': responseHTML});
                var $items = $container.getElement('ul.hecontest-items').getChildren();
                $('hecontest-page').value = $container.getElement('input.hecontest-page').value;

                for (var i = 0; i < $items.length; i++) {
                    $items[i].inject($('hecontest-items'));
                }
                $('hecontest-load-more-loader').setStyle('display', 'none');
                self.loadingMore = false;

                hecontestViewer.update();
            }
        });
        r.send();
    },

};

var HecontestLikeButton = new Class({

    Implements: [Options],

    options: {
        object_type: '',
        object_id: 0,
        likeBtn: '',
        loader: '',
        menuHtml: '',
        menuId: '',
        suggestBtn: '',
        likeUrl: en4.core.baseUrl + 'like/like',
        unlikeUrl: en4.core.baseUrl + 'like/unlike',
        switcher: ''
    },

    unlike_class: 'unlike',

    like_class: 'like',

    block: false,

    aclass: 'hecontest_like_button',
    cclass: 'hecontest_like_button_container',

    menuContainer: null,

    initialize: function (options) {
        this.setOptions(options);
        this.options.likeBtn = $(this.options.likeBtn);
        this.options.loader = $(this.options.loader);
        this.options.switcher = $(this.options.switcher);

        if (this.options.likeBtn) {
            if (this.options.likeBtn.hasClass(this.unlike_class)) {
                this.setUnlike();
            } else {
                this.setLike();
            }
        }

        this.init_menu();
    },

    init_menu: function () {
        var $div = new Element('div', {'html': this.options.menuHtml, 'style': 'position:absolute;', 'id': this.options.menuId});
        this.menuContainer = $div = $div.getElements('.like_container_menu_wrapper')[0];
        $$('body')[0].appendChild($div);

        this.initPosition();

        this.options.suggestBtn = $$(this.options.suggestBtn)[0];
        this.init_suggest_link();

        if (this.options.switcher) {
            var self = this;
            this.options.switcher.addEvent('click', function () {
                self.toggle_menu();
            });
        }

        if (Smoothbox && Smoothbox.init) {
            Smoothbox.init();
        }
    },

    initPosition: function () {
        var $switcher = $(this.options.likeBtn).getNext();
        var position = $switcher.getPosition();
        if (this.menuContainer != undefined) {
            if (en4.orientation == 'rtl') {
                this.menuContainer.setStyle('left', position.x - this.menuContainer.getSize().x);
            }
            else {
                this.menuContainer.setStyle('left', position.x + 20);
            }
            this.menuContainer.setStyle('top', position.y);
        }
    },

    init_suggest_link: function () {
        var self = this;

        if (!this.options.suggestBtn) {
            return;
        }

        this.options.suggestBtn.addEvent('click', function (e) {
            e.stop();
            like.url.suggest = this.href;
            he_contacts.box('like', 'getFriends', 'like.suggest', en4.core.language.translate('like_Suggest to Friends'), {
                'object': self.options.object_type,
                'object_id': self.options.object_id
            }, 0);
        });
    },

    like: function () {
        var self = this;
        if (this.block) {
            return;
        }
        this.showLoader();
        this.block = true;
        new Request.JSON({
            method: 'post',
            url: self.options.likeUrl,
            data: {
                format: 'json'
            },
            onSuccess: function (response) {
                self.block = false;

                if (response.error) {
                    he_show_message(response.html, 'error', 3000);
                    return;
                }
                self.hideLoader();
                self.toggle();
                return true;
            }
        }).send();
    },

    unlike: function () {
        var self = this;
        if (this.block) {
            return;
        }
        self.showLoader();
        this.block = true;
        new Request.JSON({
            method: 'post',
            url: self.options.unlikeUrl,
            data: {
                format: 'json'
            },
            onSuccess: function (response) {
                self.block = false;

                if (response.error) {
                    he_show_message(response.html, 'error', 3000);
                    return;
                }
                self.hideLoader();
                self.toggle();
                return true;
            }
        }).send();
    },

    toggle: function () {
        var $link = $(this.options.likeBtn);
        if ($link.hasClass(this.unlike_class)) {
            this.setLike();
        } else {
            this.setUnlike();
        }
        this.initPosition();
    },

    toggle_menu: function () {
        var menu = this.menuContainer;
        var link = this.options.switcher;
        if (menu.hasClass('hidden')) {
            menu.removeClass('hidden');
            link.getElements('span')[0].set('class', 'hide_options');
            link.set('title', en4.core.language.translate('like_Hide'));
            this.initPosition();
        } else {
            menu.addClass('hidden');
            link.getElements('span')[0].set('class', 'show_options');
            link.set('title', en4.core.language.translate('like_Show Like'));
        }
    },

    setLike: function () {
        var $link = $(this.options.likeBtn);
        var self = this;
        var links = $$('.hecontest_like_button_container').getElement('.hecontest_like_button');
        links.each(function (item) {
            item.getElements('span')[0].set('class', 'like_button');
            item.getElements('span')[0].set('text', en4.core.language.translate('like_Like'));
            item.set('class', 'hecontest_like_button like_button_link ' + self.like_class);
            item.removeEvents('click');
            item.addEvent('click', function () {
                self.like();
            });
        });

        /*$link.getElements('span')[0].set('class', 'like_button');
         $link.getElements('span')[0].set('text', en4.core.language.translate('like_Like'));
         $link.set('class', 'hecontest_like_button like_button_link ' + self.like_class);
         $link.removeEvents('click');
         $link.addEvent('click', function(){
         self.like();
         });*/

        return this;
    },

    setUnlike: function () {
        var $link = $(this.options.likeBtn);
        var links = $$('.hecontest_like_button_container').getElement('.hecontest_like_button');
        var self = this;
        links.each(function (item) {
            item.removeEvents('click');
            item.getElements('span')[0].set('class', 'unlike_button');
            item.getElements('span')[0].set('text', en4.core.language.translate('like_Unlike'));
            item.set('class', 'hecontest_like_button like_button_link ' + self.unlike_class);
            item.addEvent('click', function () {
                self.unlike();
            });
        });
        /*$link.removeEvents('click');
         $link.getElements('span')[0].set('class', 'unlike_button');
         $link.getElements('span')[0].set('text', en4.core.language.translate('like_Unlike'));
         $link.set('class', 'hecontest_like_button like_button_link ' + self.unlike_class);
         $link.addEvent('click', function(){
         self.unlike();
         });*/
        return this;
    },

    showLoader: function () {
        $$('.hecontest_like_button_container').getElement('a').addClass('hidden');
        var switcher = $$('.hecontest_like_button_container').getElement('.hecontest_like_switcher');

        if (switcher) {
            switcher.forEach(function (entry) {
                if (entry)
                    entry.addClass('hidden');
            });
        }
        $$('.hecontest_like_button_container').getElement('.hecontest_like_button_loader').removeClass('hidden');
    },

    hideLoader: function () {
        $$('.hecontest_like_button_container').getElement('a').removeClass('hidden');
        var switcher = $$('.hecontest_like_button_container').getElement('.hecontest_like_switcher');

        if (switcher) {
            switcher.forEach(function (entry) {
                if (entry)
                    entry.removeClass('hidden');
            });
        }
        $$('.hecontest_like_button_container').getElement('.hecontest_like_button_loader').addClass('hidden');

    }

});

var hecontestViewer = {

        $content: {},
        $close: {},
        $wrapper: {},
        $slideLeft: {},
        $slideRight: {},

        $prev: {},
        $next: {},
        $active: {},
        $photos: {},
        $comments: {},

        prevId: 0,
        nextId: 0,
        prevN: 0,
        nextN: 0,
        activeN: 0,
        activeId: 0,
        photosIds: [],
        contestId: 0,
        startPhoto: 0,
        loadedPhotosIds: [],

        init: function (contest_id) {
            var self = this;
            self.contestId = contest_id;

            self.initControls();

            $$('.hecontest-item-content').each(function (el) {
                var id = el.get('href').split('#');
                self.photosIds.push(id[1]);
            });
        },

        update: function () {
            var self = this;
            self.photosIds = [];
            $$('.hecontest-item-content').each(function (el) {
                var id = el.get('href').split('#');
                self.photosIds.push(id[1]);
            });
        },

        isActive: false,
        fromHash: false,
        show: function (photo_id, from_hash) {
            var self = this;
            self.fromHash = from_hash;
            if (self.isActive) {
                return;
            }
            self.isActive = true;
            self.startPhoto = photo_id;
            self.$wrapper = $('hecontest-viewer-wrapper');
            if (!self.$wrapper.hasClass('active')) {
                self.$wrapper.addClass('active');
            }
            self.loadPhoto(photo_id);
            self.resize();
        },
        onAir: false,
        loadPhoto: function (photo_id) {
            var self = this;
            if (self.onAir) {
                return;
            }
            self.onAir = true;
            var $tmp = $('hecontest-viewer-photo-' + photo_id);
            if ($tmp) {
                self.$active = $tmp;
                self.activate(photo_id);
                self.updateControls(photo_id);
                self.changeHash();
                self.onAir = false;
                return
            }
            var r = new Request.HTML({
                url: en4.core.baseUrl + "contest/view",
                method: 'post',
                data: {
                    photo_id: photo_id,
                    contest_id: self.contestId
                },
                evalScripts: true,
                onComplete: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                    var imgHtml = self.getHtml(responseHTML, photo_id, 1);
                    imgHtml.inject($('hecontest-viewer-photos-wrapper'));
                    var commentsHtml = self.getHtml(responseHTML, photo_id, 2);
                    commentsHtml.inject($('hecontest-viewer-photo-comment'));
                    var descrHtml = self.getHtml(responseHTML, photo_id, 3);
                    descrHtml.inject($('hecontest-viewer-photos-desc'));
                    var controlsHtml = self.getHtml(responseHTML, photo_id, 4);
                    controlsHtml.inject($('hecontest-controls'));

                    var votersHtml = self.getHtml(responseHTML, photo_id, 5);
                    votersHtml.inject($('hecontest-viewer-photo-voters'));

                    self.$photos = $('hecontest-viewer-photos-wrapper').getChildren();
                    self.$comments = $('hecontest-viewer-photo-comment').getChildren();

                    self.$active = $('hecontest-viewer-photo-' + photo_id);
                    self.activate(photo_id);

                    self.updateControls(photo_id);
                    self.onAir = false;

                    self.resize();
                    self.changeHash();
                }
            });
            r.send();
        },

        attachCreateComment: function (formElement, comments_block) {
            var bind = this;
            formElement.removeEvent('submit');
            formElement.addEvent('submit', function (event) {
                event.stop();
                var form_values = formElement.toQueryString();
                form_values += '&format=json';
                form_values += '&id=' + formElement.identity.value;
                en4.core.request.send(new Request.JSON({
                    url: en4.core.baseUrl + 'core/comment/create',
                    data: form_values
                }), {
                    'element': comments_block
                });
            });
        },


        getHtml: function (responseHTML, photo_id, type) {
            var self = this;
            var t = new Element('div');
            t.set('html', responseHTML);
            var $html = '';
            var $wrapper = '';
            switch (type) {
                case 1:
                    $html = t.getChildren('div#another-like-wrapper').get('html');
                    $html += t.getChildren('div#hecontest-photo-img').get('html');
                    $wrapper = new Element('div', {
                        class: 'hecontest-viewer-photo active fade',
                        id: 'hecontest-viewer-photo-' + photo_id
                    });
                    break;
                case 2:
                    $html = t.getChildren('div#hecontest-photo-comments').get('html');
                    $wrapper = new Element('div', {
                        class: 'hecontest-viewer-comment active fade',
                        id: 'hecontest-viewer-comment-' + photo_id
                    });
                    break;
                case 3:
                    $html = t.getChildren('div#hecontest-photo-description').get('html');
                    $wrapper = new Element('div', {
                        class: 'hecontest-viewer-descr active fade',
                        id: 'hecontest-viewer-descr-' + photo_id
                    });
                    break;
                case 4:
                    $html = t.getChildren('div#hecontest-photo-controls').get('html');
                    $wrapper = new Element('div', {
                        class: 'hecontest-controls-item active fade',
                        id: 'hecontest-controls-item-' + photo_id
                    });
                    break;
                case 5:
                    $html = t.getChildren('div#hecontest-photo-voters').get('html');
                    $wrapper = new Element('div', {
                        class: 'hecontest-viewer-voters active fade',
                        id: 'hecontest-viewer-voters-' + photo_id
                    });
                    break;
                default:
            }

            $wrapper.set('html', $html);
            return $wrapper;
        },


        activate: function (photo_id) {
            var self = this;
            var img = $('hecontest-viewer-photo-' + photo_id);
            var cmnt = $('hecontest-viewer-comment-' + photo_id);
            var desc = $('hecontest-viewer-descr-' + photo_id);
            var cntrl = $('hecontest-controls-item-' + photo_id);
            var voters = $('hecontest-viewer-voters-' + photo_id);

            self.on(img);
            self.on(cmnt);
            self.on(desc);
            self.on(cntrl);
            self.on(voters);

            $('hecontest-controls').addClass('active');
        },
        on: function (el) {
            if (!el) return;
            if (!el.hasClass('fade')) {
                el.addClass('fade');
            }
            if (!el.hasClass('active')) {
                el.addClass('active');
            }
            //el.setStyle('opacity', 1);
        },
        deactivate: function (photo_id) {
            var self = this;
            var img = $('hecontest-viewer-photo-' + photo_id);
            var cmnt = $('hecontest-viewer-comment-' + photo_id);
            var desc = $('hecontest-viewer-descr-' + photo_id);
            var cntrl = $('hecontest-controls-item-' + photo_id);
            var voters = $('hecontest-viewer-voters-' + photo_id);

            self.off(img);
            self.off(cmnt);
            self.off(desc);
            self.off(cntrl);
            self.off(voters);

            $('hecontest-controls').removeClass('active');
        },
        off: function (el) {
            if (!el) return;
            if (el.hasClass('active')) {
                el.removeClass('active');
            }
            if (el.hasClass('fade')) {
                el.removeClass('fade');
            }
        },

        processNumbers: function (active) {
            var self = this;
            self.activeN = active;
            self.prevN = active - 1;
            self.nextN = active + 1;

            self.activeId = self.photosIds[active];

            if (self.prevN < 0) {
                self.$slideLeft.setStyle('display', 'none');
            } else {
                self.prevId = self.photosIds[self.prevN];
                self.$slideLeft.setStyle('display', 'block');
            }
            if (self.nextN > self.photosIds.length - 1) {
                self.$slideRight.setStyle('display', 'none');
            } else {
                self.nextId = self.photosIds[self.nextN];
                self.$slideRight.setStyle('display', 'block');
            }
        },
        /*
         Проблема с переходом с виджета
         * вариант 1. убрать контролы скролла и подрхтовать закрытие
         * вариант 2. привязкак к текущим фоткам, если нет активной
         * */
        updateControls: function (photo_id) {
            var self = this;
            for (var i = 0; i < self.photosIds.length; i++) {
                if (self.photosIds[i] == photo_id) {
                    self.processNumbers(i);
                }
            }
            if (self.activeId == 0) {
                self.activeId = photo_id;
                self.$slideLeft.setStyle('display', 'none');
                self.$slideRight.setStyle('display', 'none');
            }
        },

        initControls: function () {
            var self = this;
            self.$close = $$('.hecontest-viewer-slide-close');
            self.$slideLeft = $$('.hecontest-viewer-slide-left');
            self.$slideRight = $$('.hecontest-viewer-slide-right');

            self.$content = $('hecontest-viewer-content');

            self.$close.addEvent('click', function () {
                self.close();
            });
            self.$slideLeft.addEvent('click', function () {
                self.slideLeft();
            });
            self.$slideRight.addEvent('click', function () {
                self.slideRight();
            });


            $(window).addEvent('resize', function (e) {
                self.resize();
            });
        },

        like: function () {
            var self = this;
        },
        isCommentFormVisible: false,
        comment: function () {
            var self = this;
            if (self.isCommentFormVisible) {
                self.hideCommentForm();
            } else {
                self.showCommentForm();
            }
        },
        showCommentForm: function () {
            var self = this;
            var $form = $$('#hecontest-viewer-comment-' + self.activeId + ' #comment-form')[0];

            self.attachCreateComment($form, $form.getParent());

            $form.setStyle('display', '');

            var $body = $form.getChildren('#body')[0]
            $body.focus();

            $form.scrollTo(0, 1200);
            self.isCommentFormVisible = true;
        },
        hideCommentForm: function () {
            var self = this;
            var $form = $$('#hecontest-viewer-comment-' + self.activeId + ' #comment-form')[0];
            $form.setStyle('display', 'none');
            self.isCommentFormVisible = false;
        },
        share: function (url) {
            console.log(url);
            Smoothbox.open(url);
        },
        suggest: function () {
            var self = this;
            HESuggest.open();
        },

        resize: function () {
            var self = this;

            var width = window.innerWidth;
            var height = window.innerHeight;

            var comment_w = 400; // fixed width of comments
            var bar_h = 70; // height width of bottom bar

            width -= 50; // for margin (25px left and right)
            height -= 50; // for margin (25px for top and bottom)

            var top_content_h = height - bar_h;

            $$('.hecontest-viewer-content-table .td').setStyle('height', top_content_h);
            self.$content.setStyle('width', width + 'px');
            $('hecontest-viewer-info-wrapper').setStyle('width', comment_w - 3);

            self.$slideLeft.setStyle('line-height', height + 'px');
            self.$slideRight.setStyle('line-height', height + 'px');
            self.$slideRight.setStyle('right', comment_w + 20 + 'px');
        },

        close: function () {
            var self = this;
            self.isActive = false;
            if (self.$wrapper.hasClass('active')) {
                self.$wrapper.removeClass('active');
            }
            self.deactivate(self.activeId);
            self.processNumbers(0);

            if (hecontestCore.isLiked) {
                document.location.reload();
            }
        },

        slideLeft: function () {
            var self = this;

            self.deactivate(self.activeId);
            self.loadPhoto(self.prevId);
        },
        slideRight: function () {
            var self = this;

            self.deactivate(self.activeId);
            self.loadPhoto(self.nextId);
        },

        changeHash: function () {
            var self = this;
            document.location.hash = '#' + self.activeId;
        }
    }
    ;

function showCountDown(timestamp, id) {
    var $widget = $(id);
    var $months = $widget.getElement('div#container-months');
    var $days = $widget.getElement('div#container-days');
    var $hours = $widget.getElement('div#container-hours');
    var $minutes = $widget.getElement('div#container-minutes');
    var $seconds = $widget.getElement('div#container-seconds');

    $months.getElement('div.digit').set('html', timestamp.months);
    $days.getElement('div.digit').set('html', timestamp.days);
    $hours.getElement('div.digit').set('html', timestamp.hours);
    $minutes.getElement('div.digit').set('html', timestamp.minutes);
    $seconds.getElement('div.digit').set('html', timestamp.seconds);

    if (timestamp.months == 0 && timestamp.days == 0 &&
        timestamp.hours == 0 && timestamp.minutes == 0 &&
        timestamp.seconds == 0) {
        hecontestCore.finishHim();
        hecontestCore.countdown = false;
    }
}
function showCountDownEach(timestamp, id) {
    var $widget = $(id);
    var $months = $widget.getElement('div#container-months');
    var $days = $widget.getElement('div#container-days');
    var $hours = $widget.getElement('div#container-hours');
    var $minutes = $widget.getElement('div#container-minutes');
    var $seconds = $widget.getElement('div#container-seconds');

    $months.getElement('div.digit').set('html', timestamp.months);
    $days.getElement('div.digit').set('html', timestamp.days);
    $hours.getElement('div.digit').set('html', timestamp.hours);
    $minutes.getElement('div.digit').set('html', timestamp.minutes);
    $seconds.getElement('div.digit').set('html', timestamp.seconds);

    if (timestamp.months == 0 && timestamp.days == 0 &&
      timestamp.hours == 0 && timestamp.minutes == 0 &&
      timestamp.seconds == 0) {
        hecontestCore.finishHim();
        hecontestCore.countdown = false;
    }
}