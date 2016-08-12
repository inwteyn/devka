/*
 ---

 name: Zoomer
 description: Class to show zoomed image inside original
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: [Core/Class.Extras, Core/Element.Style, Core/Element.Dimensions, Core/Element.Event, Core/Fx.Tween]
 provides: Zoomer

 ...
 */

var Zoomer = new Class({

  version: '1.9.3',
  top: 'auto',
  width: 0,
  height: 0,
  Implements: [Options],

  options: {
    smooth: 6,
    wrapper: '',
    isQuick: true
  },

  initialize: function (element, options) {
    var self = this;
    this.setOptions(options);
    this.small = document.id(element);

    var w1 = $(this.options.wrapper).getWidth();
    var h1 = $(this.options.wrapper).getHeight();

    this.small.setStyle('visibility', 'hidden');
    this.small.addEvent('load', function () {
      var img = $(this.small);

      var w = img.width;
      var h = img.height;

      var r = w / h;

      var h3 = 400;
      var w3 = h3 * r;
      var top = 0;

      if (w3 > w1) {
        w3 = w1;
        h3 = w1 / r;
      }

      if (h > 400) {
        top = 0;
      } else {
        if (h3 < 400) {
          top = Math.abs(400 - h3);
        } else {
          top = Math.abs(h3 - h);
        }
      }

      if (h < 400) {
        self.width = w;
        self.height = h;
      } else {
        self.width = w3;
        self.height = h3;
      }

      if (this.options.isQuick) {
        top = Math.abs(400 - self.height);
      }

      if (top > 0) {
        self.top = new Number(top / 2);
      }

      this.prepareSmall(self.width, self.height);
    }.bind(this));

    var src = this.options.big || this.small.get('big');

    var styles = {
      position: 'absolute',
      top: 0,
      left: 0,
      opacity: 0,
      cursor: 'crosshair',
      visibility: 'hidden'
    };

    if (typeof src == 'string') {
      this.big = new Element('img', {
        src: src,
        styles: styles
      });
    } else {
      this.big = src;
      this.big.setStyles(styles);
    }

    this.big.setStyle('visibility', 'hidden');
    if (!this.big.complete) {
      this.big.addEvent('load', function () {
        this.prepareBig();
      }.bind(this));
    } else {
      this.prepareBig();
    }
  },

  prepareSmall: function (w, h) {
    var self = this;
    this.wrapper = new Element('div', {'class': 'zoomer-wrapper'}).wraps(this.small);
    ['left', 'bottom', 'right', 'float', 'clear', 'border', 'padding'].each(function (p) {
      var style = this.small.getStyle(p);
      var dflt = 'auto';
      if (['float', 'clear', 'border'].contains(p)) dflt = 'none';
      if (p == 'padding') dflt = '0';
      try {
        this.small.setStyle(p, dflt);
        this.wrapper.setStyle(p, style);
      } catch (e) {
      }
    }, this);
    this.wrapper.setStyles({
      visibility: 'visible',
      width: w,
      height: h,
      margin: '0 auto',
      position: 'relative',
      overflow: 'hidden',
      top: self.top
    });
    this.smallSize = {
      width: w,
      height: h
    };
    if (this.bigPrepared) {
      this.ready();
    } else {
      this.smallPrepared = true;
    }
  },

  prepareBig: function () {
    this.bigSize = {
      width: this.big.width,
      height: this.big.height
    };
    if (this.smallPrepared) {
      this.ready();
    } else {
      this.bigPrepared = true;
    }
  },

  ready: function () {
    this.big.inject(this.wrapper);
    this.bigWrapper = new Element('div', {
      'class': 'zoomer-wrapper-big',
      styles: {
        position: 'absolute',
        overflow: 'hidden',
        top: this.small.getPosition().y - this.wrapper.getPosition().y - this.wrapper.getStyle('border-top-width').toInt(),
        left: this.small.getPosition().x - this.wrapper.getPosition().x - this.wrapper.getStyle('border-left-width').toInt(),
        width: this.small.offsetWidth,
        height: this.small.offsetHeight,
        background: 'url("' + this.small.getAttribute('src') + '")',
        backgroundSize: this.width + 'px ' + this.height + 'px',
        zIndex: (this.small.getStyle('zIndex').toInt() || 0) + 1
      },
      events: {
        mouseenter: this.startZoom.bind(this),
        mouseleave: this.stopZoom.bind(this),
        mousemove: this.move.bind(this)
      }
    }).wraps(this.big);

    this.small.setStyle('visibility', 'visible');
    this.big.setStyle('visibility', 'visible');
  },

  move: function (event) {
    this.dstPos = event.page;
  },

  tmpSize: {},
  tmpImage: {},
  startZoom: function () {
    var self = this;
    this.position = this.small.getPosition();

    this.ratio = {
      x: 1 - this.bigSize.width / this.smallSize.width,
      y: 1 - this.bigSize.height / this.smallSize.height
    };

    this.current = {
      left: this.big.getStyle('left').toInt(),
      top: this.big.getStyle('top').toInt()
    };

    this.timer = this.zoom.periodical(10, this);
    this.big.fade('in');


    setTimeout(function () {
      self.tmpSize = self.bigWrapper.getStyle('background-size');
      self.tmpImage = self.bigWrapper.getStyle('background-image');
      self.bigWrapper.setStyle('background-image', '');
    }, 100);

  },

  stopZoom: function () {
    var self = this;
    $clear(this.timer);
    this.big.fade('out');
    setTimeout(function () {
      self.bigWrapper.setStyle('background-size', self.tmpSize);
      self.bigWrapper.setStyle('background-image', self.tmpImage);
    }, 100);
  },

  zoom: function () {
    if (!this.dstPos) return;

    var steps = this.options.smooth;
    var dst = {
      left: parseInt((this.dstPos.x - this.position.x) * this.ratio.x, 10),
      top: parseInt((this.dstPos.y - this.position.y) * this.ratio.y, 10)
    };
    this.current.left -= (this.current.left - dst.left) / steps;
    this.current.top -= (this.current.top - dst.top) / steps;

    this.big.setStyles(this.current);
  }

});


function zoomMe(src, big, id, wrapper, isQuick) {
  var $wrapper = $(wrapper);
  $wrapper.set('html', '');
  var $img = new Element('img', {
    src: src,
    big: big,
    id: id
  });

  $img.inject($wrapper);

  new Zoomer(id, {
    smooth: 10,
    wrapper: wrapper,
    isQuick: isQuick
  });
}