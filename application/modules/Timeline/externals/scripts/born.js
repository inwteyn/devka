/**
 * Copyright Hire-Experts LLC
 *
 * User: mt.uulu
 * Date: 1/27/12
 * Time: 12:49 PM
 */

var TimelineBorn = new Class({
    Implements: [Options],
    options: {
        element_id: null,
        edit_buttons: null,
        loader_id: null,
        born_width: 850,
        born_url: '',
        position_url: '',
        is_allowed: false
    },

    block: null,
    buttons: null,
    element: null,
    editButton: null,
    saveButton: null,

    imgSize: null,

    initialize: function (options) {
        this.setOptions(options);
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
            this.toggleClass('active');
            self.reposition.stop();
        });
    },

    isAllowed: function () {
        return this.options.is_allowed;
    },

  load_photo: function (id) {
    var self = this;

    new Request.JSON({
      method: 'get',
      url: self.options.born_url,
      data: {'format': 'json'},
      onComplete: function (response) {
        self.options.imgStr = response.coverPhoto;
        self.element.setAttribute('src', response.coverPhoto.photoSrc);
        self.element.getParent().setAttribute('href', response.albumPhoto);

        if(window.PhotoViewer) {
          window.PhotoViewer.bindPhotoViewerCover('born-container');
          self.element.getParent().setAttribute('href', response.albumPhoto);
        } else {
          self.element.getParent().setAttribute('href', 'javascript://');
        }

        self.position = JSON.parse(response.position);
        self.setInitPosition();
        self.updateImageSize(true, response.coverPhoto);
      }
    }).send();
  },

    position: {
        top: 0,
        left: 0
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
        self.editButton.setStyle('display', 'block');
        self.saveButton.setStyle('display', 'none');
    },

    reposition: {
        drag: null,
        active: false,
        start: function () {
            if (this.active) {
                return;
            }

            var self = document.tl_born;
            var born = document.tl_born.element;
            this.active = true;
            self.showReposition();
            born.addClass('draggable');
            var cont = born.getParent();
            var img = document.tl_born.imgSize;
            var size = self.element.getSize();

            var verticalLimit = size.y - cont.getSize().y;

            var limit = {x: [0, 0], y: [0, 0]};

            if (verticalLimit > 0) {
                limit.y = [-verticalLimit, 0];
            }

            this.drag = new Drag(born, {
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

            var self = document.tl_born;
            var born = document.tl_born.element;
            self.hideReposition();

            new Request.JSON({
                method: 'get',
                url: self.options.position_url,
                data: {'format': 'json', 'position': self.position},
                onRequest: function () {

                },
                onSuccess: function (response) {
                    self.reposition.drag.detach();
                    born.removeClass('draggable');

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
