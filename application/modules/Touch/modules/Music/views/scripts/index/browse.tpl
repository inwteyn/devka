<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: browse.tpl 8300 2011-01-25 06:42:26Z john $
 * @author     Steve
 */
?>


<?php if( count($this->navigation) > 0 ): ?>
<?php
		// Render the menu
		echo $this->navigation()
->menu()
->setContainer($this->navigation)
->setPartial(array('navigation/index.tpl', 'touch'))
->render();
?>
<?php endif; ?>
<div id="navigation_content">

  <div class="search">
   <?php echo $this->paginationControl(
   $this->paginator,
    null,
    array('pagination/filter.tpl', 'touch'),
    array(
    'search'=>$this->formFilter->getElement('search')->getValue(),
    'filter_default_value'=>$this->translate('TOUCH_MUSIC_SEARCH'),
    'filterUrl'=>$this->url(array('controller'=>'index', 'action'=>'browse'), 'music_general', true)
    )
    ); ?>
  </div>
  <div id="filter_block">
  <?php if (0 == count($this->paginator) ): ?>

    <div class="tip">
      <span>
        <?php echo $this->translate('There is no music uploaded yet.') ?>
        <?php if( $this->canCreate ){ ?>
          <?php echo $this->htmlLink(array(
            'route' => 'music_general',
            'action' => 'create'
          ), $this->translate('Why don\'t you add some?'), array('class' =>
            'touchajax')) ?>
        <?php } ?>
      </span>
    </div>
  <?php else: ?>
  
    <ul class="items">
      <?php foreach ($this->paginator as $playlist): ?>
        <li>
          <div class="item_photo">
            <?php echo $this->htmlLink($playlist->getHref(),
                       $this->itemPhoto($playlist, 'thumb.icon', $playlist->getTitle()), array('class' =>
            'touchajax')) ?>
          </div>

          <div class="item_body">
              <div class="item_title">
                  <?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle(), array('class' =>
            'touchajax')) ?>
              </div>
              <div class="item_date">
                <?php echo $this->translate('Created %s by ', $this->timestamp($playlist->creation_date)) ?>
                <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle(), array('class' =>
            'touchajax')) ?>
                -
                <?php echo $this->htmlLink($playlist->getHref(),  $this->translate(array('%s comment', '%s comments', $playlist->getCommentCount()), $this->locale()->toNumber($playlist->getCommentCount())), array('class' =>
            'touchajax')) ?>
              </div>
              <div class="item_desc">
                <?php echo $playlist->description ?>
              </div>
          </div>
        </li>

      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  </div>
</div>
