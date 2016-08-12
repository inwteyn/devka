<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<div class="layout_content">
  <ul class="items"><li>
    <?php echo $this->suggest; ?>
    </li>
  </ul>

  <div class="suggest-view-all">
    <?php
      echo $this->htmlLink(
      $this->url(array(), 'suggest_view', true),
      $this->translate('View All Suggestions'),
      array('class' => 'buttonlink icon_viewmore touchajax'));
    ?>
  </div>
</div>