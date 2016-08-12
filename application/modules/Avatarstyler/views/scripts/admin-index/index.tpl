<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Avatarstyler
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>

<script>
//window.onload = function () {

//    var fW = document.getElementById('fieldset-avatars');
////    $$('#fieldset-avatars .form-label').setStyles({display:'none'});
////    $$('#fieldset-avatars .form-wrapper').setStyles({float:'left',clear:'none'});
//    for (var i = 1; i <= fW.children.length-1; i++) {
//        var b = new Element('a#'+i+".round-button");
//        b.setAttribute('onclick','deletePhoto(this)');
//        $(fW.children[i].children[1]).setStyle('position', 'relative');
//        $(fW.children[i].children[1]).grab(b);
//    }
//};

    function deletePhoto (id){
        var data = id;
        var myRequest = new Request({url:'<?php echo
           Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
            'module' => 'avatarstyler',
            'controller' => 'index',
            'action' => 'remove-photo'
          ), 'admin_default', 1)?>',
            onSuccess: function(data){
                if(data){
                    alert("deleted");

                } else {
                    console.log("else");
                    return data;
                }
            },
            onFailure: function(data){
                console.log("Failure");
            }});

        myRequest.send({
            method: 'post',
            data: {
                'id':data
            }

        });

//        var jsonReq = new Request({
//            url: document.location.href,
//            method: 'post',
//            data: {
//                'per_page': parseInt(article_count) + 3
//            },
//            onComplete: function (response) {
//                $$('.layout_article_list_popular_articles')[0].set('html', response);
//            }
//        }).send();

    }


</script>
<div class="settings">

  <?php echo $this->form->render($this);?>
    <div class = container>
        <?php foreach($this->photoIDs as $photoID):?>
            <div style="position: relative; width: 200px; display:inline-block; height: 150px; background-image: url(<?php echo Engine_Api::_()->avatarstyler()->getLayer($photoID);?>);background-size: contain;background-repeat:no-repeat;background-position: center;">
<!--                <img src="--><?php //echo Engine_Api::_()->avatarstyler()->getLayer($photoID);?><!--" alt="phoyo" style="max-width: 100%;"/>-->
                <a href="javascript:void(0);" class="round_button hei hei-times" onclick="deletePhoto (<?php echo $photoID?>)">
                </a>
            </div>
        <?php endforeach ?>
    </div>
</div>