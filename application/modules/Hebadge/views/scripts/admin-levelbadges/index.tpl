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

<h2><?php echo $this->translate('HEBADGE_ADMIN_MANAGE_TITLE');?></h2>
<p><?php echo $this->translate('HEBADGE_ADMIN_MANAGE_DESCRIPTION');?></p>

<br />

<div class="hebadge_layout_general">


  <?php if( count($this->navigation) ): ?>
     <div class='tabs'>
       <?php
         echo $this->navigation()->menu()->setContainer($this->navigation)->render();
       ?>
     </div>
   <?php endif; ?>

  <div class="hebadge_layout_left">
    <?php echo $this->partial('_adminMenuTabs.tpl', 'hebadge');?>
  </div>

  <div class="hebadge_layout_center">

    <?php if (count($this->paginator)):?>

      <table class="admin_table admin_hebadge_manage">
        <thead>
          <tr>
            <th width="1%"><?php echo $this->translate('HEBADGE_ADMIN_BADGE_ICON');?></th>
            <th width="100%"><?php echo $this->translate('HEBADGE_ADMIN_BADGE_TITLE');?></th>
            <th width="1%"><?php echo $this->translate('HEBADGE_ADMIN_BADGE_MEMBER_COUNT');?></th>
            <th width="1%"><?php echo $this->translate('HEBADGE_ADMIN_BADGE_DATE');?></th>
            <th width="1%"><?php echo $this->translate('HEBADGE_ENABLED');?></th>
            <th width="1%"><?php echo $this->translate('HEBADGE_ADMIN_BADGE_OPTIONS');?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->paginator as $item):?>
            <tr>
              <td><?php echo $this->itemPhoto($item, 'thumb.icon');?></td>
              <td>
                <div class="item_title"><?php echo $item->getTitle();?></div>
              </td>
              <td>
                <?php echo $this->translate(array('%1$s member', '%1$s members', $item->member_count), $item->member_count);?>
              </td>
              <td>
                <?php echo $this->locale()->toDateTime($item->creation_date) ?>
              </td>
              <td>
                <input type="checkbox" name="simple" onchange="Hebadge.request(en4.core.baseUrl+'admin/hebadge/badges/enabled/badge_id/<?php echo $item->getIdentity()?>/enabled/'+((this.checked)?1:0))" <?php if ($item->enabled):?>checked="checked"<?php endif;?> />
              </td>
              <td>
                <a href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'levelbadges', 'action' => 'edit', 'badge_id' => $item->getIdentity()), 'admin_default', true);?>"><?php echo $this->translate('Edit');?>
                </a>&nbsp;|&nbsp;<a class="smoothbox" href="<?php echo $this->url(array('module' => 'hebadge', 'controller' => 'levelbadges', 'action' => 'remove', 'badge_id' => $item->getIdentity()), 'admin_default', true);?>"><?php echo $this->translate('Delete');?></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <br />

      <?php if( $this->paginator->count() > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
          'query' => $this->formValues,
        )); ?>
      <?php endif; ?>

    <?php else: ?>
      <div class="tip">
        <span>
          <?php echo $this->translate("HEBADGE_ADMIN_EMPTY") ?>
        </span>
      </div>
    <?php endif; ?>

  </div>

</div>