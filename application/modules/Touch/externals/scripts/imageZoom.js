/**
 * Created by Hire-Expert LLC.
 * Author: Ulan
 * Date: 23.01.12
 * Time: 14:52
 */
TouchImageZoom = new  Class({
  scroll: null,
  photos_a: null,
  active_photo_a: null,
  initialize: function(container, options){
    const self = this;
    // Scroll
    this.scroll = new Fx.Scroll(document.body, {
        wait: false,
        duration: 300,
        offset: {'x': 0, 'y': 0},
        transition: Fx.Transitions.Quad.easeInOut
    });

    if(!$type(container) ||$type(container) != 'element'){
      console.log('Wrong parameter has set, the parameter must be an element;');
      throw Error('Wrong parameter has set, the parameter must be an element;');
    }
    this.photos_a = container.getElements('a');
    this.photos_a.addClass('touch_zoom_image_a_inactive')
    this.photos_a.addEvent('click', function(e){
      var img = this.getElement('img');
      var src = img.src;
      img.src = this.href;
      this.href = src;
      if(!this.hasClass('touch_zoom_image_a_active') && $type(self.active_photo_a)){
        var aimg = self.active_photo_a.getElement('img');
        var asrc = aimg.src;
        aimg.src = self.active_photo_a.href;
        self.active_photo_a.href = asrc;
        self.active_photo_a.removeClass('touch_zoom_image_a_active');
      }
      if(this.hasClass('touch_zoom_image_a_active')){
        self.active_photo_a = null;
        this.removeClass('touch_zoom_image_a_active');
      }
      else
      {
        self.active_photo_a = this;
        this.addClass('touch_zoom_image_a_active');
      }
      setTimeout(function(){self.scroll.toElement(img);}, 300);
    });
    this.photos_a.addEvent('swipe', function(e){
      console.log(e);
      var np;
      if(e.direction == 'left'){
        np = this.getNext();
      } else {
        np = this.getPrevious();
      }
      if($type(np)){
        np.fireEvent('click');
      }
    });
    }
})