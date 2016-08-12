<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>


<div class="navigation_content">
	<ul class="items subcontent">
		<li>

			<div class="item_photo">
				<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner, 'thumb.profile'), array('class' => 'blogs_gutter_photo touchajax')) ?>
			</div>
			<div class="item_body">
			<?php if ($this->category && count($this->userCategories )):?>
				<ul class="categories blog_categories">
						<li> <a href="<?php echo $this->url(array('user_id'=>$this->owner->getIdentity()), 'blog_view', true); ?>" class="touchajax"><?php echo $this->translate('All Categories')?></a> </li>
						<?php foreach ($this->userCategories as $category): ?>
							<li> <a href="<?php echo $this->url(array('user_id'=>$this->owner->getIdentity(), 'category'=>$category->category_id), 'blog_view', true); ?>" class="touchajax <?php if ($category->category_id == $this->category):?>active<?php endif;?>"><?php echo $category->category_name?></a></li>
						<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			</div>
		</li>

  </ul>

  <div class="search">
    <?php echo $this->paginationControl(
        $this->paginator,
        null,
        array('pagination/filter.tpl', 'touch'),
        array(
          'search'=>$this->form->getElement('search')->getValue(),
          'filter_default_value'=>$this->translate('TOUCH_Search Blogs'),
          'filterUrl'=>$this->url(array('user_id' => $this->owner->getIdentity(), 'category' => $this->category), 'blog_view', true)
        )
    ); ?>
  </div>

  <ul id="filter_block" class="items subcontent" style="margin-top:5px;">

		<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
		<?php foreach ($this->paginator as $item): ?>
		<li>
			<div class="item_body">
				<?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class'=>'blogentry_title touchajax')) ?>

				<div class="item_date">
				 <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getParent(), $item->getParent()->getTitle(), array('class' => 'touchajax')) ?>
					<?php echo $this->timestamp($item->creation_date) ?>
				</div>

				<div class="item_date touch_box">
				<?php if ($item->comment_count > 0) :?>
					<?php echo $this->htmlLink($item->getHref(), $item->comment_count . ' ' . ( $item->comment_count != 1 ? 'comments' : 'comment' ), array('class' => 'buttonlink icon_comments touchajax')) ?>
				<?php endif; ?>
				</div>

			</div>
		</li>
		<?php endforeach; ?>

		<?php elseif( $this->category || $this->tag ): ?>
		<li>
			<span>
				<?php echo $this->translate('%1$s has not published a blog entry with that criteria.', $this->owner->getTitle()); ?>
			</span>
		</li>

		<?php else: ?>
		<li>
			<span>
				<?php echo $this->translate('%1$s has not written a blog entry yet.', $this->owner->getTitle()); ?>
			</span>
		</li>
		<?php endif; ?>
	</ul>

</div>