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

<?php if( !empty($this->channel) ): ?>
  <ul class="items">
    <?php $count=0;foreach( $this->channel['items'] as $item ): $count++ ?>
      <li class="rss_item">
        <div class="item_body">
          <?php echo $this->htmlLink($item['link'], $item['title'], array('target' => '_blank', 'class' => 'item_title')) ?>
          <p class="rss_desc">
            <?php $desc = Engine_String::strip_tags($item['description']); if( Engine_String::strlen($desc) > 350 ): ?>
              <?php echo Engine_String::substr($desc, 0, 350) ?>...
            <?php else: ?>
              <?php echo $desc ?>
            <?php endif; ?>
          </p>
          <br>
          <div class="item_date">
            <?php echo $this->locale()->toDatetime(strtotime($item['pubDate']), array('size' => 'long')) ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
    <li class="rss_last_row">
      <div>
        &nbsp;
      </div>
      <div>
        &#187; <?php echo $this->htmlLink($this->channel['link'], $this->translate("More"), array('target' => '_blank')) ?>
      </div>
    </li>
  </ul>
<?php endif; ?>