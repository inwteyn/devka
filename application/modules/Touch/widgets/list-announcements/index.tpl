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

<ul class="items announcements">
  <?php foreach( $this->announcements as $item ): ?>
    <li>
      <div class="announcements_title">
        <?php echo $item->title ?>
      </div>
      <div class="announcements_info">
        <span class="announcements_author">
          <?php echo $this->translate('Posted by %1$s %2$s',
                        $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' => 'touchajax')),
                        $this->timestamp($item->creation_date)) ?>
        </span>
      </div>
      <div class="announcements_body">
        <?php echo $item->body ?>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
