
var PhotoboxClass = new Class({
	isOpen: true,

	globalPhotobox: 'global_photo_box',
	loading: 'photo_box_loading',
	navigation: 'photo_box_navigators',
	photobox: 'photo_box_photo',
	photoboxPrev: 'photo_box_prev',
	photoboxNext: 'photo_box_next',
	photoboxMiddle: 'photo_box_middle',
	photoboxClose: 'photo_box_close',
	photoHeader: 'photo_box_header',

	navItems: null,
	photo: null,

	nextBt:null,
	prevBt:null,
	media:null,
  subnav:false,

	setOptions:function(options){
		var self = this;

		if ($type(options.next_button) == 'element'){
			self.nextBt = options.next_button;
		} else
		if ($type(options.next_button) == 'string'){
			self.nextBt = $(options.next_button);
		}

		if ($type(options.prev_button) == 'element'){
			self.prevBt = options.prev_button;
		} else
		if ($type(options.prev_button) == 'string'){
			self.prevBt = $(options.prev_button);
		}

		if ($type(options.media_photo) == 'element'){
			self.media = options.media_photo;
		} else
		if ($type(options.prev_button) == 'string'){
			self.media = $(options.media_photo);
		}

    if (options.sub_nav != null){
      self.subnav = true;
    }


		self.photobox = $(self.photobox);
		self.globalPhotobox = $(self.globalPhotobox);
		self.loading = $(self.loading);
		self.navigation = $(self.navigation);
		self.photoboxPrev = $(self.photoboxPrev);
		self.photoboxNext = $(self.photoboxNext);
		self.photoboxMiddle = $(self.photoboxMiddle);
		self.photoboxClose = $(self.photoboxClose);
		self.photoHeader = $(self.photoHeader);

		self.buttons = [self.photoboxPrev, self.photoboxMiddle, self.photoboxNext, self.photoboxClose];

		self.photoboxPrev.removeEvents();
		self.photoboxPrev.addEvent('click', function(){
			self.prev();
		});

		self.photoboxNext.removeEvents();
		self.photoboxNext.addEvent('click', function(){
			self.next();
		});

		self.photoboxMiddle.removeEvents();
		self.photoboxMiddle.addEvent('click', function(){
			self.showOptions();
		});

		self.photoboxClose.removeEvents();
		self.photoboxClose.addEvent('click', function(){
			self.close();
		});

		self.media.removeEvents();
		self.media.addEvent('click', function(){
			self.show(self.media.getElement('img'))
		});
	},

	show:function(photo){
		var self = this;
		var t = Touch;

		if ($type(photo) == 'string'){
			photo = $(photo);
		}

		if ($type(photo) != 'element'){
			return;
		}

		self.globalPhotobox.setStyle('display', 'block');
    $$('#global_header, #global_wrapper, #global_footer').setStyle('display', 'none');
    document.body.style.backgroundColor = '#000';
		var new_photo = photo.clone();
		self.injectPhoto(new_photo);
		self.isOpen = true;
    document.head.getElementsByName('viewport')[0].set('content', 'width=device-width; initial-scale=1.0; maximum-scale=5.0; user-scalable=1;');
	},

	injectPhoto:function(photo){
		var self = this;

		photo.set('id', 'new-photo-box-photo');
		photo.setStyles({'visibility':'hidden', 'opacity':'0'});

		self.loading.setStyle('display', 'none');
		if ($type(self.nextBt) == 'element' || $type(self.prevBt) == 'element'){
			self.navigation.setStyle('display', 'block');
		}

		self.navigation.setStyle('top', self.photoHeader.getSize().y);

		if ($type($('new-photo-box-photo')) == 'element'){
			photo.replaces($('new-photo-box-photo'));
		} else {
			photo.inject(self.photobox);
			self.showOptions();
		}

		self.photo = photo;

		setTimeout(function(){
			self.resize();
			photo.fade('in');
		}, '100');
	},
    
	prev:function(){
		var self = this;
    if($type(self.prevBt) != 'element')
      return;
		var t = Touch;

		self.loading.setStyle('display', 'inline-block');
		$('new-photo-box-photo').setStyle('opacity', '0.6');
    if(self.subnav)
      t.navigation.subNavRequest(self.prevBt);
    else{
		t.navigation.request(self.prevBt);
    }
	},

	next:function(){
		var self = this;
    if($type(self.nextBt) != 'element')
      return;
		var t = Touch;

		self.loading.setStyle('display', 'inline-block');
		$('new-photo-box-photo').setStyle('opacity', '0.6');
    if(self.subnav)
      t.navigation.subNavRequest(self.nextBt);
    else{
      t.navigation.request(self.nextBt);
    }
	},

	close:function(){
		var self = this;
		var t = Touch;

		self.photo = null;
		self.isOpen = false;
		t.globalbind.setStyles({'height':'100%', 'overflow':'auto'});
		self.globalPhotobox.setStyle('display', 'none');
    $$('#global_header, #global_wrapper, #global_footer').setStyle('display', 'block');
    document.body.style.backgroundColor = '';
    document.head.getElementsByTagName('meta')[0].set('content', 'width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=1;');
	},

	showOptions:function(){
		var self = this;

		self.buttons.each(function(b){
			b.store('showed', true);
			b.fade('in');
		});

		setTimeout(function(){
			Photobox.buttons.each(function(b){
				if ( b.retrieve('showed', false) ){
					setTimeout(function(){b.setStyle('visibility', 'visible')}, '600');
				}
			});
		}, '2000');
	},

	'resize': function(){
		var self = this;

		if (!this.isOpen) return;

		if ($type(self.photobox) != 'element') return;

		var windowH = window.getSize().y;
		var boxH = self.photobox.getSize().y;

		var navH = windowH;

		var top = 0;
		if (windowH > boxH)
		{
			top = ((windowH - boxH)/2);
		} else {
			navH = boxH;
		}


		navH = navH - self.photoHeader.getSize().y;
		self.photoboxPrev.setStyle('height', navH);
		self.photoboxNext.setStyle('height', navH);
		self.photoboxMiddle.setStyles({'height':navH, 'width':(window.getSize().x - 110)});

		self.photobox.setStyle('top', top);
	}
});