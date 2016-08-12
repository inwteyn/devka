<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: itemList.tpl 2011-11-21 15:04:01 Ulan $
 * @author     Ulan
 */

?>

<?php
if(!$this->lang_posted){
  $this->lang_posted = $this->translate('Posted');
}
if(!$this->lang_by){
  $this->lang_by = $this->translate('by');
}
?>
<div id="navigation_content">

  <div class="search">
    <?php echo $this->paginationControl(
     $this->paginator,
      null,
      array($this->filter_params['path']?$this->filter_params['path']:'pagination/filter.tpl', 'touch'),
      $this->filter_params
    ); ?>
  </div>
  <div id="filter_block">
      <?php if($this->function['custom']){?>
        <div class="custom">
          <?php $this->function['custom']($this); ?>
        </div> 
      <?php } ?>
    <?php if( $this->paginator->getTotalItemCount() > 0 ){ ?>
    <ul class="items">
      <?php foreach( $this->paginator as $item ): ?>
      <li>
        <div class='item_photo'>
          <?php
          if($this->function['item_photo'])
            echo $this->function['item_photo']($item, $this);
          else
            echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon'), array('class' => 'touchajax'));
          ?>
        </div>
        <div class='item_body'>
          <?php
          if($this->function['item_title'])
            echo $this->function['item_title']($item, $this);
          else
            echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'touchajax'));
          if($this->function['item_rate']) {
            echo $this->function['item_rate']($item, $this);
          } elseif($this->rate)
            echo $this->touchItemRate($this->rate, $item->getIdentity());
          ?>
          <div class='item_date'>
          <?php
            if($this->function['item_date']){ 
              $time_n_owner = $this->function['item_date']($item, $this);
            if(is_array($time_n_owner))
              echo $this->lang_posted.' '.$this->timestamp(strtotime($time_n_owner['creation_date'])).' '. $time_n_owner['custom'].' '.$this->lang_by.' '. $time_n_owner['owner'];
            else
              echo $time_n_owner;
            }else
              echo $this->lang_posted.' '.$this->timestamp(strtotime($item->creation_date)).' '.$this->lang_by.' '. $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' => 'touchajax'));
          ?>
          </div>
          <div class="item_stats">
            <?php echo $this->translate(array("%s view", "%s views", $item->view_count), $this->locale()->toNumber($item->view_count)); ?>
            - <?php echo $this->translate(array("%s comment", "%s comments", $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>
          </div>
          <?php if($this->function['item_manage']){ ?>
          <div class="item_options">
            <?php foreach($this->function['item_manage']($item, $this) as $option){
              echo $option;
            }?>
          </div>
          <?php } ?>
        </div>
        <?php if($this->function['item_custom']){?>
          <div class="item_custom">
            <?php if($this->function['custom'])$this->function['item_custom']($item, $this); ?>
          </div>
        <?php } ?>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php }elseif( $this->search ){?>
    <div class="tip">
        <span>
          <?php echo $this->lang_no_search_results; ?>
          <?php if( $this->can_create): ?>
            <?php echo $this->lang_create_item; ?>
          <?php endif; ?>
        </span>
    </div>
    <?php } else { ?>
    <div class="tip">
        <span>
          <?php echo $this->lang_no_item_found; ?>
          <?php if( $this->can_create ): ?>
            <?php echo $this->lang_create_item; ?>
          <?php endif; ?>
        </span>
    </div>
    <?php } ?>
  </div>
</div>
