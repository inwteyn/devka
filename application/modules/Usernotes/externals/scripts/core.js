
/* $Id: Core.js 2010-07-30 18:00 vadim $ */

var he_usernotes = {

	usernote_id: 0,
	owner_id: 0,
	user_id: 0,
	sended_req: 0,
	note_text: '',
	note_info: '',
	state: 'view',

	langvars:{},
	action_urls:{},
	cont: {},

	construct: function(usernote_id, owner_id, user_id, note_text, state, urls, langvars)
	{
		this.usernote_id = usernote_id;
		this.owner_id = owner_id;
		this.user_id = user_id;
		this.note_text = note_text;
		this.state = state;

		this.cont.$note_text = $('he_usernotes_text');
		this.cont.$notes_info = $('usernotes_info');

		this.cont.$form = $('he_usernotes_form');
		this.cont.$input_user_id = $('user_id');
		this.cont.$input_note = $('note');
		this.cont.$input_note_info = $('he_usernotes_date');
		this.cont.$usernotes_edit = $('usernotes_edit');
		this.cont.$usernotes_delete = $('usernotes_delete');
		this.cont.$usernotes_help = $('usernotes_help');
		this.cont.$usernotes_cancel = $('he_usernotes_cancel');

		this.action_urls = urls;

		if (langvars) {
			this.langvars = langvars;
		}
	},

	toggleEdit: function(switch_to)
    {
		if ((switch_to != undefined) && (switch_to == this.state)) {
            return;
        }

		if (this.state =='view') {
			this.cont.$note_text.fade('out');
			this.cont.$notes_info.fade('out');
			this.cont.$form.fade('in');
			this.state = 'edit';
			//this.cont.$note_text.innerHTML = this.note_text;
		} else {
			this.cont.$note_text.setStyle('visibility','visible');
			this.cont.$notes_info.setStyle('visibility','visible');
			this.cont.$notes_info.fade('in');
			this.cont.$note_text.fade('in');
			this.cont.$form.fade('out');
			this.state = 'view';
		}
	},

	save: function()
    {
		if (this.sended_req) {
            return;
        }

		if (this.cont.$input_note.value.trim() == '') {
			he_show_message(this.langvars.empty_text, 'error');
			return;
		}

		var self = this;
		this.sended_req = 1;

        new Request.JSON({
            method: 'post',
            url: this.action_urls.save_note,
            data: {'user_id':this.cont.$input_user_id.value, 'note':this.cont.$input_note.value},
            onSuccess : function(response)
            {
				self.sended_req = 0;
                
				if (response.result.error == 0) {
					self.usernote_id = response.usernote.usernote_id;
					self.owner_id = response.usernote.owner_id;
					self.user_id = response.usernote.user_id;
					self.note_text = response.usernote.note_text;
					self.note_info = response.usernote.note_info;

					self.cont.$note_text.set('html', response.usernote.note_br);
					if( self.cont.$input_note_info ) self.cont.$input_note_info.set('html', response.usernote.note_info);
					self.cont.$usernotes_edit.fade('in');
					self.cont.$usernotes_delete.fade('in');
					self.cont.$usernotes_cancel.fade('in');
					self.note_text = self.cont.$input_note.value;
					self.toggleEdit('view');
				} else {
					he_show_message(response.result.message, 'error');
				}
			}
        }).send();
	},

	cancel: function()
    {
		if (this.note_text == '') {
            return;
        }

		this.toggleEdit();
		this.cont.$input_note.value = this.note_text;
	},

	delete_note: function()
    {
		if (this.sended_req) {
            return;
        }
        
        if (this.usernote_id == 0) {
            return;
        }

		var self = this;
		this.sended_req = 1;
		new Request.JSON({
            method: 'get',
	        url: this.action_urls.delete_note,
            data: {'usernote_id':this.usernote_id},
            onSuccess : function(response)
            {
				self.sended_req = 0;

        		if (response.result.error == 0) {
					self.note_text = '';
					self.cont.$usernotes_edit.fade('out');
					self.cont.$usernotes_delete.fade('out');

					self.cont.$input_note.value = '';
					self.cont.$input_note_info.innerHTML = '&nbsp;';

					self.note_text = '';
					self.note_info = '';

					self.cont.$usernotes_cancel.fade('out');
					self.toggleEdit('edit');
				} else {
        			he_show_message(response.result.message, 'error');
				}
				Smoothbox.close()
			}
        }).send();
    },

    delete_admin: function()
    {
        if (this.sended_req) {
            return;
        }

        if (this.usernote_id == 0) {
            return;
        }

        var self = this;
        this.sended_req = 1;

        new Request.JSON({
            method : 'get',
            url : this.action_urls.delete_note,
            data : {'usernote_id':this.usernote_id},
            onSuccess : function(response)
            {
                self.sended_req = 0;
                if (response.result.error == 0) {
                    window.location.reload();
                } else {
                    he_show_message(response.result.message, 'error');
                }
                Smoothbox.close()
            }
        }).send();
	}
};