Element.implement({
  getTranslate3d: function(){
    var match = this.getStyle('-webkit-transform').match(/translate3d\(([\.\-0-9]+)[^\.\-0-9]+([\.\-0-9]+)[^\.\-0-9]+([\.\-0-9]+)[^\.\-0-9]+/);

    if (match && match.shift() && match.length === 3)
      return new MTPoint(match[0].toInt(), match[1].toInt(), match[2].toInt());
  },

  setTranslate3d: function(x,y,z){
    var oldPoint = this.getTranslate3d() || new MTPoint(0,0,0);

    var x = $chk(x) ? x : oldPoint.x,
        y = $chk(y) ? y : oldPoint.y,
        z = $chk(z) ? z : oldPoint.z;

    var newPoint = new MTPoint(x,y,z);

    if (newPoint.equals(oldPoint))
      return;

    var previousTransform = (this.getStyle('webkitTransform') || '').replace(/translate3d\([^)]+\)/,'');
    this.setStyle('webkitTransform', previousTransform + " translate3d(" + x + "px," + y + "px," + z + "px)");

    return this;
  },
  setTranslate: function(x,y, useZero){
    return this.setTranslate3d(x, y, useZero ? 0 : null);
  },
  setTranslateX: function(x, useZero){
    return this.setTranslate3d(x, useZero ? 0 : null, useZero ? 0 : null);
  },
  setTranslateY: function(y, useZero){
    return this.setTranslate3d(useZero ? 0 : null, y, useZero ? 0 : null);
  },
  setTranslateZ: function(z, useZero){
    return this.setTranslate3d(useZero ? 0 : null, useZero ? 0 : null, z);
  }
});

/*
--- 
authors: 
- 3n
provides: [MTCore]
requires:
  - Core/Core
  - Core/Element.Event
license: MIT-style
description: The core of MooTouch.
...
*/

// for use with Element.addEvent
Element.NativeEvents.touchstart          = 2;
Element.NativeEvents.touchmove           = 2;
Element.NativeEvents.touchend            = 2;
Element.NativeEvents.webkitTransitionEnd = 2;
Element.NativeEvents.orientationchange   = 2;

// MooTouch master object
var MT = {
  supportsTouches : 'createTouch' in document
}
MT.startEvent = MT.supportsTouches ? 'touchstart' : 'mousedown';
MT.moveEvent  = MT.supportsTouches ? 'touchmove'  : 'mousemove';
MT.endEvent   = MT.supportsTouches ? 'touchend'   : 'mouseup';

MT.getEvent = function(event){
  return (event.touches && event.touches.length > 0) ? event.touches[0] : event;
};
