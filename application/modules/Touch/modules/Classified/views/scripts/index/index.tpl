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
					'filter_default_value'=>$this->translate('TOUCH_Search Classifieds'),
					'filterUrl'=>$this->url(array(), 'classified_general', true)
				)
		); ?>
	</div>

	<div id="filter_block">

  <?php if( $this->tag ): ?>
    <h3>
      <?php echo $this->translate('Showing classified listings using the tag');?> #<?php echo $this->tag_text;?> <a href="<?php echo $this->url(array('module' => 'classified', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
    </h3>
  <?php endif; ?>

  <?php if( $this->start_date ): ?>
    <?php foreach ($this->archive_list as $archive): ?>
      <h3>
        <?php echo $this->translate('Showing classified listings created on');?> <?php if ($this->start_date==$archive['date_start']) echo $archive['label']?> <a href="<?php echo $this->url(array('module' => 'classified', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>">(x)</a>
      </h3>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="items classifieds_profile_tab">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='item_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('class' => 'touchajax')) ?>
          </div>
          <div class='item_body'>
            <div class='classifieds_browse_info_title'>
              <div>
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'touchajax')) ?>
              <?php if( $item->closed ): ?>
                <img src='application/modules/Classified/externals/images/close.png'/>
              <?php endif;?>
              </div>
            </div>
            <div class='item_date classifieds_browse_info_date'>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              - <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('class' => 'touchajax')) ?>
            </div>
          </div>
          <?php echo $this->touchClassifiedRate('classified', $item->getIdentity())?>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif( $this->search ):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted a classified listing with that criteria.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'classified_general', true).'" class="touchajax">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has posted a classified listing yet.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'classified_general', true).'" class="touchajax">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

  </div>
</div>



