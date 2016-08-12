<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Offers
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-06-06 17:01 ratbek $
 * @author     Ratbek
 */
?>




<h2><?php echo $this->translate("Offers Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render();
  ?>
</div>
<?php endif; ?>

<p>
  <?php echo $this->translate("OFFERS_ADMIN_POPULAR_DESCRIPTION") ?>
</p>



<br />


<br />

<?php echo $this->popular->render($this); ?>