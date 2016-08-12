<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: members.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<h2><?php echo $this->translate('HEBADGE_ADMIN_MEMBERS_TITLE');?></h2>
<p><?php echo $this->translate('HEBADGE_ADMIN_MEMBERS_DESCRIPTION');?></p>

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


    <div class='admin_search'>
      <?php echo $this->formFilter->render($this) ?>
    </div>

    <br />

    <div class='admin_results'>
      <div>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => true,
          'query' => $this->formValues,
          //'params' => $this->formValues,
        )); ?>
      </div>
    </div>

    <br />


    <?php if (count($this->paginator)):?>

      <table class="admin_table admin_hebadge_manage">
        <thead>
          <tr>
            <th style='width: 1%;'><?php echo $this->translate("ID") ?></th>
            <th><?php echo $this->translate("Display Name") ?></th>
            <th><?php echo $this->translate("Username") ?></th>
            <th style='width: 1%;'><?php echo $this->translate("Email") ?></th>
            <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("User Level") ?></th>
            <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if( count($this->paginator) ): ?>
            <?php foreach( $this->paginator as $item ):
              $user = $this->item('user', $item->user_id);
              ?>
              <tr>
                <td><?php echo $item->user_id ?></td>
                <td class='admin_table_bold'>
                  <?php echo $this->htmlLink($user->getHref(),
                      $this->string()->truncate($user->getTitle(), 10),
                      array('target' => '_blank'))?>
                </td>
                <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
                <td class='admin_table_email'>
                  <?php if( defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER ): ?>
                    (hidden)
                  <?php else: ?>
                    <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
                  <?php endif; ?>
                </td>
                <td class="admin_table_centered nowrap">
                  <a href="<?php echo $this->url(array('module'=>'authorization','controller'=>'level', 'action' => 'edit', 'id' => $item->level_id)) ?>">
                    <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $item->level_id)->getTitle()) ?>
                  </a>
                </td>
                <td class='admin_table_options'>
                  <a class='smoothbox' href='<?php echo $this->url(array('action' => 'edit-member', 'id' => $item->getIdentity(), 'page' => 1));?>'>
                    <?php echo $this->translate("HEBADGE_MEMBERS_ITEM_BADGES") ?>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
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


