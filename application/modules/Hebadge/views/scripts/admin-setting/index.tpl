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
<h2><?php echo $this->translate('HEBADGE_ADMIN_SETTING_TITLE');?></h2>
<p><?php echo $this->translate('HEBADGE_ADMIN_SETTING_DESCRIPTION');?></p>

<br />

<?php if( count($this->navigation) ): ?>
   <div class='tabs'>
     <?php
       echo $this->navigation()->menu()->setContainer($this->navigation)->render();
     ?>
   </div>
 <?php endif; ?>

<div class="settings">
  <?php echo $this->form->render()?>
</div>
