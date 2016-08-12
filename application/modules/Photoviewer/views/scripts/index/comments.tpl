<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: comments.tpl 08.02.13 10:28 michael $
 * @author     Michael
 */
?>

<script type="text/javascript">
    en4.core.runonce.add(function () {

        var taggerInstance = window.taggerInstance<?php echo $this->subject()->getIdentity();?> = new Tagger('imgPlace_<?php echo $this->subject()->getIdentity();?>', {
            'title': '<?php echo $this->string()->escapeJavascript($this->translate('ADD TAG'));?>',
            'description': '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.'));?>',
            'createRequestOptions': {
                'url': '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
                'data': {
                    'subject': '<?php echo $this->subject()->getGuid() ?>'
                }
            },
            'deleteRequestOptions': {
                'url': '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
                'data': {
                    'subject': '<?php echo $this->subject()->getGuid() ?>'
                }
            },
            'cropOptions': {
                'container': $('media_photo_next')
            },
            'tagListElement': 'media_tags_<?php echo $this->subject()->getIdentity();?>',
            'existingTags': <?php echo Zend_Json::encode($this->tags) ?>,
            'suggestProto': 'request.json',
            'suggestParam': "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
            'guid': <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
            'enableCreate': <?php echo ( $this->canTag ? 'true' : 'false') ?>,
            'enableDelete': <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
        });

        taggerInstance.addEvent('begin', function () {
            PhotoViewer.viewer().addClass('tagging_process');
            PhotoViewer.noZoom(1);
        });
        taggerInstance.onMove = function (coords) {
            this.coords = coords;
            var image_size = {w: $('lassoMask').getSize().x, h: $('lassoMask').getSize().y};
            var form_top = 0;
            var form_left = 0;
            var form = this.getForm();


            if (coords.x > image_size.w/2 && coords.y < image_size.h/2) {
                form_top = coords.y;
                form_left = coords.x - form.getSize().x - 20;
            } else if (coords.x < image_size.w/2 && coords.y > image_size.h/2) {
                form_top = coords.y - form.getSize().y + coords.h;
                form_left = coords.x + coords.w + 20;
            } else if(coords.x > image_size.w/2 && coords.y > image_size.h/2){
                form_top = coords.y - form.getSize().y + coords.h;
                form_left = coords.x - form.getSize().x - 20;
            } else {
                form_top = coords.y;
                form_left = coords.x + coords.w + 20;
            }

            form.setStyles({
                'top': form_top,
                'left': form_left
            });
        };

        taggerInstance.addEvent('end', function () {
            PhotoViewer.viewer().removeClass('tagging_process');
            PhotoViewer.noZoom(0);
        });

    });

</script>


<?php
$owner = $this->subject()->getOwner();
?>

<div class="owner_info">
    <div class="thumb">
        <a href="<?php echo $owner->getTitle(); ?>"><?php echo $this->itemPhoto($owner, 'thumb.icon'); ?></a>
    </div>
    <div class="poster">
        <a href="<?php echo $owner->getHref(); ?>"><?php echo $owner->getTitle(); ?></a>
    </div>
</div>

<div class="album_timestamp">
    <?php echo $this->translate('Added %1$s', $this->timestamp($this->photo->modified_date)) ?>
</div>

<div class="photo_info">
    <div class="photo_title"><?php echo $this->subject()->getTitle(); ?></div>
    <div class="photo_description"><?php echo $this->subject()->getDescription(); ?></div>
</div>


<div class="wpTags" id="media_tags_<?php echo $this->subject()->getIdentity(); ?>" style="display: none;"
     onmouseover="PhotoViewer.noZoom(1)" onmouseout="PhotoViewer.noZoom(0)">
    <?php echo $this->translate('Tagged:') ?>
</div>

<div class="external-options" style="display: none;">
    <?php echo $this->htmlLink(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), '<i class="hei hei-flag"></i>' . $this->translate("Report"), array('class' => 'smoothbox')); ?>
    <?php if ($this->photo->getType() == 'album_photo'): ?>
        <?php echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), '<i class="hei hei-picture-o"></i>' . $this->translate('Make Profile Photo'), array('class' => 'smoothbox')) ?>
    <?php endif; ?>
    <?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('photoviewer.downloadable', 1)): ?>
        <?php
        if (true) {
            echo '<a href="' . $this->photo->getPhotoUrl('thumb.default') . '" download="download" content="nofollow"><i class="hei hei-download"></i>' . $this->translate('PHOTOVIEWER_Download this photo') . '</a>';
        } else {
            ?>
            <?php echo $this->htmlLink(array('module' => 'photoviewer', 'controller' => 'index', 'action' => 'download', 'photo_id' => $this->photo->getIdentity(), 'isPage' => $this->isPage, 'format' => 'smoothbox'), '<i class="hei hei-download"></i>' . $this->translate('PHOTOVIEWER_Download this photo')) ?>
        <?php
        }
    endif;?>
</div>


<?php

