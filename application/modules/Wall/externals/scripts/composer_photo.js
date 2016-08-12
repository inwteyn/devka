/* $Id: composer_photo.js 18.06.12 10:52 michael $ */


Wall.Composer.Plugin.Photo = new Class({

    Extends: Wall.Composer.Plugin.Interface,

    name: 'photo',

    options: {
        title: 'Add Photo',
        lang: {},
        requestOptions: false,
        fancyUploadEnabled: true,
        fancyUploadOptions: {},
        count_photo: 0,
        photo_ids: [],
        user_albums: {}
    },
    photo_tempIds: [],
    initialize: function (options) {
        this.elements = new Hash(this.elements);
        this.params = new Hash(this.params);
        this.parent(options);
    },

    attach: function () {
        this.parent();
        this.makeActivator();
        return this;
    },

    detach: function () {
        this.parent();
        return this;
    },

    activate: function () {
        if (this.active) return;
        this.parent();

        this.makeMenu();
        this.makeBody();
        this.photo_tempIds = [];
        // Generate form
        var fullUrl = this.options.requestOptions.url;
        this.elements.form = new Element('form', {
            'id': 'compose-photo-form',
            'class': 'compose-form',
            'method': 'post',
            'action': fullUrl,
            'enctype': 'multipart/form-data'
        }).inject(this.elements.body);

        this.elements.formInput = new Element('input', {
            'id': 'compose-photo-form-input',
            'class': 'compose-form-input-wall',
            'type': 'file',
            'name': 'Filedata',
            'events': {
                'change': this.doRequest.bind(this)
            }
        }).inject(this.elements.form);
        var wall_feed_new = Wall.feeds.items[this.getComposer().options.feed_uid];
        var subject_check = wall_feed_new.options.subject_guid;

        if (!subject_check) {
            this.elements.formSubmit = new Element('a', {
                'class': 'buttonlink photo_wall_button  first_child',
                'href': 'javascript:void(0);',
                'html': '<i class="hei hei-picture-o"></i><span>' + this._lang('Create Album') + '</span>',
                'events': {
                    'click': function (e) {
                        e.stop();
                        this.album();
                    }.bind(this)
                }
            }).inject(this.elements.body);
        }

        // Try to init fancyupload
        if (this.options.fancyUploadEnabled && this.options.fancyUploadOptions) {
            this.elements.formFancyContainer = new Element('div', {
                'styles': {
                    //'display' : 'none',
                    'visibility': 'hidden'
                }
            }).inject(this.elements.body);

            // This is the browse button
            this.elements.formFancyFile = new Element('a', {
                'href': 'javascript:void(0);',
                'id': 'compose-photo-form-fancy-file-wall',
                'class': 'buttonlink photo_wall_button',
                'style': 'position: relative;',
                'html': '<i class="hei hei-camera"></i><span>' + this._lang('Upload Photos') + '</span>'
            }).inject(this.elements.formFancyContainer);


            // This is the status
            this.elements.formFancyStatus = new Element('div', {
                'html': '<div style="display:none;">\n\
            <div class="demo-status-overall" id="demo-status-overall" style="display:none;">\n\
              <div class="overall-title"></div>\n\
              <img src="" class="progress overall-progress" />\n\
            </div>\n\
            <div class="demo-status-current" id="demo-status-current" style="display:none;">\n\
              <div class="current-title"></div>\n\
              <img src="" class="progress current-progress" />\n\
            </div>\n\
            <div class="current-text"></div>\n\
          </div>'
            }).inject(this.elements.formFancyContainer);

            // This is the list
            this.elements.formFancyList = new Element('div', {
                'styles': {
                    'display': 'none'
                }
            }).inject(this.elements.formFancyContainer);

            var self = this;
            var opts = $merge({
                url: fullUrl,
                appendCookieData: true,
                multiple: true,
                typeFilter: {
                    'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
                },
                target: self.elements.formFancyFile,
                container: self.elements.formFancyFile,
                // Events
                onLoad: function () {

                    self.elements.formFancyContainer.setStyle('display', '');
                    self.elements.formFancyContainer.setStyle('visibility', 'visible');
                    //self.elements.form.setStyle('display', 'none');
                    self.elements.form.destroy();

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
                onSelectSuccess: function () {
                    self.makeLoading('invisible');
                    this.start();
                },
                onError: function () {
                    var error = MooTools.lang.get('FancyUpload', 'fileError').substitute(this);
                    alert(error);
                },
                onFileSuccess: function (file, response) {
                    response = response.trim();
                    var json = new Hash(JSON.decode(response, true) || {});
                    self.doProcessResponse(json);
                },
                onComplete: function () {
                    if (self.elements.loading) self.elements.loading.destroy();
                }
            }, this.options.fancyUploadOptions);

            try {
                this.elements.formFancyUpload = new FancyUpload2(this.elements.formFancyStatus, this.elements.formFancyList, opts);
            } catch (e) {
            }

            this.elements.camera = new Element('a', {
                'href': 'javascript:void(0);',
                'class': 'buttonlink photo_wall_button',
                'html': '<i class="hei hei-video-camera"></i><span>' + en4.core.language.translate('WALL_COMPOSE_CAMERA') + '</span>',
                'events': {
                    click: function () {
                        var camera = new Wall.Camera();
                        camera.addEvent('onSuccess', function (obj) {
                            self.doProcessResponse(obj);
                        });
                    }
                }
            }).inject(this.elements.formFancyContainer);


        }
    },
    album: function () {
        window.photo_ids_del = [];
        var self = this;
        var body = this.bodyAlbum = $$('.photo_download_contaner')[0];


        /*  var wb  = (window.getSize().x - body.getSize().x)/2;
         body.setStyle('left',wb+'px')*/

        var status_bar = en4.core.baseUrl + 'application/modules/Wall/externals/images/loader.gif';


        var form = new Element('form', {
            'id': 'compose-photo-form',
            'class': 'compose-form',
            'method': 'post',
            'style': 'box-sizing: border-box; float: left; padding: 2px; width: 100%;',
            'action': '',
            'enctype': 'multipart/form-data'
        }).inject(body);

        var user_albums = this.options.user_albums;

        if (user_albums) {
            var select_albums = new Element('select', {
                'name': 'composer_select_album_name',
                'id': 'composer_select_album_name'
            });

            select_albums.grab(new Element('option', {
                value: 0,
                selected: 'yes',
                text: en4.core.language.translate('Select album')
            }));
            var new_arr = [];
            for (var key in user_albums) {
                new_arr.push(user_albums[key].toLowerCase() + '~' + key);
            }
            new_arr.sort();

            var len = new_arr.length;
            for (var i = 0; i < len; i++) {
                var tmp_arr = new_arr[i].split('~');
                if (user_albums[tmp_arr[1]] != en4.core.language.translate('Select album')) {
                    select_albums.grab(new Element('option', {value: tmp_arr[1], text: user_albums[tmp_arr[1]]}));
                }
            }
        }


        var formInput = new Element('input', {
            'id': 'title_album_wall_composer',
            'class': 'compose-form-input',
            'type': 'text',
            'name': 'title',
            'placeholder': en4.core.language.translate('Album name')
        });

        var formInputText = new Element('textarea', {
            'id': 'description_album',
            'class': 'compose-form-input_wall_album',
            'placeholder': this._lang('Album description')
        });

        if (user_albums) {
            select_albums.inject(form);
        } else {
            formInput.addClass('title_album_wall_composer_full');
        }

        formInput.inject(form);
        formInputText.inject(form);

        this.div_for_preview = new Element('div', {
            'id': 'contaner_for_preview',
            'style': ''
        }).inject(body);
        var formFancyContainer = this.formFancyContainerAlbum = new Element('div', {
            'styles': {
                //'display' : 'none',
                'visibility': 'hidden'
            },
            'style': '   clear: both;float: none;margin-left: 3px;width: 155px;bottom: 15px; position: absolute; '
        }).inject(body);

        // This is the browse button
        var formFancyFile = new Element('a', {
            'href': 'javascript:void(0);',
            'id': 'compose-photo-form-fancy-filE-album-wall',
            'class': 'button_wall_add_photo ',
            'style': 'width:100px;  padding:0;position: relative; ',
            'html': en4.core.language.translate('Upload Photos')
        }).inject(formFancyContainer);
        window.onbeforeunload = OnBeforeUnLoad;
        window.onunload = after;
        function OnBeforeUnLoad() {
            return 'Do you really wanna leave? If you leave, all photos will be lost';
        }

        function after(evt) {
            var req = new Request({
                method: 'get',
                url: en4.core.baseUrl + 'wall/index/album',
                data: {
                    'do': '1',
                    'photos_id_del': self.options.photo_ids
                },
                onComplete: function (response) {
                }
            }).send();
        }

        // This is the status
        var formFancyStatus = new Element('div', {
            'html': '<div style="">\n\
        <div class="demo-status-overall" id="demo-status-overall" style="display: none">\n\
          <div class="overall-title"></div>\n\
          <img src="' + status_bar + '" class="progress overall-progress" />\n\
            </div>\n\
            <div class="demo-status-current" id="demo-status-current" style="">\n\
              <div class="current-title" style="display: none"></div>\n\
              <img src="' + status_bar + '" class="progress current-progress" style = " position: absolute;      right: 5px;    top: 0;"/>\n\
            </div>\n\
            <div class="current-text" style="display: none"></div>\n\
          </div>',
            'style': 'display: none;'

        }).inject(formFancyContainer);

        // This is the list
        var formFancyList = new Element('div', {
            'styles': {
                'display': 'none'
            }
        }).inject(formFancyContainer);

        var self = this;
        var opts = $merge({
            url: '',
            appendCookieData: true,
            multiple: true,
            typeFilter: {
                'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
            },
            target: formFancyFile,
            container: formFancyFile,
            // Events
            onLoad: function () {
                $$('.photo_download_contaner .demo-status-current')[0].setStyle('display:block');
                formFancyContainer.setStyle('display', '');
                formFancyContainer.setStyle('visibility', 'visible');
                //self.elements.form.setStyle('display', 'none');
                //form.destroy();
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
            onSelectSuccess: function () {
                var demostatuscurrent = document.getElementById("demo-status-current");

                this.start();
            },

            onQueue: function () {
                self.loading.setStyle('display', 'block');
            },
            onComplete: function () {
                self.loading.setStyle('display', 'none');
            },


            onFileSuccess: function (file, response) {
                self.loading.setStyle('display', 'none');
                response = response.trim();
                var json = new Hash(JSON.decode(response, true) || {});
                self.doProcessResponseAlbum(json);

            }
        }, this.options.fancyUploadOptions);
        var window_w = window.getSize().x;
        var window_h = window.getSize().y;
        var composer_w = window_w - 400;
        var composer_h = window_h - 150;
        if (composer_w <= 760) {
            composer_w = 760;
        }

        $$('.photo_download_contaner').setStyles({
            'width': composer_w + 'px',
            'height': composer_h + 'px',
            'left': '200px',
            'top': '50px'
        });
        var contaner = $('contaner_for_preview');
        var count = contaner.getSize().x / 245;
        var withr = (contaner.getSize().x / count.toInt()) - 15;

        contaner.getChildren('div').each(function (elem) {
            elem.setStyles({
                'width': withr + 'px'
            })
        });

        var contaner_h = $$('.photo_download_contaner').getSize()[0].y;
        var photos_h = contaner_h - 200;
        this.div_for_preview.setStyle('height', photos_h);

        window.addEvent('resize', function () {
            var window_w = window.getSize().x;
            var window_h = window.getSize().y;
            var composer_w = window_w - 400;
            var composer_h = window_h - 150;
            if (composer_w <= 760) {
                composer_w = 760;
            }
            $$('.photo_download_contaner').setStyles({
                'width': composer_w + 'px',
                'height': composer_h + 'px',
                'left': '200px',
                'top': '50px'
            })
            var contaner = $('contaner_for_preview');
            var count = contaner.getSize().x / 245;
            var withr = (contaner.getSize().x / count.toInt()) - 15;

            contaner.getChildren('div').each(function (elem) {
                elem.setStyles({
                    'width': withr + 'px'
                })
            });
            var contaner_h = $$('.photo_download_contaner').getSize()[0].y;
            var photos_h = contaner_h - 200;
            self.div_for_preview.setStyle('height', photos_h);
        });
        try {
            var formFancyUpload = new FancyUpload2(formFancyStatus, formFancyList, opts);
        } catch (e) {
        }
        this.loading = new Element('div', {
            'html': '<img src="' + status_bar + '" class="loading" />',
            style: 'display: none;     position: absolute;    right: -20px;    top: 5px;',
            'class': 'loading_wall_bar'
        }).inject(formFancyFile);


        var button_save = new Element('a', {
            'class': 'button_wall_add_photo',
            'id': 'save_button',
            'style': ' position: absolute;    right: 20px;      bottom: 15px;    width: 130px; cursor:pointer;',
            'html': en4.core.language.translate('Create Album')
        }).inject(body);
        var select_privacy = new Element('select', {
            'name': 'privacy_album_conposer',
            'id': 'privacy_album_conposer'
        }).inject(body);
        select_privacy.grab(new Element('option', {value: 'everyone', selected: 'yes', text: 'Public'}));
        select_privacy.grab(new Element('option', {value: 'owner_network', text: 'My Friends & Networks'}));
        select_privacy.grab(new Element('option', {value: 'owner_member', text: 'My Friends'}));
        select_privacy.grab(new Element('option', {value: 'owner', text: 'Only me'}));
        button_save.addEvent('click', function () {
            self.save_album();
        });

        if (user_albums) {
            select_albums.addEvent('change', function () {
                if (this.value != 0) {
                    formInput.hide();
                    formInputText.hide();
                    select_privacy.hide();
                    button_save.set('html', en4.core.language.translate('Add Photos'));
                } else {
                    formInput.show();
                    formInputText.show();
                    select_privacy.show();
                    button_save.set('html', en4.core.language.translate('Create Album'));
                }
            });
        }

        body.setStyle('display', 'block');
        $$('.background_wall_photo_download').setStyle('display', 'block');
    },
    save_album: function () {

        if (window.submitCheck == 1) {
            return;
        }

        if (this.options.photo_ids.length <= 0) {
            return;
        }
        var self = this;
        var title = $('title_album_wall_composer');
        var photo_ids = this.options.photo_ids;
        var params = {};
        params.title = title.value;
        params.photo_ids = photo_ids;
        var url = en4.core.baseUrl + 'wall/index/album';
        params.format = 'json';
        params.p = 'album';
        params.composer = true;
        params.privacy = $('privacy_album_conposer').value;
        if (self.options.user_albums) {
            params.user_album = $('composer_select_album_name').value;
        }
        params.desc = $('description_album').value;
        var wall_feed_id = $$('.wallFeed')[0].get('id');
        var wall_feed_new = Wall.feeds.items[self.getComposer().options.feed_uid];
        params.subject = wall_feed_new.options.subject_guid;
        if (title.value.trim() == '' || !title.value.trim()) {
            if (self.options.user_albums) {
                if($('composer_select_album_name').value == 0){
                    title.setStyle('border', '1px solid red');
                    window.submitCheck = 0;
                    return;
                }
            } else {
                title.setStyle('border', '1px solid red');
                window.submitCheck = 0;
                return;
            }
        }
        window.submitCheck = 1;
        window.photo_ids_del = '';
        window.onbeforeunload = 'NULL';
        window.onunload = 'NULL';
        show_loading_album_download();
        Wall.request(url, params, function (obj) {


            if (self.options.is_timeline && false) {
                var feed = timeline.feed.object.get();
                var data;
                if (timeline.feed.object.setLasts(obj.last_date, obj.last_id)) {
                    feed.checkEmptyFeed();
                    data = $merge(feed.params, {
                        'minid': this.options.last_id,
                        'checkUpdate': true
                    });
                    feed.feed.getElements('.container-get-last').destroy();
                    feed.loadFeed(data, 'top', function () {
                        feed.checkActive = false;
                    });
                }
            } else {
                var wall = Wall.feeds.get(self.getComposer().options.feed_uid);
                wall.checkEmptyFeed();
                data = $merge(wall.params, {
                    'minid': obj.last_id,
                    'checkUpdate': false
                });
                wall.feed.getElements('.container-get-last').destroy();
                wall.loadFeed(data, 'top', function () {
                    wall.checkActive = false;
                });

            }
            window.submitCheck = 0;
            hide_loading_album_download();
            hide_form_wall_album();
            var wall_feed_id = $$('.wallFeed')[0].get('id');
            var wall_feed_new = Wall.feeds.items[self.getComposer().options.feed_uid];
            wall_feed_new.compose.deactivate();
            wall_feed_new.compose.close();
            self.options.photo_ids = [];
            self.params.photo_id = 0;
        });


    },
    doProcessResponseAlbum: function (responseJSON) {
        // An error occurred
        if (($type(responseJSON) != 'hash' && $type(responseJSON) != 'object') || $type(responseJSON.src) != 'string' || $type(parseInt(responseJSON.photo_id)) != 'number') {
            //this.elements.body.empty();
            this.makeError(this._lang('Unable to upload photo. Please click cancel and try again'), 'empty');
            return;
            //throw "unable to upload image";
        }
        var self = this;
        // Success
        this.params.set('rawParams', responseJSON);
        this.params.set('photo_id', responseJSON.photo_id);
        this.options.count_photo++;
        /*
         if(this.options.photo_ids['id'].length > order)
         this.options.photo_ids['id'][order] = responseJSON.photo_id;
         */
        this.options.photo_ids.push([responseJSON.photo_id.toInt(), 0, 0]);
        if (!window.photo_ids_del) {
            window.photo_ids_del = [];
        }
        window.photo_ids_del.push(responseJSON.photo_id);
        localStorage.setItem("photo_ids", window.photo_ids_del);
        this.elements.preview = Asset.image(responseJSON.src, {
            'id': 'compose-photo-preview-image_' + responseJSON.photo_id,
            'class': 'compose-preview-image',
            'rev': responseJSON.photo_id,
            'style': 'min-width: 200px; max-width:230px',
            'onload': function () {
                self.doImageLoadedAlbum(responseJSON.photo_id, responseJSON.src)
            }
        });

    },
    doImageLoadedAlbum: function (photo_id, scr) {
        var self = this;
        this.loading.setStyle('display', 'none');
        if (this.elements.formFancyContainerAlbum) this.formFancyContainerAlbum.destroy();
        this.elements.preview.erase('width');
        this.elements.preview.erase('height');


        var formInput = new Element('div', {
            'id': 'preview_' + photo_id,
            'data-id': photo_id,
            'class': 'order_photos',
            'cover': '0',
            'html': '',
            'style': 'background-image: url(' + scr + ');margin:4px;      width:235px;    height:235px;    position: relative;    box-shadow: 0 0 2px 0 rgba(0, 0, 0, 0.5);    overflow:hidden;    float:left;    background-size: cover;    background-position: center top;'

        }).inject(this.div_for_preview);

        this.elements.cover_button = new Element('div', {
            'id': 'cover_album_button_' + photo_id,
            'data-id': photo_id,
            'class': 'cover_album_button',
            'html': 'Album cover',
            'style': '  ',
            events: {
                'click': function (e) {
                    self.setCoverPhoto(this, photo_id);
                }
            }
        }).inject(formInput);
        $('preview_' + photo_id).addEvents({
            mouseover: function () {
                $('cover_album_button_' + photo_id).setStyle('opacity', '1');
            },
            mouseout: function () {
                $('cover_album_button_' + photo_id)
                    .setStyle('opacity', '0')
            }
        });
        this.elements.contaner_options_photo = new Element('div', {
            'id': 'contaner_options_photo_' + photo_id,
            'data-id': photo_id,
            'class': 'contaner_options_photo'
        }).inject(formInput);

        this.elements.name_photo = new Element('input', {
            'id': 'title_photo_album',
            'class': 'compose-form-input',
            'type': 'text',
            'name': 'title_photo_album',
            events: {
                'keyup': function (e) {
                    self.saveTitlePhoto(this, photo_id);
                }
            },
            'placeholder': this._lang('Title')
        }).inject(this.elements.contaner_options_photo);

        this.elements.rotate_photo = new Element('div', {
            'id': 'rotate_photo_' + photo_id,
            'data-id': photo_id,
            'class': 'rotate_photo_composer',
            'html': '<i class="hei hei-rotate-right" style="margin-top: 9px;"></i> ',
            events: {
                'click': function (e) {
                    self.saveRotatePhoto(this, photo_id);
                }
            }
        }).inject(this.elements.contaner_options_photo);
        var delete_button = new Element('div', {
            'id': 'delete_' + photo_id,
            'class': 'delete_button_composer_album',
            'data-id': photo_id,
            'html': '<i class="hei hei-trash-o" style="margin-top: 9px;"> '
        }).inject(this.elements.contaner_options_photo);
        var delete_loading = new Element('div', {
            'id': 'load_delete_' + photo_id,
            'class': 'load_delete_album_wall',
            'style': 'display:none;'
        }).inject(formInput);
        delete_button.addEvent('click', function () {
            self.delete_photo_album(this.get('data-id'));
        });
        var self = this;
        new Sortables($('contaner_for_preview'), {
            revert: {duration: 600, transition: 'cubic:out'},
            clone: true,
            onSort: function (element, clone) {
                clone.removeClass('order_photos');
                element.setStyle('opacity', '0');
                clone.setStyle('z-index', '100');
                clone.getElement('.contaner_options_photo').setStyle('display', 'none');
                clone.getElement('.cover_album_button').setStyle('display', 'none');
            },
            onComplete: function (element) {
                self.order_photos();
                element.setStyle('opacity', '1');

            }
        });
        var contaner = $('contaner_for_preview');
        var count = contaner.getSize().x / 245;
        var withr = (contaner.getSize().x / count.toInt()) - 15;

        contaner.getChildren('div').each(function (elem) {
            elem.setStyles({
                'width': withr + 'px'
            })
        });
        //this.elements.preview.inject(formInput);
        new Fx.Scroll($('contaner_for_preview')).toElement($('preview_' + photo_id));


        this.makeFormInputs();
    },
    saveRotatePhoto: function (el, id) {
        $('load_delete_' + id).setStyle('display', 'block');
        var req = new Request({
            method: 'get',
            url: en4.core.baseUrl + 'wall/index/rotatephoto',
            data: {
                'rotate': el.get('value'),
                'id': id
            },
            onComplete: function (response) {
                $('preview_' + id).setStyle('background-image', 'url("' + response + '")');
                $('load_delete_' + id).setStyle('display', 'none');
            }
        }).send();
    },
    saveTitlePhoto: function (el, id) {
        if (this.options.interval) {
            clearTimeout(this.options.interval);
        }
        this.options.interval = setTimeout(function () {
            var req = new Request({
                method: 'get',
                url: en4.core.baseUrl + 'wall/index/titlephoto',
                data: {
                    'name': el.get('value'),
                    'id': id
                },
                onComplete: function (response) {

                }
            }).send();
        }, 1000);
    },
    order_photos: function () {
        var items = $$('.order_photos');
        if (!items) {
            return;
        }
        var len = items.length;
        if (!len) {
            return;
        }
        this.options.photo_ids = [];

        for (var i = 0; i < len; i++) {
            var item = items[i];
            var id = item.get('data-id').toInt();
            var cover = item.get('cover').toInt();
            if (cover == 1) {
                this.options.photo_ids.push([id, i, 1]);
                $('preview_' + id).set('cover', '1');
                $('cover_album_button_' + id).addClass('active')
                continue;
            } else {
                this.options.photo_ids.push([id, i, 0]);
                $('cover_album_button_' + id).removeClass('active');
                $('preview_' + id).set('cover', '0');
                continue;
            }
        }


    },
    setCoverPhoto: function (element, id_photo) {

        var items = $$('.order_photos');
        var self = this;
        if (!items) {
            return;
        }

        var len = items.length;
        if (!len) {
            return;
        }

        self.options.photo_ids = [];
        for (var i = 0; i < len; i++) {
            var item = items[i];
            var id = item.get('data-id').toInt();
            var cover = item.get('cover').toInt();
            if (id_photo.toInt() == id) {
                self.options.photo_ids.push([id, i, 1]);
                $('preview_' + id).set('cover', '1');
                $('cover_album_button_' + id).addClass('active')
                continue;
            } else {
                self.options.photo_ids.push([id, i, 0]);
                $('cover_album_button_' + id).removeClass('active');
                $('preview_' + id).set('cover', '0');
                continue;
            }
        }


    },

    delete_photo_album: function (id) {
        var self = this;
        if (window.load_image_deletes == 1) {
            return;
        }
        $('load_delete_' + id).setStyle('display', 'block');
        window.load_image_deletes = 1;
        var req = new Request({
            method: 'get',
            url: en4.core.baseUrl + 'wall/index/album',
            data: {
                'do': '1',
                'photos_id_del': id
            },
            onComplete: function (response) {
                $('preview_' + id).remove();

                var index = self.getIndexOfId(self.options.photo_ids, id.toInt());
                var index2 = window.photo_ids_del.indexOf(id.toInt());
                var index3 = self.photo_tempIds.indexOf(id.toInt());
                if (index > -1) {
                    self.options.photo_ids.splice(index, 1);
                    self.order_photos();
                }
                if (index2 > -1) {
                    window.photo_ids_del.splice(index2, 1);
                }
                if (index3 > -1) {
                    self.photo_tempIds.splice(index3, 1);
                }
                localStorage.setItem("photo_ids", window.photo_ids_del);
                window.load_image_deletes = 0;

                self.makeFormInputs(self.photo_tempIds);
            }
        }).send();
    },
    getIndexOfId: function (items, id) {
        for (var i = 0; i < items.length; i++) {
            if (items[i][0] == id.toInt()) {
                return i;
            }
        }
    },
    deactivate: function () {
        if (!this.active) {
            dellPhotos();
            return;
        }
        this.parent();
    },

    doRequest: function () {
        this.elements.iframe = new IFrame({
            'name': 'composePhotoFrame',
            'src': 'javascript:false;',
            'styles': {
                'display': 'none'
            },
            'events': {
                'load': function () {
                    this.doProcessResponse(window._composePhotoResponse);
                    window._composePhotoResponse = false;
                }.bind(this)
            }
        }).inject(this.elements.body);

        window._composePhotoResponse = false;
        this.elements.form.set('target', 'composePhotoFrame');

        // Submit and then destroy form
        this.elements.form.submit();
        this.elements.form.destroy();

        // Start loading screen
        this.makeLoading();
    },

    doProcessResponse: function (responseJSON) {
        var self = this;

        // An error occurred
        if (($type(responseJSON) != 'hash' && $type(responseJSON) != 'object') || $type(responseJSON.src) != 'string' || $type(parseInt(responseJSON.photo_id)) != 'number') {
            //this.elements.body.empty();
            this.makeError(this._lang('Unable to upload photo. Please click cancel and try again'), 'empty');
            return;
            //throw "unable to upload image";
        }

        // Success

        this.photo_tempIds.push(responseJSON.photo_id);
        this.params.set('rawParams', responseJSON);
        this.params.set('photo_id', this.photo_tempIds);
        if (!window.photo_ids_del) {
            window.photo_ids_del = [];
        }
        window.photo_ids_del.push(responseJSON.photo_id);
        localStorage.setItem("photo_ids", window.photo_ids_del);

        this.elements.preview_container = new Element('div', {
            'id': 'preview_' + responseJSON.photo_id,
            'class': 'wall-compose-photo-preview-container'
        });

        this.elements.preview = Asset.image(responseJSON.src, {
            'class': 'wall-compose-preview-image wall-compose-photo-preview-image',
            'onload': this.doImageLoaded.bind(this)
        }).inject(this.elements.preview_container);

        this.elements.delete_photo_button = new Element('i', {
            'class': 'hei hei-times wall-compose-photo-preview-delete'
        });

        this.elements.delete_photo_button.addEvent('click', function () {
            self.delete_photo_album(responseJSON.photo_id);
        });

        var delete_loading = new Element('div', {
            'id': 'load_delete_' + responseJSON.photo_id,
            'class': 'load_delete_album_wall',
            'style': 'display:none;'
        }).inject(this.elements.preview_container);

        this.elements.delete_photo_button.inject(this.elements.preview_container);
    },

    doImageLoaded: function () {
        this.elements.preview.erase('width');
        this.elements.preview.erase('height');
        this.elements.preview_container.inject(this.elements.body);
        this.makeFormInputs(this.params.photo_id);
    },

    makeFormInputs: function (photo_id) {
        var self = this;
        this.ready();
        var data = {
            'photo_id': photo_id,
            'type': 'photo'
        };
        $H(data).each(function (value, key) {
            self.setFormInputValue(key, value);
        });
    }

});

function hide_form_wall_album() {
    window.onbeforeunload = 'NULL';
    window.onunload = 'NULL';
    window.submitCheck = 0;
    localStorage.setItem("photo_ids", '');
    var req = new Request({
        method: 'get',
        url: en4.core.baseUrl + 'wall/index/album',
        data: {
            'do': '1',
            'photos_id_del': window.photo_ids_del
        },
        onComplete: function (response) {
            window.photo_ids_del = '';
        }
    }).send();
    var body = $$('.photo_download_contaner');
    body.setStyle('display', 'none');
    body.set('html', '');
    $$('.background_wall_photo_download').setStyle('display', 'none');
}

function show_loading_album_download() {
    var loader = en4.core.baseUrl + 'application/modules/Wall/externals/images/24.gif';
    var body = $$('.photo_download_contaner')[0];
    var form = new Element('div', {
        'id': 'load_album',
        'style': 'bottom: 0; height: 100%;left: 0; position: absolute; right: 0; top: 0; width: 100%;background-color: rgba(0, 0, 0, 0.2);background-repeat: no-repeat; background-image: url(' + loader + '); background-position: center center;'
    }).inject(body);
}

function hide_loading_album_download() {
    if ($('load_album')) $('load_album').destroy();
}

window.addEvent('domready', function () {
    dellPhotos();
});

function dellPhotos() {
    var $idis = localStorage.getItem("photo_ids");
    if (!$idis) {
        return;
    }
    if ($idis.length > 0) {
        var req = new Request({
            method: 'get',
            url: en4.core.baseUrl + 'wall/index/album',
            data: {
                'do': '1',
                'photos_id_del': $idis
            },
            onComplete: function (response) {
                window.photo_ids_del = '';
                localStorage.setItem("photo_ids", '');
            }
        }).send();
    }
}