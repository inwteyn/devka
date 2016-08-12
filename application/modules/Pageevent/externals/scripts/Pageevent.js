
/* $Id: Pageevent.js 2010-05-25 01:44 michael $ */

var Pageevent =
{
    url: {
        'form': '',
        'edit': '',
        'remove_photo': '',
        'remove': '',
        'view': '',
        'list': '',
        'rsvp': '',
        'resource_approve': '',
        'member_approve': '',
        'invite': '',
        'waiting': ''
    },

    list_param: {
        page: 1,
        ipp: 10,
        page_id: 0,
        show: 'upcoming'
    },

    time_out: 500,
    event_id: 0,

    $loader: null,
    $elm: null,
    $form: null,

    init: function ()
    {
        this.$loader = $('pageevent_loader');
        this.$elm = $('pageevent');
        this.$form = $('pageevent-form');
    },

    goForm: function ()
    {
        this.loadTab('form');
    },

    goView: function ()
    {
        this.loadTab('view');
    },

    formEvent: function (event_id)
    {
        var self = this;
        if (event_id)
        {
            this.request(this.url.edit, {id: event_id}, function (obj){
                if (obj.result){
                    self.loadTab('form');
                    self.formReset();

                    var $title = self.$form.getElement('h3');
                    if ($title){
                        $title.set('html', en4.core.language.translate('PAGEEVENT_EDIT_TITLE'));
                    }
                    var $description = self.$form.getElement('p.form-description');
                    if ($description){
                        $description.set('html', en4.core.language.translate('PAGEEVENT_EDIT_DESCRIPTION'));
                    }

                    self.$form.getElements('.datepicker_container.starttime-container input')
                        .set('value', obj.event_info.starttime);

                    self.$form.getElements('.datepicker_container.endtime-container input')
                        .set('value', obj.event_info.endtime);

                    if (obj.photo_html){
                        self.$form.getElement('#event_photo-demo-status').setStyle('display', 'none');
                        self.$form.getElement('#event_photo-demo-list')
                            .setStyle('display', 'block')
                            .set('html', obj.photo_html);
                    }
                    self.$form.title.value = obj.event_info.title;
                    self.$form.description.value = obj.event_info.description;
                    self.$form.location.value = obj.event_info.location;
                    $('approval').checked = obj.event_info.approval;
                    $('invite').checked = obj.event_info.invite;
                    self.$form.id.value = event_id;
                    self.$form.getElements('input[name="privacy"][value="'+obj.view_auth+'"]').setProperty('checked', true);
                }
            });
        }
        else
        {
            self.loadTab('form');
            self.formReset();

            var $title = self.$form.getElement('h3');
            if ($title){
                $title.set('html', en4.core.language.translate('PAGEEVENT_CREATE_TITLE'));
            }
            var $description = self.$form.getElement('p.form-description');
            if ($description){
                $description.set('html', en4.core.language.translate('PAGEEVENT_CREATE_DESCRIPTION'));
            }
        }

    },

    formSubmit: function (form)
    {
        var self = this;
        this.request(this.url.form, $(form).toQueryString()+'&page_id='+self.page_id, function (obj){
            if (obj.result){
                self.$form.event_photo_fileid.value = 0;
                self.showMessage(obj.html);
                self.setCounter(obj.count);
                setTimeout(function (){ self.view(obj.id); }, self.time_out);
            } else {
                self.showMessage(obj.html);
            }
        });
        return false;
    },

    formReset: function ()
    {
        window.event_photo_up.fileList.each(function (file){
            file.remove();
        });
        this.$form.title.value = '';
        this.$form.description.value = '';
        this.$form.starttime.value = '';
        this.$form.endtime.value = '';
        this.$form.location.value = '';
        $('approval').checked = false;
        $('invite').checked = true;

        this.$form.getElements('.datepicker_container input').set('value', '');
        this.$form.id.value = 0;

        $('event_photo-demo-list').setStyle('display', 'none').empty();
        $('event_photo-demo-status').setStyle('display', 'block');

    },

    formCancel: function ()
    {
        var self = this;
        if (this.$form.id.value != 0){
            this.view(this.$form.id.value);
        } else {
            this.list();
        }
        setTimeout(function (){ self.formReset(); } , this.time_out);
        return false;
    },

    removePhoto: function (photo_id)
    {
        this.request(this.url.remove_photo, {'photo_id': photo_id});

        this.$form.getElement('#event_photo-demo-status').setStyle('display', 'block');
        this.$form.getElement('#event_photo-demo-list').empty().setStyle('display', 'none');

    },

    loadTab: function (tab)
    {
        var $tab = this.$elm.getElement('.tab_' + tab);
        this.$elm.getElements('.tab').addClass('hidden');
        $tab.removeClass('hidden');
        return $tab;
    },

    remove: function (id)
    {
        var self = this;
        he_show_confirm(
            en4.core.language.translate('PAGEEVENT_DELETE_TITLE'),
            en4.core.language.translate('PAGEEVENT_DELETE_DESCRIPTION'),
            function (){
                self.request(self.url.remove, {'id': id, 'page_id': self.page_id}, function (obj){
                    if (obj.result){
                        self.showMessage(obj.html);
                        self.setCounter(obj.count);
                        setTimeout(function (){ self.list(); }, self.time_out);
                    } else {
                        self.showMessage(obj.html);
                    }
                });
            }
        );
    },

    view: function (id)
    {
        var self = this;
        this.request(this.url.view, {'id': id, 'page_id': self.page_id}, function (obj)
        {
            if (obj.result){
                self.loadTab('view').set('html', obj.html);
                obj.html.stripScripts(true);
              var opt =false;
              $$('.content').getElement('.header').each(function(el){
                if(el){
                  opt = el.getElement('.options');

                }
              })
              if(opt) {
                opt.getElements('.pageevent_rsp_status').each(function (el) {
                  el.setStyle('opacity', '0');
                })
                opt.addEvent('mouseover', function () {
                    opt.getElements('.pageevent_rsp_status').each(function (el) {
                      el.fade("in");
                    })
                  }
                );
                opt.addEvent('mouseout', function () {
                    opt.getElements('.pageevent_rsp_status').each(function (el) {
                      el.fade("out");
                    })
                  }
                );
              }
                en4.core.runonce.trigger();
                new LikeTips('pageevent', id, {
                    'container' : 'pageevent_comments',
                    'html' : obj.likeHtml,
                    'url' : {
                        'like' : obj.likeUrl,
                        'unlike' : obj.unlikeUrl,
                        'hint' : obj.hintUrl,
                        'showLikes' : obj.showLikesUrl,
                        'postComment' : obj.postCommentUrl
                    }
                });
            } else {
                self.showMessage(obj.html);
            }
        });

    },

    list: function (show, page)
    {
        var self = this;
        this.formReset();
        if (show == 'upcoming' || show == 'past' || show == 'user'){
            this.list_param.show = show;
        }
        if (page){
            this.list_param.page = page;
        }
        this.list_param.page_id = self.page_id;
        this.list_param.ipp = self.ipp;
        this.request(this.url.list, this.list_param, function (obj){
            if (obj.html){
                self.setCounter(obj.count);
                self.loadTab('list').set('html', obj.html);
            }
        });
    },

    rsvp: function (id, rsvp)
    {
        var self = this;
        this.request(this.url.rsvp, {'id': id, 'rsvp': rsvp}, function (obj){
            if (obj.result){
                self.showMessage(obj.html);
                setTimeout(function (){ self.view(id); }, self.time_out);

            } else {
                self.showMessage(obj.html);
            }
        });
    },

    memberApprove: function (id, approve)
    {
        var self = this;
        this.request(this.url.member_approve, {'id': id, 'approve': approve}, function (obj){
            if (obj.result){
                self.showMessage(obj.html);
                setTimeout(function (){ self.view(id); }, self.time_out);
            } else {
                self.showMessage(obj.html);
            }
        });
    },

    resourceApprove: function (id, user_id, approve, element)
    {
        var self = this;
        this.request(this.url.resource_approve, {'id': id, 'user_id': user_id, 'approve': approve}, function (obj){
            if (obj.result)
            {
                var $element = $(element);
                if ($element){ $element.getParent('.item').destroy(); }

                var $count = $('pageevent_waiting_list_count');
                if ($count){ $count.set('html', obj.count); }

                if (obj.count == 0){
                    self.waiting(id);
                }
            }
        });
    },

    init_event : function() {
        if($$('.tab_layout_pageevent_profile_event a')[0])
            tabContainerSwitch($$('.tab_layout_pageevent_profile_event a')[0], 'generic_layout_container layout_pageevent_profile_event');
        if($$('.more_tab .tab_layout_pageevent_profile_event')[0])
            tabContainerSwitch($$('.more_tab .tab_layout_pageevent_profile_event')[0], 'generic_layout_container layout_pageevent_profile_event');
    },

    loadView: function (id)
    {
        if($$('.tab_layout_pageevent_profile_event a')[0])
            tabContainerSwitch($$('.tab_layout_pageevent_profile_event a')[0], 'generic_layout_container layout_pageevent_profile_event');
        else if($$('.more_tab .tab_layout_pageevent_profile_event')[0])
            tabContainerSwitch($$('.more .tab_layout_pageevent_profile_event')[0], 'generic_layout_container layout_pageevent_profile_event');

        this.view(id);
    },

    invite: function (id)
    {
        this.event_id = id;
        var title = en4.core.language.translate('PAGEEVENT_INVITE_TITLE');
        var disabled_label = en4.core.language.translate('PAGEEVENT_INVITE_DISABLED');
        he_contacts.box('pageevent', 'getInviteMembers',
            "Pageevent.doInvite", title, {'id': id, 'disabled_label': disabled_label});

    },

    doInvite: function  (user_ids)
    {
        var self = this;
        self.request(self.url.invite, {'id': this.event_id, 'user_ids' : user_ids}, function (obj){
            if (obj.result){
                self.showMessage(obj.html);
                setTimeout(function (){ self.view(self.event_id); }, self.time_out);
            } else {
                self.showMessage(obj.html);
            }
        });
    },

    members: function (id, rsvp)
    {
        this.event_id = id;
        var key = '';
        if (rsvp == 2){
            key = 'PAGEEVENT_MEMBERSBOX_ATTENDING';
        } else if (rsvp == 1){
            key = 'PAGEEVENT_MEMBERSBOX_MAYBE_ATTENDING';
        } else {
            key = 'PAGEEVENT_MEMBERSBOX_NOT_ATTENDING';
        }
        he_list.box('pageevent', 'getMembers', en4.core.language.translate(key), {id: id, rsvp: rsvp});
    },

    waiting: function (id)
    {
        var self = this;
        this.request(this.url.waiting, {id: id}, function (obj){
            if (obj.result){
                self.showMessage(obj.html);
            }
        });
    },

    showMessage: function (html)
    {
        this.loadTab('message').set('html', html);
    },

    setCounter: function (count)
    {
        var $counter = $$('.tab_layout_pageevent_profile_event span')[0];
        if ($counter){ $counter.set('html', '('+count+')'); }
    },

    request: function (url, data, callback)
    {
        var self = this;

        if (typeof(data) == 'string')
        {
            data += '&format=json&no_cache=' + Math.random();
        }
        else if (typeof(data) == 'object')
        {
            data.format = 'json';
            data.nocache = Math.random();
        }

        if (self.$loader != null)
            self.$loader.removeClass('hidden');

        var request = new Request.JSON({
            secure: false,
            url: url,
            method: 'post',
            data: data,
            onRequest: function(){
                if($('loader_pageevent')){ $('loader_pageevent').setStyle('display','block'); }
            },
            onSuccess: function(obj)
            {
                self.$loader.addClass('hidden');
                if($('pageevent-composer-create-form')){
                    setTimeout(function(){

                        var The = new Wall.Composer.Plugin.Pageevent();

                        if (!The.is_composer_opened) {

                        }

                        The.els.createForm.set('style', '');
                        The.els.createForm.setStyle('display', 'block');
                        (function(cf){
                            setTimeout(function(){cf.set('style', '');}, 500);
                        })(The.els.createForm);
                        The.els.createFormBg.set('style', '');
//    $$('.wallFeed .wallTextareaContainer, .wallFeed .wall-stream-header, .wallFeed .submitMenu').show();
                        var self = The;
                        The.resetForm();
                        The.deactivate();

                    },1000)

                    var wall_feed_id = $$('.wallFeed').get('id');
                    var wall = Wall.feeds.get(wall_feed_id);
                    wall.checkEmptyFeed();
                    obj = $merge(wall.params, {
                        'minid': obj.last_id,
                        'checkUpdate': false
                    });
                    wall.feed.getElements('.container-get-last').destroy();
                    wall.loadFeed(obj, 'top', function (){
                        wall.checkActive = false;
                    });

                }
                if (callback){ callback(obj); }
            }
        }).send();

    }

};