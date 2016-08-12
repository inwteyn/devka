<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8098 2010-12-21 03:23:33Z john $
 * @author     Steve
 */
$urlParams = array(
  'module' => 'core',
  'controller' => 'widget',
  'action' => 'index',
  'content_id' => $this->identity,
  'subject' => $this->subject()->getGuid(),
  'format' => 'html'
);
?>

<div id="widget_content">
  <div class="search">
    <?php echo $this->paginationControl($this->paginator, null,
        array('pagination/filter.tpl', 'touch'),
        array(
          'search'=>$this->form->getElement('search')->getValue(),
          'filter_default_value'=>$this->translate('TOUCH_Search Music'),
          'filterUrl'=> $this->url($urlParams, 'default', true),
          'filterOptions' => array(
            'replace_content' => 'widget_content',
            'noChangeHash' => 1,
          ),
          'pageUrlParams' => $urlParams
        )
    ); ?>
  </div>
  <div id="filter_block">
    <ul id="profile_music" class="music_browse">
      <?php foreach( $this->paginator as $playlist ): ?>
      <li>
        <div class='music_browse_info'>
          <div class="music_browse_info_title">
            <?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?>
          </div>
          <div class='music_browse_info_date'>
            Posted <?php echo $this->timestamp($playlist->creation_date) ?>
          </div>
          <div class='music_browse_info_desc'>
            <?php echo $playlist->description ?>
          </div>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

