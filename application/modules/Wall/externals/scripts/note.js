/**
 * Created by Медербек on 18.04.2015.
 */



if($$('.comment_body_127').length){
    var comment_clone = $$('.comment_body_127')[0].clone();

    if(comment_clone.getElements('.wall_smile').length){
        comment_clone.getElements('.wall_smile').each(function (el) {

            var smile_alt = el.get('alt');

            var smile_tag = window.Wall_smiles.filter(function( elem ) {

                if(elem.title.toLowerCase() == smile_alt.toString().toLowerCase()){
                    return elem;
                }
            });

            var textnode = document.createTextNode(smile_tag[0].tag);

            comment_clone.replaceChild(textnode, el);

        });
    }

    if(comment_clone.getElements('.tag_people').length){
        comment_clone.getElements('.tag_people').each(function (el) {

            var textnode = document.createTextNode('@' + el.get('rev'));

            comment_clone.replaceChild(textnode, el);

        });
    }

    if(comment_clone.getElement('.comment_photo')){
        var comment_photo = comment_clone.getElement('.comment_photo').clone();

        comment_photo.inject($('comment_attach_preview_image_wall1918'));   //TODO post_id

        var comment_photo_del = new Element('div',{
            'id':'delete_1918',
            'class':'wpClose hei hei-times delete_photo_in_comment_button'
        }).inject($('comment_attach_preview_image_wall1918'));

        comment_photo_del.addEvent('click', function () {
            var wall_feed_id = $$('.wallFeed').get('id');
            var feed = Wall.feeds.get(wall_feed_id);
            feed.compose.getPlugin('photo').delete_photo_album();
        });

    }

    if(comment_clone.getElement('.smiles_NEW')){
        var Heemoticons =  new Heemoticon();

        var comment_sticker = comment_clone.getElement('.smiles_NEW').clone();

        var used_id = comment_sticker.getElement('img').get('src').split('=').pop();
        var sticker_id = comment_sticker.getElement('img').get('sticker_id');

        var comment_photo_del = new Element('div',{
            'id':'delete_1918',
            'class':'wpClose hei hei-times delete_photo_in_comment_button'
        });

        comment_photo_del.addEvent('click', function(e){
            Heemoticons.deleteImageComposer(used_id,sticker_id,0);
        });

        comment_sticker.inject($('comment_attach_preview_image_wall1918')); //TODO post_id
        comment_photo_del.inject($('comment_attach_preview_image_wall1918')); //TODO post_id
    }

    comment_clone.innerHTML.trim().replace(/\s{2,}/g, ' ');
}





/* Replace smiles to tags*/

$$('.comment_body_119').getElements('.wall_smile')[0].each(function (el) {

    var smile_alt = el.get('alt');

    var smile_tag = window.Wall_smiles.filter(function( elem ) {

        if(elem.title.toLowerCase() == smile_alt.toString().toLowerCase()){
            return elem;
        }
    });

    var textnode = document.createTextNode(smile_tag[0].tag);

    $$('.comment_body_119')[0].replaceChild(textnode, el);

});


/* Get text from comment body */

$$('.comment_body_119')[0].innerHTML.trim().replace(/\s{2,}/g, ' ');


/* Get attached image id*/

var photo_id = $$('.comment_body_114').getElement('.comment_photo')[0].get('href').split('/').pop();

/* Replace user_name to revs */


$$('.comment_body_126').getElements('.tag_people')[0].each(function (el) {

    var textnode = document.createTextNode('@' + el.get('rev'));

    $$('.comment_body_126')[0].replaceChild(textnode, el);

});


/* Get POST_ID onclick */

//$p.getParent('.wall-action-item').get('rev').split('-').pop();


