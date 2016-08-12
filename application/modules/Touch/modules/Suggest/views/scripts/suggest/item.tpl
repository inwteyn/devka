<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: item.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

  <?php if (!in_array($this->object->getType(), array('blog'))): ?>
    <div class="item_photo">
      <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->itemPhoto($this->object, $this->thumb, '', array('style' => 'max-height: 110px')), array('class'=>'touchajax')); ?>
    </div>
  <?php else: ?>
    <div class="item_photo">
      <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->htmlImage($this->baseUrl().'/application/modules/Suggest/externals/images/nophoto/blog.png', '', array('style' => 'max-height: 110px')), array('class'=>'touchajax')); ?>
    </div>
  <?php endif; ?>
  <div class="item_body">
    <div class="item_title">
      <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->object->getTitle(), array('class'=>'touchajax', 'style'=>'float:left; margin-right: 5px;')); ?>
      <?php echo $this->likeEnabled ? $this->likeButton($this->object) : ''; ?>
    </div>
    <div class="clr"></div>
    <div class="item_options" style="margin-bottom:5px">
      <?php echo $this->touchSuggestOptions($this->suggest); ?>
    </div>

    <div class="item_date">
        <?php echo str_replace('<br /><br />', '<br/>', $this->suggest->getDescription()); ?>
    </div>

  </div>