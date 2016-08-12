
if (!Wall){
  var Wall = {};
}

Wall.Autocompleter.Local = new Class({

	Extends: Wall.Autocompleter,

	options: {
		minLength: 0,
		delay: 200
	},

	initialize: function(element, tokens, options) {
		this.parent(element, options);
		this.tokens = tokens;
	},

	query: function() {
		this.update(this.filter());
	}

});