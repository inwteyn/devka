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

<?php
$wid = rand(1, 10000);
?>

<ul class="suggest-widget-container items">

<?php
$ids = array();
?>

  <?php if (count($this->items) > 0): ?>
  <?php foreach ($this->items as $type => $item): ?>
    <?php $ids[$type][] = $item->getIdentity(); ?>
    <?php $this->item = $item; ?>
    <li class="suggest-item">

      <div class="item_photo">
        <?php
              if (!isset($this->item->photo_id)) {
        echo $this->htmlLink($this->item->getHref(), $this->htmlImage($this->baseUrl() . '/application/modules/Suggest/externals/images/nophoto/' . $this->item->getType() . '.png', '', array('class' => 'touchajax thumb_icon item_photo_' . $this->item->getType())), array('class' => 'touchajax'));
      } else {
        echo $this->htmlLink($this->item->getHref(), $this->itemPhoto($this->item, 'touchajax thumb.icon'), array('class' => 'touchajax'));
      }
        ?>
      </div>

      <div class="item_body">
        <div class="item_options">
          <?php echo $this->partial('widget/options.tpl', 'touch', array('object' => $this->item)); ?>
        </div>

        <div class="item_title">
          <?php echo $this->htmlLink($this->item->getHref(), $this->truncate($this->item->getTitle(), 12), array('class'  => 'touchajax')); ?>
        </div>

        <div class="clr"></div>

        <div class="item_date">
          <span>
            <?php echo $this->touchSuggestDetails($this->item); ?>
          </span>
        </div>

        <div class="clr"></div>

      </div>

      <div class="clr"></div>

    </li>
    <?php endforeach; ?>
  <?php endif; ?>

  <div class="clr"></div>

</ul>