<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Usernotes
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 vadim $
 * @author     Vadim
 */
?>

<?php if ( $this->enabled ) { ?>

<?php
  $this->headScript()->appendFile($this->baseUrl() . '/application/modules/Usernotes/externals/scripts/core.js');
  $this->langvars = Zend_Json::encode(array(
    'your_note_saved' => $this->translate('Your note saved!'),
    'empty_text' => $this->translate('Please type your note!'),
  ));
?>

<script type="text/javascript">

en4.core.runonce.add(function() {
  he_usernotes.action_urls = <?php echo $this->urls_js; ?>;
  he_usernotes.langvars = <?php echo $this->langvars; ?>;
});

</script>

<div class="headline">
  <h2><?php echo $this->translate('Your Notes');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render();?></div>
</div>

<!--<div class='layout_right'></div>-->

<div class='layout_middle'>
  <h2><?php echo $this->translate('List of Your Notes');?></h2>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <?php $item_nechet = true; ?>
    <?php foreach ($this->paginator as $item): ?>
    <?php
      try {
        $user = $item->getParent();
      } catch (Exception $e) {
        $user = false;
      }

      if ($user === false) {
        continue;
      }
    ?>
    <?php if($item_nechet){?><div><?php } ?>
    <div class="he_usernote_item <?php if($item_nechet){?>usernote_leftitem<?php }else{ ?>usernote_rightitem<?php } ?>">
      <div class="usernotes_menu">
        <a href="<?php echo $item->getParent()->getHref() ?>" class="usernotes_edit"><?php echo $this->translate('Edit Note');?></a>
        <a href="javascript://" id="usernotes_delete" onclick="he_usernotes.usernote_id = <?php echo $item->usernote_id; ?>; Smoothbox.open($('delete_note_confrim'), {mode: 'Inline', width: 350, height: 100});" class="usernotes_delete"><?php echo $this->translate('Delete Note');?></a>
      </div>
      
      <div class="usernotes_photo">
        <?php echo $this->htmlLink($item->getParent()->getHref(), $this->itemPhoto($item->getParent(), 'thumb.icon')) ?>
      </div>

      <div class="usernotes_body">
        <div class="usernotes_info">
          <?php echo $this->translate('to');?> <?php echo $this->htmlLink($item->getParent(), $item->getParent()->getTitle()) ?>
          <?php echo $this->timestamp($item->creation_date) ?>
        </div>

        <?php echo $item->note; ?>
      </div>

      <div class="clr"></div>
    </div>
    <?php if(!$item_nechet){?><div class="clr"></div></div><?php } ?>
    <?php $item_nechet = !$item_nechet; ?>
    <?php endforeach; ?>

  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any notes.'); ?>
      </span>
    </div>
  <?php endif; ?>

  <div class='browse_nextlast'>
    <?php echo $this->paginationControl($this->paginator, null, null, null); ?>
  </div>

</div>
  
</div>

<div style="display: none;">
  <div id="delete_note_confrim">
    <div class="title" style="font-weight: bold; font-size: 11pt; margin-bottom: 10px;"><?php echo $this->translate('Delete Note'); ?></div>
    <div>
      <?php echo $this->translate('Are you sure you want to delete this note?'); ?><br /><br />
    </div>
    <div align="center">
      <button type="button" onclick="he_usernotes.delete_admin();"><?php echo $this->translate('Delete'); ?></button>
      <button type="button" onclick="Smoothbox.close()"><?php echo $this->translate('Cancel'); ?></button>
    </div>
  </div>
</div>

<?php } ?>