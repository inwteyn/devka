<h2>
    <?php echo $this->translate('HE-Emoticon Emoticon Plugin') ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<div class="export_collection_contaner">
<?php echo $this->htmlLink(array('action' => 'export-collection', 'collection_id' => $this->collection->getIdentity()), $this->translate('Export').' <i class="hei hei-upload hei-lg" ></i>', array(
  'class' => 'export-collection-btn',
  'title' => $this->translate('Export'))); ?>
</div>

<div class='clear'>
    <div class='settings'>
        <div class="he_emoticon_form editcollection_form">
            <?php echo $this->form->render($this) ?>
        </div>
    </div>
</div>


<?php foreach ($this->stickers as $sticker): ?>

    <div id="sticker-block-<?php echo $sticker['photo_id'] ?>" class="uploaded-sticker-block">
        <div class="stickers-preview-img" style="background-image: url(<?php echo $sticker->url; ?>);"></div>

        <i class="hei hei-times stickers-preview-img-delete" onclick="delete_sticker_by_id(this)"
           delete_id="<?php echo $sticker->getIdentity(); ?>" photo_id="<?php echo $sticker['photo_id']; ?>"></i>

        <i class="hei hei-check-circle hei-lg stickers-preview-img-cover"
           onclick="collection_cover_select(this) " photo_id="<?php echo $sticker['photo_id']; ?>"></i>
    </div>

<?php endforeach; ?>

<script>
    window.addEvent('domready', function () {
        $$('.uploaded-sticker-block').each(function(elem){
            elem.inject($('uploaded-stickers-preview'));
        });
        <?php if($this->collection->cover):?>
            $('sticker-block-<?php echo $this->collection->cover ?>').getElement('.stickers-preview-img-cover').click();
        <?php else :?>
            if($('uploaded-stickers-preview').getElements('.stickers-preview-img-cover')[0]){
                $('uploaded-stickers-preview').getElements('.stickers-preview-img-cover')[0].click();
            }
        <?php endif?>
        $('add_collection_submit_btn').set('html', 'Save changes');

        stickers_sortable();

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