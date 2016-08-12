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
					'search'=>$this->form->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search'),
					'filterUrl'=>$this->url(array('module' => 'core', 'controller' => 'search', 'action' => 'index'), 'default', true)
				)
		); ?>
	</div>

	<div id="filter_block">

    <ul class="items">
      <?php foreach( $this->paginator as $item ):
      $item = $this->item($item->type, $item->id);
      if( !$item ) continue; ?>
      <li>
        <div class="item_photo" style="display:block; width:50px ; height:50px; ">
          <div class="search_photo">
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
          </div>
        </div>
        <div class="item_body">
          <?php if( '' != $this->search ): ?>
            <?php echo $this->htmlLink($item->getHref(), $this->highlightText($item->getTitle(), $this->search), array('class' => 'search_title')) ?>
          <?php else: ?>
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title')) ?>
          <?php endif; ?>
          <p>
            <?php if( '' != $this->search ): ?>
              <?php echo $this->highlightText($this->touchSubstr($item->getDescription()), $this->search); ?>
            <?php else: ?>
              <?php echo $this->touchSubstr($item->getDescription()); ?>
            <?php endif; ?>
          </p>
        </div>
      </li>
      <?php endforeach; ?>

    </ul>

    <?php if( empty($this->search) ): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('Please enter a search query.') ?>
        </span>
      </div>
    <?php elseif( $this->paginator->getTotalItemCount() <= 0 ): ?>
      <div class="tip">
        <span>
          <?php echo $this->translate('No results were found.') ?>
        </span>
      </div>
    <?php endif;?>

  </div>

</div>

