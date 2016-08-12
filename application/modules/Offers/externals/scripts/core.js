var offers_manager = {
  page_num: 1,
  filter: 'free',
  url: '',
  changeStatusCouponUrl: '',
  offersBrowseUrl: '',
  map: {
    lat: 0,
    lng: 0
  },

  getOffers: function() {

      var self = this;
      var data = {
          filter: self.filter,
          my_offers_filter: self.my_offers_filter,
          page_num: self.page_num
      };

      self.request(self.url, data);
  },

  setPage: function(page, max){
    if(window.load_offers_more == 1){
      return;
    }
    window.load_offers_more = 1;
     var self = this;
    var page_id = page;
    if(page == max){
      window.load_more_status = 1;
    }
    if(page < max){
      page_id++;
      $$('.paginationControl a')[0].set('onclick','offers_manager.setPage('+page_id+', '+max+'); return false;');
    }else{
      $$('.paginationControl a')[0].setStyle('display','none');
      $$('.paginationControl.offers_pagination')[0].setStyle('display','none');
      window.load_more_status = 1;
    }
     self.page_num = page;
     self.getOffers()
  },

  formSearch: function(filter) {
      var self = this;
      $('offers_filter_form').removeEvents().addEvent('submit', function(event){
          event.stop();

          var url = window.location.pathname;

          var data = {
              filter: filter,
              searchText: $('search_title_offer').get('value'),
              category_id: $('category_id').get('value')
          }

          self.request(url, data);
      });

  },

  request: function(url, data) {
    var request = new Request.HTML({
      url: url,
      data: data,
      format: 'html',
      onRequest: function() {
        $('offer_navigation_loader').removeClass('hidden');
        $('offer_navigation_loader_new').removeClass('hidden');
        $('offer_viewmoreButton').addClass('hidden');
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        var el = $$('.layout_offers_browse_offers');
        var tElement = new Element('div', {'html': responseHTML});
        tElement.getElements('.HeCubeView li').inject($$('.offers.HeCubeView')[0]);
       /* el[0].innerHTML = tElement.getElement('.layout_offers_browse_offers').innerHTML;*/
        $('offer_navigation_loader').addClass('hidden');
        $('offer_navigation_loader_new').addClass('hidden');
        $('offer_viewmoreButton').removeClass('hidden');

        if (data.my_offers_filter) {
          if (data.my_offers_filter == 'past') {
            $$('.past_button').addClass('active');
            $$('.upcoming_button').removeClass('active');
          }
          else {
            $$('.past_button').removeClass('active');
            $$('.upcoming_button').addClass('active');
          }
        }

        $$('ul.offer-categories li a').setStyle('font-weight', 'normal');
        if (data.category_id == 0) {
          if ($('offer_all_categories')) {
            $('offer_all_categories').setStyle('font-weight','bold');
          }
        } else {
          $$('.category_'+data.category_id).setStyle('font-weight','bold');
        }
        window.load_offers_more = 0;
      }
    }).post();
  },

  setCategory : function(category_id, filter, my_offers_filter) {
    var self = this;
    var data = {
      category_id: category_id,
      filter: filter,
      my_offers_filter: my_offers_filter
    };
    var url = self.url;
    this.request(url, data);
  },

  showGMap: function() {
    var self = this;
      var latlng = new google.maps.LatLng(self.map.lat, self.map.lng);
      var myOptions = {
        zoom: 14,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      var map = new google.maps.Map($('map_canvas'), myOptions);
      var marker = new google.maps.Marker(({
        position: latlng,
        map: map
      }));
  },

  changeStatusCoupon: function(offer_id, $element, user_id) {
    var self = this;
    var user_id = (user_id > 0) ? user_id : 0;
    var request = new Request.JSON(
      {
        secure:false,
        url: self.changeStatusCouponUrl,
        method:'post',
        data:{
          'format':'json',
          'offer_id':offer_id,
          'user_id':user_id
        },
        onSuccess:function (result) {
          if ($element) {
            if (self.my_offers_filter) {
              $element.getParent('.offer_item').destroy();
            } else {
              if (result.new_status == 'used') {
                $element.set('html', en4.core.language.translate('OFFERS_change_status_coupon', 'Active'));
              } else {
                $element.set('html', en4.core.language.translate('OFFERS_change_status_coupon', 'Used'));
              }
            }
          }
        }
      }).send();
  }
};
window.addEvent('domready', function(){
  if(!$$('.HeCubeView')[0] ||  !$$('.paginationControl.offers_pagination a')[0]){return;}
  window.onscroll = function (event) {
    var window_h = window.getSize().y;
    var contayner = $$('.HeCubeView')[0].getSize().y + $$('.HeCubeView')[0].getPosition().y;
    var hh = contayner - window_h;
    if(hh<event.pageY && window.load_more_status != 1){
      $$('.paginationControl.offers_pagination a')[0].click();
    }
  }
});
