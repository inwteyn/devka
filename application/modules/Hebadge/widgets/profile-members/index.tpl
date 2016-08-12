<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<?php if ($this->is_active):?>
  
  <script type="text/javascript">
    en4.core.runonce.add(function (){
      tabContainerSwitch($$('.tab_layout_hebadge_profile_members')[0], 'generic_layout_container layout_hebadge_profile_members');
    });
  </script>

<?php endif;?>

<div class="hebadge_widget_description">
  <?php echo $this->translate('HEBADGE_WIDGET_DESCRIPTION_BADGE_MEMBERS');?>
</div>

<?php if ($this->paginator->getTotalItemCount()):?>
  <ul class="hebadge_widget_members_browse">
    <?php foreach ($this->members as $item):?>
      <li>
        <div class="item_photo">
          <a href="<?php echo $item->getHref()?>">
            <?php echo $this->itemPhoto($item, 'thumb.icon');?>
           </a>
        </div>
        <div class="item_body">
          <div class="item_title"><a href="<?php echo $item->getHref()?>"><?php echo $item->getTitle();?></a></div>
          <div class="item_description">
            <?php echo $this->translate('HEBADGE_MEMBER_CREATION_DATE');?>
            <?php echo $this->timestamp($item->creation_date) ?>
          </div>
        </div>

      </li>
    <?php endforeach;?>
  </ul>
  
<?php else:?>

  <div class="tip"><span><?php echo $this->translate('HEBADGE_WIDGET_PROFILE_MEMBERS');?></span></div>

<?php endif;?>

<?php if ($this->paginator->count() > 1): ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","hebadge"), array(
    'ajax_url' => $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'hebadge.profile-members', 'subject' => $this->subject()->getGuid()), 'default', true),
    'ajax_class' => 'layout_hebadge_profile_members'
  ))?>
  <br />
<?php endif?>