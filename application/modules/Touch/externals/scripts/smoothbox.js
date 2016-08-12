var SmoothboxClass =new Class({
	bind:function(block){
		var self = this;

		if ($type(block) != 'string' && $type(block) != 'element'){
			//Elements;
			self.box = $('global_smooth_box');
			self.loading = $('smooth-loading');
			self.body = $('smooth-body');
		}
		
		block = Touch.getBlock(block);

		var elements = block.getElements('a.smoothbox');

		elements.each(function(el){

			if( el.get('tag') != 'a' || el.retrieve('smoothed', false))
			{
				return;
			}

			el.store('smoothed', true);

			el.addEvent('click', function(event)
			{
				event.stop(); // Maybe move this to after next line when done debugging
				self.open(el);
			});
		});
	},

	openInline:function(el){
		var self = this;
		self.load();
		var el = ($type(el) == 'element')? el : $(el);
		if ($type(el) == 'element'){
			self.body.set('html', el.get('html'));
			self.success();
		} else {
			self.close();
		}
	},

	open:function(el){
		var self = this;
		self.load();

		new Request.HTML({
			'url':el.href,
			'method':'get',
			'data':{'format':'html'},
			evalScripts : true,
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        	self.body.set('html', responseHTML);
					self.success(1);
			},
			onFailure: function(responseTree, responseElements, responseHTML, responseJavaScript){
        self.body.set('html', responseHTML);
				setTimeout(function(){self.close()}, '500');
			}
		}).send();
	},

	load:function(){
		var self = this;

		if (!self.box.retrieve('opened', false)){
			self.body.setStyle('display', 'none');
			self.loading.setStyle('display', 'block');

			self.box.setStyle('display', 'block');
			$$('#global_header, #global_wrapper, #global_footer').setStyle('display', 'none');

			self.box.store('opened', true);
		}
	},

	success: function(){
		var self = this;

		self.loading.setStyle('display', 'none');
		self.body.setStyle('display', 'block');

		setTimeout(function(){self.resize()}, '50');
		Touch.bind(self.body.getProperty('id'));
	},

	close:function(){
		var self = this;

		if ($type(self.box) == 'element')
		{
			self.box.setStyle('display', 'none');
			self.box.store('opened', false);
			$$('#global_header, #global_wrapper, #global_footer').setStyle('display', 'block');
		}
	},

	'resize': function(){
		var self = this;

		if (self.box && !self.box.retrieve('opened', false)) return;

		if ($type(self.body) != 'element') return;

		var windowH = window.getSize().y;
		var bodyH = self.body.getSize().y;

		var top = 0;
		if (windowH > bodyH)
		{
			top = ((windowH - bodyH)/2);
		}



		self.body.setStyle('top', top);
	}
});
