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

<ul class="generic_list_widget">
  <?php foreach( $this->paginator as $item ): ?>
  <?php
    if( $item['type'] == 'page' )
      $playlist = Engine_Api::_()->getItem('playlist', $item['playlist_id']);
    else
      $playlist = Engine_Api::_()->getItem('music_playlist', $item['playlist_id']);

  ?>
  <li>
    <div class="photo">
      <?php echo $this->htmlLink($playlist->getHref(), $this->itemPhoto($playlist, 'thumb.icon'), array('class' => 'thumb')) ?>
    </div>
    <div class="info">
      <div class="title">
        <?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?>
      </div>
      <div class="stats">
        <?php echo $this->translate(array('%s play', '%s plays', $playlist->play_count), $this->locale()->toNumber($playlist->play_count)) ?>
      </div>
      <div class="owner">
        <?php
        $owner = $playlist->getOwner();?>
        <?php if( $item['type'] == 'page' && (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.show.owner', 0) == 1 || Engine_Api::_()->getItem('page', $playlist->page_id)->getOwner() != $playlist->getOwner())) : ?>
          <?php echo $this->translate('Posted by %1$s',
            $this->htmlLink($playlist->getOwner()->getHref(), $playlist->getOwner()->getTitle())) ?>
        <?php elseif ($item['type'] == 'page'):?>
          <?php echo $this->translate('Posted by %1$s',
            $this->htmlLink(Engine_Api::_()->getItem('page', $playlist->page_id)->getHref(), Engine_Api::_()->getItem('page', $playlist->page_id)->getTitle())) ?>
        <?php else:?>
          <?php echo $this->translate('Posted by %1$s',
            $this->htmlLink($playlist->getOwner()->getHref(), $playlist->getOwner()->getTitle())) ?>
        <?php endif;?>
        <?php
        if( $item['type'] == 'page' ) {
          echo '<br/>';
          echo $this->translate('On page ');
          echo $this->htmlLink($playlist->getPage()->getHref(), $playlist->getPage()->getTitle());
        }
        ?>
      </div>
    </div>
  </li>
  <?php endforeach; ?>
</ul>