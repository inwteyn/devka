<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<?php if( !$this->classified): ?>
<?php echo $this->translate('The classified you are looking for does not exist or has been deleted.');?>
<?php return; // Do no render the rest of the script in this mode
endif; ?>

<div class='layout_middle'>
  <h4>
    <?php echo $this->classified->getTitle(); ?>
    <?php if( $this->classified->closed == 1 ): ?>
      <img src='application/modules/Classified/externals/images/close.png'/>
    <?php endif;?>
  </h4>
  <ul class='classifieds_entrylist'>
    <li>
      <div class="classifide_entrylist_entry_date">
        <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->classified->getParent(), $this->classified->getParent()->getTitle(), array('class' => 'touchajax')) ?>
        <?php echo $this->timestamp($this->classified->creation_date) ?>
        <?php if ($this->category):?>- <?php echo $this->translate('Filed in');?>
        <?php echo $this->translate($this->category->category_name); ?>
        <?php endif; ?>

        - <?php echo $this->translate(array('%s view', '%s views', $this->classified->view_count), $this->locale()->toNumber($this->classified->view_count)) ?>

        <?php if (count($this->classifiedTags )):?>
        -
          <?php foreach ($this->classifiedTags as $tag): ?>
          <?php if (!empty($tag->getTag()->text)):?>
            #<?php echo $tag->getTag()->text?>&nbsp;
          <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>

        <div class="touch_box">
          <?php echo $this->fieldValueLoop($this->classified, $this->fieldStructure) ?>
        </div>

      </div>

      <?php if ($this->classified->closed == 1):?>
        <div class="tip">
          <span>
            <b><?php echo $this->translate('This classified listing has been closed by the poster.');?></b>
          </span>
        </div>
      <?php endif; ?>
      <div class="classified_entrylist_entry_body">
        <?php echo nl2br($this->classified->body) ?>
      </div>


    </li>
  </ul>



  <?php if ($this->paginator->getTotalItemCount() > 0 || $this->canUpload):?>

  <script type="text/javascript">
    (function(){
      Photobox.isOpen = true;
    })();
  </script>

  <?php
    // Render the menu & paginator
    if ($this->canUpload) {
      echo $this->navigationPaginator($this->navigation, $this->paginator);
    }
  ?>

  <div id="navigation_content">

    <?php if (!$this->paginator->getTotalItemCount()):?>
      <div class="tip">
        <span><?php echo $this->translate('TOUCH_ITEM_NO_PHOTOS')?></span>
      </div>
    <?php endif;?>

    <ul class="items">
      <?php foreach( $this->paginator as $photo ): ?>
        <li class="thumbs">
            <div class="item_photo">
              <a class="thumbs_photo touchajax" href="<?php echo $photo->getHref(); ?>">
                <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
              </a>
            </div>
        </li>
      <?php endforeach;?>
    </ul>

    <div class="clr"></div>
  </div>

  <br />

  <?php endif;?>



  <div class="classified_stats">
    <?php if( $this->canEdit ): ?>
      <?php echo $this->htmlLink(array(
        'route' => 'classified_specific',
        'action' => 'edit',
        'classified_id' => $this->classified->getIdentity(),
        //'format' => 'smoothbox'
      ), $this->translate("Edit"), array('class' => 'touchajax')); ?>
       - 
    <?php endif; ?>
    <?php if( $this->canDelete ): ?>
      <?php echo $this->htmlLink(array(
        'route' => 'classified_specific',
        'action' => 'delete',
        'classified_id' => $this->classified->getIdentity(),
        'format' => 'smoothbox'
      ), $this->translate("Delete"), array('class' => 'smoothbox')); ?>
       - 
    <?php endif; ?>
    <?php if( $this->canEdit ): ?>
      <?php if( !$this->classified->closed ): ?>
        <?php echo $this->htmlLink(array(
          'route' => 'classified_specific',
          'action' => 'close',
          'classified_id' => $this->classified->getIdentity(),
          'closed' => 1,
          'QUERY' => array(
            'return_url' => $this->url(),
          ),
        ), $this->translate('Close'), array('class' => 'touchajax')) ?>
      <?php else: ?>
        <?php echo $this->htmlLink(array(
          'route' => 'classified_specific',
          'action' => 'close',
          'classified_id' => $this->classified->getIdentity(),
          'closed' => 0,
          'QUERY' => array(
            'return_url' => $this->url(),
          ),
        ), $this->translate('Open'), array('class' => 'touchajax')) ?>
      <?php endif; ?>
       - 
    <?php endif; ?>
    <?php echo $this->htmlLink(array(
      'module' => 'activity',
      'controller' => 'index',
      'action' => 'share',
      'route' => 'default',
      'type' => 'classified',
      'id' => $this->classified->getIdentity(),
      'format' => 'smoothbox'
    ), $this->translate("Share"), array('class' => 'smoothbox')); ?>
  </div>

  <?php echo $this->content()->renderWidget('touch.rate-widget')?>

  <?php echo $this->touchAction("list", "comment", "core", array("type"=>"classified", "id"=>$this->classified->getIdentity(), 'viewAllLikes'=>true)); ?>


</div>