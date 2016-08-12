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

<?php
  $request = Zend_Controller_Front::getInstance()->getRequest();



  $active = null;

  if ($request->getModuleName() == 'hebadge' && $request->getControllerName() == 'credit'){

    $active = 'hebadge_main_credit';

    $navigation = Engine_Api::_()
        ->getApi('menus', 'core')
        ->getNavigation('credit_main', array(), $active);

?>

  <div class="headline">
    <h2>
      <?php echo $this->translate('Credits');?>
    </h2>
    <?php if( count($navigation) > 0 ): ?>
    <div class="tabs">
      <?php
      // Render the menu
      echo $this->navigation()
          ->menu()
          ->setContainer($navigation)
          ->render();
      ?>
    </div>
    <?php endif; ?>
  </div>


  <?php

  } else {

    if ($request->getModuleName() == 'hebadge' && $request->getControllerName() == 'index' && $request->getActionName() == 'index'){
      $active = 'hebadge_main_home';
    } else if ($request->getModuleName() == 'hebadge' && $request->getControllerName() == 'index' && $request->getActionName() == 'manage'){
      $active = 'hebadge_main_manage';
    }

    $navigation = Engine_Api::_()
        ->getApi('menus', 'core')
        ->getNavigation('hebadge_main', array(), $active);

?>

  <div class="headline">
    <h2>
      <?php echo $this->translate('HEBADGE_HOME_TITLE') ?>
    </h2>
    <div class="tabs">
      <?php
      // Render the menu
      echo $this->navigation()
          ->menu()
          ->setContainer($navigation)
          ->render();
      ?>
    </div>
  </div>

  <?php

  }

?>




