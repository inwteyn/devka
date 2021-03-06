/*
--- 
authors: 
- 3n
provides: [MTSwipeEvent]
requires: 
  - MTCore
  - Core/Element.Event
license: MIT-style
description: Adds element.addEvent('swipe', fn). fn is passed information about the swipe location and direction.
...
*/

Element.Events.swipe = {
  allSwipesCanceled : false,
  
  cancelAllSwipes : function(){
    Element.Events.swipe.allSwipesCanceled = true;
  },
  
  onAdd: function(fn){
    var startX, startY, active = false;

    var touchStart = function(event){
      active = true;
      Element.Events.swipe.allSwipesCanceled = false;
      var originalEvent = MT.getEvent(event.event);
      startX = originalEvent.pageX;
      startY = originalEvent.pageY;
    };
    var touchMove = function(event){
      var originalEvent = MT.getEvent(event.event);      
      var endX  = originalEvent.pageX,
          endY  = originalEvent.pageY,          
          diff  = endX - startX,
          isLeftSwipe = diff < -1 * Element.Events.swipe.swipeWidth,
          isRightSwipe = diff > Element.Events.swipe.swipeWidth;

      if (active && !Element.Events.swipe.allSwipesCanceled && (isRightSwipe || isLeftSwipe)          
          && (event.onlySwipeLeft ? isLeftSwipe : true)
          && (event.onlySwipeRight ? isRightSwipe : true) ){
        active = false;
        fn.call(this, {
          'direction' : isRightSwipe ? 'right' : 'left', 
          'startX'    : startX,
          'endX'      : endX,
          'startY'    : startY,
          'endY'      : endY
        }, event);
      }
      
      if (Element.Events.swipe.cancelVertical
          && Math.abs(startY - endY) < Math.abs(startX - endX)){
        return false;
      }
    }
    var touchEnd = function(event){
      active = false;
    }
    this.addEvent(MT.startEvent, touchStart);
    this.addEvent(MT.moveEvent, touchMove);
    this.addEvent(MT.endEvent, touchEnd);

    var swipeAddedEvents = {};
    swipeAddedEvents[fn] = {};
    swipeAddedEvents[fn][MT.startEvent] = touchStart;
    swipeAddedEvents[fn][MT.moveEvent]  = touchMove;
    
    this.store('swipeAddedEvents', swipeAddedEvents);
  },
  
  onRemove: function(fn){
    $H(this.retrieve('swipeAddedEvents')[fn]).each(function(v,k){
      this.removeEvent(k,v);
    }, this);
  }
};

Element.Events.swipe.swipeWidth = 70;
Element.Events.swipe.cancelVertical = true;
