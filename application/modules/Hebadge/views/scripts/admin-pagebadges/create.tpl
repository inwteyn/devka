<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>
<h2><?php echo $this->translate('HEBADGE_ADMIN_PAGE_MANAGE_TITLE');?></h2>
<p><?php echo $this->translate('HEBADGE_ADMIN_PAGE_MANAGE_DESCRIPTION');?></p>

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

    <div class="settings">
      <?php echo $this->form->render();?>
    </div>

  </div>

</div>