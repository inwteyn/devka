<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: upload.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */
?>

<?php if($this->form->isErrors()): ?>

<script type="text/javascript">
    Touchform.show_errors( <?php echo Zend_Json:: encode($this->form->getMessages()); ?>);

    en4.core.runonce.add(function(){
    Touch.goto('<?php echo $this->url(array('action' => 'upload', 'album_id' => $this->album_id), 'album_general', true)?>');
    });
</script>

<?php else: ?>

<script type="text/javascript">
    (function()
    {
        window.updateTextFields = function()
            {
                var fieldToggleGroup = ['#title-wrapper', '#category_id-wrapper', '#description-wrapper', '#search-wrapper',
                    '#auth_view-wrapper',  '#auth_comment-wrapper', '#auth_tag-wrapper'];
                fieldToggleGroup = $$(fieldToggleGroup.join(','))
                if ($('album').get('value') == 0) {
                    fieldToggleGroup.show();
                } else {
                    fieldToggleGroup.hide();
                }
            }
      window.multiSelect = new MultiSelector();
      en4.core.runonce.add(function() {
        updateTextFields();
        multiSelect.bind('file-wrapper', 5);
        multiSelect.addElement($('file'));
      });

if(Touch.isIPhone())
{
    window.uploadedPhotos = [];
    window.photoDeleteUrl = "<?php echo $this->url(array('module'=>'album', 'controller'=>'photo', 'action'=>'delete'), 'default'); ?>";
    window.Picup.responseCallback = function(response)
        {
            if ($type(response.photo_id) == 'number'){
            uploadedPhotos[uploadedPhotos.length] = response.photo_id;
            $('photos').set('value', uploadedPhotos);
            }

            multiSelect.iPhone_addListRow($('iPhone-file-button'), photoDeleteUrl, response.photo_name, response, function ()
            {
                if ($type(response.photo_id) == 'number')
                {
                    var index = uploadedPhotos.indexOf(response.photo_id);
                    if (index != -1){
                        delete uploadedPhotos[index];
                    }
                    $('photos').set('value', uploadedPhotos);
                }
            });
        }
}
    })();
</script>

<?php if( count($this->navigation) > 0 ): ?>
<?php
			// Render the menu
			echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->setPartial(array('navigation/index.tpl', 'touch'))
                ->render();
?>
<?php endif; ?>

<div id="navigation_content">
    <div class="layout_content">
        <?php echo $this->form->setAttrib('class', 'global_form touchupload touch-multi-upload')->render($this); ?>
    </div>
</div>

<?php endif; ?>