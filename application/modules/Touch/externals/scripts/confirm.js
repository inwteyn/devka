/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 30.03.11
 * Time: 15:47
 * To change this template use File | Settings | File Templates.
 */
//Confirm dialog
var TouchconfirmClass =new Class({
	bind: function(block){
		var self = Touchconfirm;

		var elements;

		block = Touch.getBlock(block);

    elements = block.getElements("a.touchconfirm");

		elements.each(function(el)
		{
			if( el.get('tag') != 'a' || el.retrieve('touchconfirmed', false))
			{
				return;
			}

			el.store('touchconfirmed', true);

			el.addEvent('click', function(event)
			{
				event.stop();
				if (confirm(el.get('text').trim())){
        	self.request(el, (el.hasClass('redirect'))?1:0);
				}
			});

		});
	},

	request: function(el, redirect){
		new Request.HTML({
			url: el.href,
			method: 'post',
			data: {'format': 'html', 'redirect':redirect},
			evalScripts : true,

			onRequest:function(){
			},
			
			onFailure: function(){
				Touch.message('An error has occurred!!!', 'error', '1000');
			},

			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
			},

			onComplete : function(responseTree, responseElements, responseHTML, responseJavaScript){
				en4.core.runonce.trigger();
			}
		}).send();
	}
});
