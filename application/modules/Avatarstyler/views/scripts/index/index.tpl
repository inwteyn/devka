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
//    window.onload = function () {
//
//        var fW = document.getElementById('fieldset-avatars');
////    $$('#fieldset-avatars .form-label').setStyles({display:'none'});
////    $$('#fieldset-avatars .form-wrapper').setStyles({float:'left',clear:'none'});
////        for (var i = 1; i <= fW.children.length-1; i++) {
////            var b = new Element('a#'+i);
////            b.setAttribute('style','display:block;width:30px;height:30px;line-height:30px;border-radius: 50%;color:#f5f5f5;text-align:center;text-decoration:none;background: #464646;position: absolute;top: 0;right: 0;')
////            b.setAttribute('onclick','test(this)');
//            $(fW.children[i].children[1]).setStyle('position', 'relative');
//            $(fW.children[i].children[1]).grab(b);
//        }
//    };
    function test (id){
          $('preview').set('src','/application/modules/Core/externals/images/loading.gif');
        var data = id;
        $('imgId').value = id;
        var myRequest = new Request({url:'<?php echo
           Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
            'module' => 'avatarstyler',
            'controller' => 'index',
            'action' => 'showpreview'
          ))?>',
            onSuccess: function(data){
                if(data){
                    $('preview').set('src',data.trim());
                } else {
                   alert('error');
                }

            },
            onFailure: function(data){
                console.log("Failure");
            }});

        myRequest.send({
            method: 'post',
            data: {
                'imgId':data
            }

        });

    }
//preloader

</script>
<div class="headline">
  <h2>
    <?php echo $this->translate('Avatarstyler_Edit My Avatar'); ?>
  </h2>

  <div class="tabs">
    <?php
    // Render the menu
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->render();
    ?>
  </div>
</div>
<?php echo $this->form->render($this) ?>
<div class="zagr" style="display: none"></div>
<div class = container>

    <?php foreach($this->photoIDs as $photoID):?>
        <div style="position: relative; width: 200px; display:inline-block; height: 150px; background-image: url(<?php echo Engine_Api::_()->avatarstyler()->getLayer($photoID);?>);background-size: contain;background-repeat:no-repeat;background-position: center;">
<!--            <img src="--><?php //echo Engine_Api::_()->avatarstyler()->getLayer($photoID);?><!--" alt="phoyo" style="max-width: 100%;"/>-->
            <a href="javascript:void(0);" class="round_button hei hei-check-circle" onclick="test(<?php echo $photoID?>)" >
            </a>
        </div>
            <?php endforeach ?>
</div>
