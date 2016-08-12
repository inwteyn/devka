<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: gateway-edit.tpl  21/9/12 6:21 PM teajay $
 * @author     TJ
 */
?>
<?php echo $this->render('_editMenu.tpl'); ?>

<div class="headline offers">
  <h2><?php echo $this->translate('Manage');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>

<div class="layout_right he-items">
  <ul class="he-item-list">
    <li>
      <div class="he-item-options">
        <?php echo $this->htmlLink(array('page_id'=>$this->page->getIdentity()), $this->translate('Back'), array(
          'class' => 'buttonlink offer_back')); ?>
        <br>
      </div>
    </li>
  </ul>
</div>

<div class="layout_middle settings">
  <?php echo $this->form->render($this) ?>
</div>