// Edit links
$edit_url = false;
$delete_url = false;
if ($this->photo->getType() == 'album_photo') {
    $edit_url = array('module' => 'album', 'controller' => 'photo', 'action' => 'edit', 'photo_id' => $this->photo->getIdentity(), 'route' => 'default');
    $delete_url = array('module' => 'album', 'controller' => 'photo', 'action' => 'delete', 'photo_id' => $this->photo->getIdentity(), 'route' => 'default');
} else if ($this->photo->getType() == 'advalbum_photo') {
}

?>

<div class="external-top">
    <?php if ($this->viewer()->getIdentity()): ?>
        <?php if ($this->canTag): ?>
            <?php echo $this->htmlLink('javascript:void(0);', '<i class="hei hei-tag onlyicon"></i>', array('class' => "wpbtn wpbtn-inverse", 'onclick' => 'taggerInstance' . $this->subject()->getIdentity() . '.begin();', 'title' => $this->translate('Add Tag'))) ?>
        <?php endif; ?>
        <?php if ($this->canEdit && $edit_url): ?>
            <?php echo $this->htmlLink($edit_url, '<i class="hei hei-wrench onlyicon"></i>', array('class' => 'wpbtn wpbtn-inverse smoothbox', 'title' => $this->translate('Edit'))) ?>
        <?php endif; ?>
        <?php if ($this->canDelete && $delete_url): ?>
            <?php echo $this->htmlLink($delete_url, '<i class="hei hei-trash-o onlyicon"></i>', array('class' => 'wpbtn wpbtn-inverse smoothbox', 'title' => $this->translate('Delete'))) ?>
        <?php endif; ?>

        <a href="javascript:void(0);" class="actions wpbtn wpbtn-inverse" onclick="PhotoViewer.toggleOptions();"
           title="<?php echo $this->translate('PHOTOVIEWER_actions'); ?>">
            <i class="hei hei-reorder onlyicon"></i>
            <i class="hei hei-caret-down right"></i></a>
    <?php endif ?>

</div>

<div class="external-bottom">
    <?php if ($this->canEdit): ?>
        <a href="javascript:void(0)" onclick="save_position(<?php echo $this->subject()->getIdentity() ?>)"
           class="wpbtn wpbtn-inverse " id="save_button_<?php echo $this->subject()->getIdentity(); ?>"><i
                class="right hei hei-save" style="margin: 0"></i></a>
    <?php endif; ?>

    <a href="javascript:void(0)" onclick="rotate_photo_viewer(-1)" class="wpbtn wpbtn-inverse "><i
            class="right hei hei-undo" style="margin: 0"></i></a>
    <a href="javascript:void(0)" onclick="rotate_photo_viewer(1)" class="wpbtn wpbtn-inverse "
       style="margin-right: 15px"><i class="right hei hei-repeat" style="margin: 0"></i></a>

    <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
        <a
            onclick="en4.core.comments.unlike('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>'); photoViewerJquery(this).parent().find('.like').show();photoViewerJquery(this).hide();"
            class="wpbtn wpbtn-danger unlike"
            href="javascript:void(0);"
            <?php if (!$this->subject()->likes()->isLike($this->viewer())): ?>style="display: none;"<?php endif; ?>>
            <i class="hei hei-thumbs-down"></i>
            <?php echo $this->translate('Unlike'); ?>
        </a>

        <a
            onclick="en4.core.comments.like('<?php echo $this->subject()->getType() ?>', '<?php echo $this->subject()->getIdentity() ?>');photoViewerJquery(this).parent().find('.unlike').show();photoViewerJquery(this).hide();"
            class="wpbtn wpbtn-danger like"
            href="javascript:void(0);"
            <?php if ($this->subject()->likes()->isLike($this->viewer())): ?>style="display: none;"<?php endif; ?>>
            <i class="hei hei-thumbs-up"></i>
            <?php echo $this->translate('Like'); ?>
        </a>

    <?php endif; ?>

    <?php if ($this->viewer()->getIdentity() && $this->canComment): ?>
        <a onclick="photoviewer_show_hide_comment(<?php echo $this->subject()->getIdentity() ?>)" class="wpbtn wpbtn-inverse"
           href="javascript:void(0);">
            <i class="hei hei-comments-o"></i>
            <?php echo $this->translate('Comment'); ?>
        </a>
    <?php endif; ?>

    <?php if ($this->viewer()->getIdentity()): ?>
        <a class="wpbtn wpbtn-success smoothbox"
           href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => $this->photo->getType(), 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), 'default', true) ?>">
            <i class="hei hei-reply"></i>
            <?php echo $this->translate('Share'); ?>
        </a>
    <?php endif; ?>

    <a class="wpbtn wpbtn-inverse wp_init" href="<?php echo $this->photo->getHref(); ?>">
        <i class="hei hei-picture-o"></i>
        <?php echo $this->translate('PHOTOVIEWER_GOTO_PHOTO'); ?>
    </a>


</div>