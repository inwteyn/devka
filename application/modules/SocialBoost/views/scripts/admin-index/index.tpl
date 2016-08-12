<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl 08.10.13 14:26 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    SocialBoost
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<div class="settings">
  <?php echo $this->form->render($this);?>
</div>
