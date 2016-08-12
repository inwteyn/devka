var instagram = {
    //$$('#bg_modal')[0].inject($$('body')[0],'top');




    edit_photos : function(page_id){
        $$('.more_photos_show_for_users').hide();
        var elements_for_save = $$('.active_img');
        var array = [];
        for (var i = 0; i < elements_for_save.length; i++) {
            //console.log(elements_for_save[i].getChildren()[1].currentSrc);
            array.push({ user_id: elements_for_save[i].get('id'), href:elements_for_save[i].getChildren()[1].currentSrc });
        }

        (new Request.HTML({
            url: en4.core.baseUrl + 'page-instagram-edit?format=html',
            data: { json : JSON.stringify(array), page_id: page_id },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('content_instagram').set('html',responseHTML);
                $$('.more_view').hide();

            }
        })).send();
    },

    more_photos_show_for_users :function(page_id){

        var page = $$('#page').get('value')[0];
        (new Request.HTML({
            url: en4.core.baseUrl + 'page-instagram-more-photo-for-user?format=html',
            data: {page: page , page_id: page_id},
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('content_instagram').set('html',responseHTML);
                $$('.photo_viewer').removeEvents().addEvent('click',function(e){
                    $$('.link').set('href',$(this).getElementById('link').get('value'));
                    $$('.link').set('html',$(this).getElementById('username').get('value'));
                    $$('.description_instagram').set('html',$(this).getElementById('caption').get('value'));
                    $$('.like_instagram_count').set('html',$(this).getElementById('likes').get('value'));
                    $$('.comment_instagram_count').set('html',$(this).getElementById('comments').get('value'));
                    $$('.profile_img').set('src',$(this).getElementById('profile_picture').get('value'));
                    $$('.src_instagram').set('src',e.target.get('src'));
                    $$('#bg_modal')[0].inject($$('body')[0],'top');
                    $$('#modal')[0].inject($$('body')[0],'top');
                    $('bg_modal').show();
                    $('modal').show();
                });


                $$('.x_close').addEvent('click',function(e){
                    $$('#bg_modal')[0].inject($$('#pages-instagram-widget-container')[0],'top');
                    $$('#modal')[0].inject($$('#pages-instagram-widget-container')[0],'top');
                    $('bg_modal').hide();
                    $('modal').hide();
                });


                $$('#bg_modal').addEvent('click',function(e){
                    $$('#bg_modal')[0].inject($$('#pages-instagram-widget-container')[0],'top');
                    $$('#modal')[0].inject($$('#pages-instagram-widget-container')[0],'top');
                    $('bg_modal').hide();
                    $('modal').hide();
                });

            }
        })).send();
    },




    more_photos :function(){
        $$('.more_photos_show_for_users').hide();
        var page = $$('#page').get('value')[0];
        var tag = $$('#tag').get('value')[0];
        //console.log(tag);
        (new Request.HTML({
            url: en4.core.baseUrl + 'page-instagram?format=html',
            data: { tag : tag, page: page },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('content_instagram').set('html',responseHTML);
                $$('.no_check').addEvent('click',function(e){
                    var element = e.target.getParent('li');
                    if(element.get('class')!="no_check active_img"){
                        element.addClass('active_img');
                        element.setStyle('opacity', '1');
                        element.getChildren('span')[0].setStyle('display', 'block');
                    }else{
                        element.removeClass('active_img');
                        element.setStyle('opacity', '0.5');
                        element.getChildren('span')[0].setStyle('display', 'none');
                    }
                });
            }
        })).send();

    },


    check_all_btn :function(){
        $$('.more_photos_show_for_users').hide();
        var elements_check = $$('.no_check');
            if(elements_check){
                if($$('.btn_check_all')[0].get('class')!='btn_check_all check') {
                    for (var i = 0; i < elements_check.length; i++) {
                        elements_check[i].addClass('active_img');
                        elements_check[i].setStyle('opacity', '1');
                        elements_check[i].getChildren('span')[0].setStyle('display', 'block');
                    }
                    $$('.btn_check_all')[0].addClass('check');
                } else {
                    for (var e = 0; e < elements_check.length; e++) {
                        elements_check[e].removeClass('active_img');
                        elements_check[e].setStyle('opacity', '0.5');
                        elements_check[e].getChildren('span')[0].setStyle('display', 'none');
                    }
                    $$('.btn_check_all')[0].removeClass('check');
                }
            }
    },



    loadimagetag :function( tag ) {
        $$('.more_photos_show_for_users').hide();
        (new Request.HTML({
            url: en4.core.baseUrl + 'page-instagram?format=html',
            data: { tag : tag },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('content_instagram').set('html',responseHTML);

                $$('.no_check').addEvent('click',function(e){
                    var element = e.target.getParent('li');
                    if(element.get('class')!="no_check active_img"){
                        element.addClass('active_img');
                        element.setStyle('opacity', '1');
                        element.getChildren('span')[0].setStyle('display', 'block');
                    }else{
                        element.removeClass('active_img');
                        element.setStyle('opacity', '0.5');
                        element.getChildren('span')[0].setStyle('display', 'none');
                    }

                });
            }
        })).send();
    },


    save_photos :function(page_id) {
        $$('.more_photos_show_for_users').hide();
        var elements_for_save = $$('.active_img');

        var array = [];
        for (var i = 0; i < elements_for_save.length; i++) {
           array.push({ user_id: elements_for_save[i].get('id'),
                        href: elements_for_save[i].getChildren()[1].currentSrc ,
                        profile_picture: elements_for_save[i].getChildren()[3].defaultValue,
                        username: elements_for_save[i].getChildren()[4].defaultValue,
                        link: elements_for_save[i].getChildren()[2].defaultValue,
                        likes:elements_for_save[i].getChildren()[5].defaultValue,
                        comments:elements_for_save[i].getChildren()[6].defaultValue,
                        caption: elements_for_save[i].getChildren()[7].defaultValue
           });
        }

       (new Request.HTML({
            url: en4.core.baseUrl + 'page-instagram-save?format=html',
            data: { json : JSON.stringify(array), page_id: page_id },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('content_instagram').set('html',responseHTML);
                $$('.more_view').hide();

                $$('.photo_viewer').removeEvents().addEvent('click',function(e){
                    $$('.link').set('href',$(this).getElementById('link').get('value'));
                    $$('.link').set('html',$(this).getElementById('username').get('value'));
                    $$('.description_instagram').set('html',$(this).getElementById('caption').get('value'));
                    $$('.like_instagram_count').set('html',$(this).getElementById('likes').get('value'));
                    $$('.comment_instagram_count').set('html',$(this).getElementById('comments').get('value'));
                    $$('.profile_img').set('src',$(this).getElementById('profile_picture').get('value'));
                    $$('.src_instagram').set('src',e.target.get('src'));
                    $$('#bg_modal')[0].inject($$('body')[0],'top');
                    $$('#modal')[0].inject($$('body')[0],'top');
                    $('bg_modal').show();
                    $('modal').show();
                });


                $$('.x_close').addEvent('click',function(e){
                    $$('#bg_modal')[0].inject($$('#pages-instagram-widget-container')[0],'top');
                    $$('#modal')[0].inject($$('#pages-instagram-widget-container')[0],'top');
                    $('bg_modal').hide();
                    $('modal').hide();
                });


                $$('#bg_modal').addEvent('click',function(e){
                    $$('#bg_modal')[0].inject($$('#pages-instagram-widget-container')[0],'top');
                    $$('#modal')[0].inject($$('#pages-instagram-widget-container')[0],'top');
                    $('bg_modal').hide();
                    $('modal').hide();
                });



            }
        })).send();
    },

    delete_photos :function(instagram_id,page_id) {
        $$('.more_photos_show_for_users').hide();
       (new Request.HTML({
            url: en4.core.baseUrl + 'page-instagram-delete?format=html',
            data: { id : instagram_id, page_id: page_id },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('content_instagram').set('html',responseHTML);
            }
        })).send();
    }

};