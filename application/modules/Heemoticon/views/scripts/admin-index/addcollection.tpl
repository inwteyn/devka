<h2>
    <?php echo $this->translate('HE-Emoticon Emoticon Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>


<div class='clear'>
    <div class='settings'>
        <div class="he_emoticon_form addcollection_form">
          <?php echo $this->form->render($this) ?>
        </div>
    </div>
</div>

<script>
    window.addEvent('domready', function () {

        $('add_collection_submit_btn').addEvent('click', function(e) {
            $('uploaded-stickers-preview').getChildren().each(function (el) {
                if(!$('order').value){
                    $('order').value += el.getElement('.stickers-preview-img-delete').get('photo_id');
                } else {
                    $('order').value += ',' + el.getElement('.stickers-preview-img-delete').get('photo_id');
                }

            });
        });

    });
    function checkInput(input, point) {
        if (point == true) {
            input.value = input.value.replace(/[^\d.]/g, '');
        } else {
            input.value = input.value.replace(/[^\d]/g, '');
        }
    }
</script>