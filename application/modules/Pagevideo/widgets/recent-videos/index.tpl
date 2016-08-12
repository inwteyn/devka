<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-20 15:35 Ulan T $
 * @author     Ulan T
 */
?>

<ul class="generic_list_widget generic_list_widget_large_photo">
  <?php foreach( $this->paginator as $item ): ?>
  <?php
  if( $item['type'] == 'page' ){
    $video = Engine_Api::_()->getItem('pagevideo', $item['video_id']);
    $th = 'thumb.norm';
  }
  else {
    $video = Engine_Api::_()->getItem('video', $item['video_id']);
    $th = 'thumb.normal';
  }
  ?>

  <li>
    <div class="photo">
      <?php echo $this->htmlLink($video->getHref(), $this->itemPhoto($video, $th), array('class' => 'thumb page_album_widget_photo')) ?>
    </div>
    <div class="info">
      <div class="title">
        <?php echo $this->htmlLink($video->getHref(), $video->getTitle()) ?>
      </div>
      <div class="stats">
        <?php echo $this->timestamp($video->creation_date) ?>
      </div>
      <div class="owner">
        <?php
        $owner = $video->getOwner();
        echo $this->translate('Posted by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
        if( $item['type'] == 'page') {
          echo '<br/>';
          echo $this->translate('On page');
          echo $this->htmlLink($video->getPage()->getHref(), $video->getPage()->getTitle());
        }
        ?>
      </div>
    </div>
  </li>
  <?php endforeach; ?>
</ul>