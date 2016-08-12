<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: viewcomment.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */
/**
 * This view script is only visible when using captcha on the comment form.
 */
$this->headScript()
    ->appendFile($this->wallBaseUrl() . 'application/modules/Touch/modules/Wall/externals/scripts/core.js');

?>




<?php if( !isset($this->form) ) return; ?>

<script type="text/javascript">

    var form_ajax = function (){

      $('wall-comment-form').getElement('form').addEvent('submit', function (e){

        e.stop();

        var loader = new Wall.OverLoader($('global_content_simple'), 'loader2', {is_smoothbox: true});
        loader.show();

        Wall.request('<?php echo $this->url(array('action' => 'comment'), 'wall_extended', true)?>', $(this).toQueryString(), function (obj){

          loader.hide();

          if (window.parent && window.parent.Wall){

            window.parent.Wall.dialog.message(obj.message||obj.error, obj.status);

            if (obj.html){
              $('wall-comment-form').set('html', obj.html);
              form_ajax();
            }
            if (obj.status){
              window.parent.Wall.instances.getAll().each(function (item){
                var $item = item.getFeed().getChildren('li[rev=item-'+obj.id+']')[0];
                $item.set('html', obj.body);
                item.initAction($item);
              });
              window.parent.Smoothbox.close();
            }
          }
          }.bind(this));

      });

   };

  Wall.runonce.add(form_ajax);

</script>


<div id="wall-comment-form-container" style="padding:10px;width:350px;">
  
  <h3><?php echo $this->translate("Comment:") ?></h3>

  <div id="wall-comment-form">
    <?php echo $this->form->render($this) ?>
  </div>

  <script type="text/javascript">
    Wall.runonce.add(function (){
      document.getElementsByTagName('form')[0].style.display = 'block';
    });
  </script>

</div>