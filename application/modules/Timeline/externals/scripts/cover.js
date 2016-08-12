/**
 * Copyright Hire-Experts LLC
 *
 * User: mt.uulu
 * Date: 1/27/12
 * Time: 12:49 PM
 */

var TimelineCover = new Class({
    Implements: [Options],
    options: {
        element_id: null,
        save_button: null,
        edit_buttons: null,
        loader_id: null,
        cover_width: 850,
        cover_url: '',
        position_url: '',
        is_allowed: false,
        imgSrc: null
    },

    block: null,
    buttons: null,
    element: null,
    editButton: null,
    saveButton: null,

    imgSize: null,

    clearStatus: function() {
        var div = $('subject-additional-info');
        if(div) {
            en4.user.clearStatus();
            div.destroy();
        }
    },

    initialize: function (options) {
        var self = this;
        self.setOptions(options);
    },

    setInitPosition: function () {
        var self = this;
        self.element.setStyle('top', self.position.top + "px");
        self.element.setStyle('left', "0");
    },

    updateImageSize: function (flag, src) {
        var self = this;
        var img = new Image();
        if (flag) {
            img.src = src;
        } else {
            img.src = self.options.imgSrc;
        }
        img.onload = function (e) {
            self.imgSize = {
                width: this.width,
                height: this.height
            };
            if (flag) {
                setTimeout(function () {
                    self.reposition.start();
                }, 1000);
            }
        };
    },
    init: function (options) {
        var self = this;
        self.updateImageSize(false);
        if ($(self.options.element_id) != null) {
            self.element = $(self.options.element_id);
        }
        self.editButton = $(self.options.edit_buttons);
        self.saveButton = $(self.options.save_button);
        self.element = $(self.options.element_id);
        self.setInitPosition();
        self.initEvents();
    },

    initEvents: function () {
        var self = this;
        if (!self.isAllowed()) return;
        self.saveButton.addEvent('click', function () {
            self.reposition.stop();
        });
    },

    isAllowed: function () {
        return this.options.is_allowed;
    },

    injectImg:function() {
        var self = this;
        var a = $('cover-container');
        var img = new Element('img', {id: 'cover-container-img'});
        img.inject(a);
        self.element = $('cover-container-img');
        self.element_id = 'cover-container-img';
    },

    load_photo: function (id, type) {
        var self = this;

        new Request.JSON({
            method: 'get',
            url: self.options.cover_url,
            data: {format: 'json', type: type},
            onComplete: function (response) {
                self.options.imgStr = response.coverPhoto.photoSrc;

                if (!self.element) {
                    self.injectImg();
                }
                self.element.setAttribute('src', response.coverPhoto.photoSrc);

                if(window.PhotoViewer) {
                    window.PhotoViewer.bindPhotoViewerCover('cover-container');
                    self.element.getParent().setAttribute('href', response.coverPhoto.photoHref);
                } else {
                    self.element.getParent().setAttribute('href', 'javascript://');
                }

                self.position = JSON.parse(response.position);
                self.setInitPosition();
                self.updateImageSize(true, response.coverPhoto.photoSrc);
            }
        }).send();
    },

    showReposition: function () {
        var self = this;
        self.element.setStyle('cursor', 'move');
        self.editButton.setStyle('display', 'none');
        self.saveButton.setStyle('display', 'block');
    },
    hideReposition: function () {
        var self = this;
        self.element.setStyle('cursor', '');
        self.editButton.setStyle('display', 'inline-block');
        self.saveButton.setStyle('display', 'none');
    },

    position: {
        top: 0,
        left: 0
    },

    reposition: {
        drag: null,
        active: false,
        start: function () {
            if (this.active) {
                return;
            }
            if (!document.tl_cover.element) {
                document.tl_cover.injectImg();
            }

            var self = document.tl_cover;
            var cover = document.tl_cover.element;
            this.active = true;
            self.showReposition();
            cover.addClass('draggable');

            var cont = cover.getParent();
            var img = document.tl_cover.imgSize;
            var size = self.element.getSize();

            var verticalLimit = size.y - cont.getSize().y;

            var limit = {x: [0, 0], y: [0, 0]};

            if (verticalLimit > 0) {
                limit.y = [-verticalLimit, 0];
            }


            this.drag = new Drag(cover, {
                limit: limit,
                onComplete: function (el) {
                    self.position.top = el.getStyle('top').toInt();
                    self.position.left = 0;
                }
            }).detach();

            this.drag.attach();
        },

        stop: function () {
            if (!this.active) {
                return;
            }
            var self = document.tl_cover;
            var cover = document.tl_cover.element;
            self.hideReposition();
            new Request.JSON({
                method: 'get',
                url: self.options.position_url,
                data: {'format': 'json', 'position': self.position},
                onRequest: function () {

                },
                onSuccess: function (response) {
                    self.reposition.drag.detach();
                    cover.removeClass('draggable');

                    self.reposition.drag = null;
                    self.reposition.active = false;
                }
            }).send();
        }
    },

    slideShow: function (url, guid, element) {
        var self = this;

        if (self.reposition.active) {
            return;
        }

        new Wall.Slideshow(url, guid, element);
    }
});
