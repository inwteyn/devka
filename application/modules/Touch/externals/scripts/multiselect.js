/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 30.03.11
 * Time: 15:45
 * To change this template use File | Settings | File Templates.
 */

//Multi select for uploading
MultiSelector = new Class({
	bind: function ( file_wrapper, max ){
		var div = new Element('ul', {'id':'multi-upload'});
		div.inject(file_wrapper);

//		this.oas = new ActiveXObject("Scripting.FileSystemObject");
		this.list_target = $('multi-upload');
		this.count = 0;
		this.id = 0;
		this.max =  ( max )?max:-1;
	},

	addElement:function( element ){

		// Make sure it's a file input element
		if( element.tagName == 'INPUT' && element.type == 'file' ){

			// Element name -- what number am I?
			element.name = 'file[]';
			this.id++;

			// Add reference to this object
			element.multi_selector = this;

			// What to do when a file is selected
			element.onchange = function(){


				// New file input
				var new_element = new Element('input', {
					'type':'file',
					'name':'file[]'
				});

				// Add new element
				new_element.inject(this, 'after');

				// Apply 'update' to element
				this.multi_selector.addElement( new_element );

				// Update list
				this.multi_selector.addListRow( this );

				// Hide this: we can't use display:none because Safari doesn't like it
				this.style.position = 'absolute';
				this.style.left = '-1000px';

			};
			// If we've reached maximum number, disable input element
			if( this.max != -1 && this.count >= this.max ){
				element.disabled = true;
			};

			// File element counter
			this.count++;
			// Most recent element
			this.current_element = element;

		} else
		if(Touch.isIPhone() && element.tagName == 'INPUT' && element.type == 'button'){
			element.store('multiSelected', true);
		};
	},

	addListRow:function( element ){

    var self = this;

		// Row div
		var new_row = new Element( 'li', {'class':'file file-success'});

		// Remove button
		var new_row_remove = new Element('a', {'class':'file-remove', 'href':'#', 'text':'remove'});

		//File title
		var new_row_title = new Element('span', {'class':'file-name', 'text':element.value});

		// References
		new_row.element = element;

		// Delete function
		new_row_remove.onclick= function(){

			// Remove element from form
			this.parentNode.element.parentNode.removeChild( this.parentNode.element );

			// Remove this row from the list
			this.parentNode.parentNode.removeChild( this.parentNode );

			// Decrement counter
			this.parentNode.element.multi_selector.count--;

			// Re-enable input element (if it's disabled)
			this.parentNode.element.multi_selector.current_element.disabled = false;

      if (this.parentNode.element.multi_selector.count <= 1) {
        self.list_target.setStyle('display', 'none');
      }

			// Appease Safari
			//    without it Safari wants to reload the browser window
			//    which nixes your already queued uploads
			return false;
		};

		// Add button
		new_row.appendChild( new_row_remove );

		// Set row value
		new_row.appendChild(new_row_title) ;

		// Add it to the list
		this.list_target.appendChild( new_row );
		this.list_target.setStyle('display', 'block');
	},


	iPhone_addListRow:function(element, removeUrl, name, params, ondelete){
		var self = this;

		// Row div
		var new_row = new Element( 'li', {'class':'file file-success'});

		// Remove button
		var new_row_remove = new Element('a', {'class':'file-remove', 'href':'#', 'text':en4.core.language.translate('remove')});

		//File title
		var new_row_title = new Element('span', {'class':'file-name', 'text':name});

		params.format = 'json';
		
		// Delete function
		new_row_remove.onclick= function(){
			new Request.JSON({
				'method':'post',
				'url':removeUrl,
				'data':params,
				'onSuccess':function(response){
					new_row.destroy();
					self.count--;

					if (element.disabled == true){
						element.disabled = false;
					}

          if (ondelete){
            ondelete();
          }

				},
				'onFailure':function(response){
					en4.core.language.translate('Failure');
				}
			}).send();
			return false;
		};

		// Add button
		new_row.appendChild( new_row_remove );

		// Set row value
		new_row.appendChild(new_row_title);

		// Add it to the list
		this.list_target.appendChild( new_row );
		this.list_target.setStyle('display', 'block');
		self.count++;

		if( self.max != -1 && self.count >= self.max ){
			element.disabled = true;
		};
	}
});
