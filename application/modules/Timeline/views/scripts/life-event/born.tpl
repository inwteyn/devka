<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version ID: born.tpl 2/16/12 11:08 AM mt.uulu $
 * @author Mirlan
 */
?>
<script type="text/javascript">
    document.tl_born = new TimelineBorn();

    <?php if ($this->canEdit): ?>
    document.tl_born.setOptions({
        element_id: 'born-container-img',
        save_button: 'tl-born-save-button',
        edit_buttons: 'tl-born-edit-group',
        imgSrc: '<?php echo $this->bornPhoto["photoSrc"]; ?>',
        is_allowed: true,

        born_url: '<?php echo $this->url(array(
      'action' => 'get',
      'item_id' => $this->subject()->getIdentity(),
      'item_type' => 'user',
      'type' => 'born',
    ), 'timeline_photo', true); ?>',

        position_url: '<?php echo $this->url(array(
      'action' => 'position',
      'item_id' => $this->subject()->getIdentity(),
      'item_type' => 'user',
      'type' => 'born',
    ), 'timeline_photo', true); ?>'
    });

    try {
        document.tl_born.position = JSON.parse('<?php echo $this->bornPhoto["position"]; ?>');
    } catch(e) {
        console.log(e);
    }
    en4.core.runonce.add(function () {
        document.tl_born.init();
        document.tl_born.options.born_width = document.tl_born.get().getParent().getWidth();
    });
    <?php endif; ?>
</script>

<li class="born tli starred le">

    <div class="info">
        <div>
            <?php
            if ($this->subject()->getType() == 'user')
                echo $this->htmlImage('application/modules/Timeline/externals/images/born_icon.png', '');
            elseif ($this->subject()->getType() == 'page')
                echo $this->htmlImage('application/modules/Timeline/externals/images/page_timeline/created_icon.png', '');
            ?>
        </div>

        <div class="date">
            <?php
            if ($this->subject()->getType() == 'user')
                echo $this->translate('TIMELINE_Born on %1s', $this->locale()->toDate($this->birthdate, array(
                    'size' => 'long',
                    'timezone' => false,
                )));
            elseif ($this->subject()->getType() == 'page') {
                echo $this->translate('TIMELINE_Created on %1s', $this->locale()->toDate($this->birthdate, array(
                    'size' => 'long',
                    'timezone' => false,
                )));
            }
            ?>
        </div>
    </div>

    <?php  if ($this->photoExists || ($this->viewer()->getIdentity() && $this->canEdit)): ?>
    <div class="photo-container <?php if (!$this->photoExists): ?>add<?php endif; ?>">

        <a href="<?php echo $this->bornPhoto['photoHref']; ?>"
           id="born-container" class="born">
            <img id="born-container-img" src="<?php echo $this->bornPhoto['photoSrc']; ?>"/>
        </a>
        <?php if ($this->canEdit): ?>
        <div class="born-edit he-button-group">
            <a id="tl-born-save-button" class="he-btn wp_init" type="button" style="display: none;">
                <?php echo $this->translate('TIMELINE_Save Positions'); ?>
            </a>

            <div id="tl-born-edit-group" class="he-btn-group" style="display: inline-block;">
                <a data-toggle="dropdown" class="he-btn he-dropdown-toggle wp_init" type="button">
                    <?php echo $this->translate('TIMELINE_Edit Born'); ?> <i class="he-caret"></i>
                </a>
                <ul class="he-dropdown-menu">
                    <?php if ($this->isAlbumEnabled): ?>
                        <li><?php echo $this->htmlLink(array(
                                    'route' => 'timeline_photo',
                                    'item_type' => $this->subject_type,
                                    'item_id' => $this->subject_id,
                                    'type' => 'born',
                                    'reset' => true
                                ),
                                $this->translate('TIMELINE_Choose from Photos...'),
                                array(
                                    'class' => 'cover-albums smoothbox',
                                )); ?>
                        </li>
                    <?php endif; ?>

                    <li>

                        <?php echo $this->htmlLink(array(
                                'route' => 'timeline_photo',
                                'action' => 'upload',
                                'item_type' => $this->subject_type,
                                'item_id' => $this->subject_id,
                                'type' => 'born',
                                'reset' => true
                            ),
                            $this->translate('TIMELINE_Upload Photo...'),
                            array(
                                'class' => 'cover-albums smoothbox',
                            )); ?>
                    </li>

                    <li><?php echo $this->htmlLink(
                            'javascript:document.tl_born.reposition.start()',
                            $this->translate('TIMELINE_Reposition...'),
                            array('class' => 'cover-reposition')); ?>
                    </li>

                    <li><?php echo $this->htmlLink(array(
                                'route' => 'timeline_photo',
                                'action' => 'remove',
                                'type' => 'born',
                                'item_type' => $this->subject_type,
                                'item_id' => $this->subject_id,
                            ),
                            $this->translate('TIMELINE_Remove...'), array(
                                'class' => 'cover-remove smoothbox')); ?>
                    </li>
                </ul>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>
</li>
