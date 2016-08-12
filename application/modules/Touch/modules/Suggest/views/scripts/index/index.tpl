<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<h2 class="suggest_header"><?php echo $this->translate('suggest_suggests_page_header'); ?></h2>
<p class="suggest_desc"><?php echo $this->translate('suggest_suggests_page_description'); ?></p>

<div class="suggest-container">
  <?php if (count($this->paginator) > 0): ?>
  <?php foreach( $this->paginator as $key => $suggests ): ?>
    <div class="suggest-item">
      <div class="suggest-title">
        <?php echo
          $this->translate(array(
            'suggest_'.$key.'_suggestions_title',
            'suggest_'.$key.'_suggestions_title',
            count($suggests)
          ), count($suggests)); ?>
      </div>
      <ul class="items">
        <?php
          foreach ($suggests as $suggest) {
            echo '<li>' . $suggest . '</li>';
          }
        ?>
       </ul>
    </div>
  <? endforeach; ?>
  <?php else: ?>
    <br />
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no suggestions.'); ?>
      </span>
    </div>
  <?php endif; ?>
</div>