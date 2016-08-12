/**
 * Created by asmproger on 13.11.14.
 */
var SomeSlider = new Class({
  Implements: [Options],

  //options
  options: {
    container: null,
    content: null,
    items: null,
    leftBtn: null,
    rightBtn: null,
    itemsCount: 0
  },

  contentLength: 0,
  itemsLength: 0,
  dx: 200,
  dt: 200,

  showControls: false,

  animation:null,

  initialize: function (options) {
    var self = this;
    this.setOptions(options);

    this.options.leftBtn.removeEvents('click').addEvent('click', function () {
      self.stepLeft();
    });
    this.options.rightBtn.removeEvents('click').addEvent('click', function () {
      self.stepRight();
    });

  },

  onAir: false,

  initValues: function () {
    var self = this;
    if (window.quickSliderPreviews == this.options.itemsCount) {
      self.contentLength = self.options.content.offsetWidth;
      self.itemsLength = self.options.items.offsetWidth;
      self.showControls = (self.contentLength < this.itemsLength);

      self.update();
      window.quickSliderPreviews = 0;

      this.animation = new Fx.Morph(this.options.items, {
        duration: self.dt,
        transition: Fx.Transitions.Sine.easeOut,
        onComplete:function(el) {
          self.update();
        }
      });
    }
  },

  update: function () {
    if (!this.showControls) {
      this.hideLeft();
      this.hideRight();
    } else {

      if (this.canRight()) {
        this.showRight();
      } else {
        this.hideRight();
      }

      if (this.canLeft()) {
        this.showLeft();
      } else {
        this.hideLeft();
      }
    }
    this.onAir = false;
  },

  stepLeft: function () {
    var self = this;
    if (!this.canLeft() || this.onAir) {
      return;
    }

    this.onAir = true;

    var left = Math.abs(this.getPosition(this.options.items, 'left'));
    left -= this.dx;
    this.animation.start({
      left:[self.currentPosition(), -left]
    });
    //setTimeout(function(){self.update();}, 100);
    self.update();
  },
  stepRight: function () {
    var self = this;
    if (!this.canRight() || this.onAir) {
      return;
    }

    this.onAir = true;

    var left = Math.abs(this.getPosition(this.options.items, 'left'));
    left += this.dx;

    this.animation.start({
      left:[self.currentPosition(), -left]
    });
    //setTimeout(function(){self.update();}, 100);
    //self.update();
  },

  currentPosition: function () {
    return this.getPosition(this.options.items, 'left');
  },

  getPosition: function (element, side) {
    var value = element.getStyle(side);
    value = value.substr(0, value.length - 2);
    var r = new Number(value);
    if (isNaN(r)) {
      r = 0;
    }
    return r;
  },

  canLeft: function () {
    return (this.currentPosition() != 0 );
  },
  canRight: function () {
    return this.getD() > this.contentLength;
  },
  getD: function () {
    return (this.itemsLength + this.currentPosition());
  },


  hideLeft: function () {
    this.options.leftBtn.setStyle('visibility', 'hidden');
  },
  hideRight: function () {
    this.options.rightBtn.setStyle('visibility', 'hidden');
  },
  showLeft: function () {
    this.options.leftBtn.setStyle('visibility', 'visible');
  },
  showRight: function () {
    this.options.rightBtn.setStyle('visibility', 'visible');
  }

});
