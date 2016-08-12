<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-07-22 11:18:13 ulan $
 * @author     Ulan
 */

?>

<?php if( !$this->article || ($this->article->published==0 && !$this->article->isOwner($this->viewer()))): ?>
<?php echo $this->translate('"TOUCH_ARTICLE_NOT_EXIST_OR_NOT_PUBLISHED');?>
<?php return; // Do no render the rest of the script in this mode
endif; ?>
<script type="text/javascript">
  var categoryAction =function(category){
    $('category').value = category;
    $('filter_form').submit();
  }
  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }
  var dateAction =function(start_date, end_date){
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('filter_form').submit();
  }
</script>
<form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('user'=>$this->article->owner_id), 'article_browse', true) ?>' style='display: none;'>
  <input type="hidden" id="tag" name="tag" value=""/>
  <input type="hidden" id="category" name="category" value=""/>
  <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date) echo $this->start_date;?>"/>
  <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date) echo $this->end_date;?>"/>
</form>

<div class="layout_content">

<div class="touch-navigation">
  <div class="navigation-header">
    <h3>
      <?php echo $this->htmlLink($this->url(array(),'article_browse',true), $this->translate('Browse Articles')); ?>
      <?php if ($this->category):?>
        &raquo; <?php echo $this->htmlLink($this->url(array('category' => $this->category->category_id), 'article_browse'), $this->translate($this->category->category_name)); ?>
      <?php endif; ?>
      <?php // echo $this->translate('%1$s\'s Article', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
    </h3>
  </div>
</div>
<ul class="items subcontent">
	<li>
		<div class="item_photo">
			<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->user($this->article->owner_id), 'thumb.profile'), array('class' => 'articles_gutter_photo touchajax')) ?>
		</div>
		<div class="item_body">
			<h3><?php echo $this->article->getTitle() ?>        <?php if( $this->article->featured ): ?>
          <img src='application/modules/Touch/modules/Article/externals/images/featured.png' class='article_title_icon_featured' />
        <?php endif;?>
        <?php if( $this->article->sponsored ): ?>
          <img src='application/modules/Touch/modules/Article/externals/images/sponsored.png' class='article_title_icon_sponsored' />
        <?php endif;?>
</h3>
			<h4>
				<div class="item_date" style="font-weight:normal; font-size: 0.9em;">
					<?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'touchajax')) ?>
					<?php echo $this->timestamp($this->article->creation_date) ?>
					<?php if ($this->category):?>- <?php echo $this->translate('Filed in');?> <a href='javascript:void(0);' onclick="javascript:categoryAction(<?php echo $this->category->category_id?>);" class="touchajax"><?php echo $this->category->category_name ?></a> <?php endif; ?>

          <?php if ($this->viewer()->getIdentity()):?>
            - <?php echo $this->htmlLink(
              array(
                'route' => 'default',
                'module' => 'activity',
                'controller' => 'index',
                'action' => 'share',
                'type' => $this->article->getType(),
                'id' => $this->article->getIdentity(),
              ),
              $this->translate('Share'),
              array('class' => 'smoothbox')
            );?>
          <?php endif;?>

				</div>
			</h4>

			<?php if (count($this->userCategories )):?>
				<ul class="categories">
						<li><a href="<?php echo $this->url(array('user' => $this->article->owner_id), 'article_browse', true); ?>" class="touchajax"><?php echo $this->translate('All Categories')?></a></li>
						<?php foreach ($this->userCategories as $category): ?>
							<li> <a href="javascript:void(0);" onclick='javascript:categoryAction(<?php echo $category->category_id?>);' class="touchajax"><?php echo $category->category_name?></a></li>
						<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</li>

  <li style="text-align: right;">
    <?php  ?>
    <?php if ($this->canEdit):?>
    <a href='<?php echo $this->url(array('article_id' => $this->article->article_id), 'article_edit', true) ?>' class='touchajax'><img src='application/modules/Touch/externals/images/edit.png'/></a>
    <?php endif; ?>

    <?php if ($this->canDelete):?>
    <a href='<?php echo $this->url(array('article_id' => $this->article->article_id), 'article_delete', true) ?>' class='smoothbox'><img src='application/modules/Touch/externals/images/delete.png' /></a>
    <?php endif; ?>
  </li>

	<?php if ($this->article->owner_id == $this->viewer->getIdentity()&&$this->article->published == 0):?>
	<li style="border-top: 0px">
	<div>
		<span>
			<?php echo $this->translate('This article has not been published yet.');?> <?php echo $this->translate('TOUCH_ARTICLE_CAN_PUBLISH_BY',  '<a href="'.$this->url(array('article_id' => $this->article->article_id, 'action' => 'edit'), 'article_edit', true).'" class="touchajax">', '</a>'); ?>
		</span>
	</div>
	</li>
	<?php endif; ?>

	<li style="border-top: 0px;">
			<div class="item_body">
				<?php echo $this->article->body ?>
			</div>
	</li>

</ul>
  <?php $photoCount = $this->paginator->getTotalItemCount(); ?>
  <?php if ($photoCount): ?>
  <div class="article_entrylist_entry_photos">
    <h4>
      <span><?php echo $this->translate('Article Album'); ?>
      (<?php echo $this->htmlLink(array(
          'route' => 'article_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->article->getGuid(),
        ), $this->translate(array("%s photo", "%s photos", $photoCount), $photoCount), array('class'=>'touchajax'
      )) ?>)
      </span>
    </h4>
    <ul class="items">
      <?php $hasphoto = false; foreach( $this->paginator as $photo ): $hasphoto = true?>
        <li style="border: 0 none; float: left; margin: 0; padding: 0;">
          <div class="item_photo">
          <a class="thumbs_photo touchajax" href="<?php echo $photo->getHref(); ?>">
            <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
          </a>
          </div>
        </li>
      <?php endforeach;?>
      <li style="border: 0 none; float: left; margin: 0; padding: 0;">
        <div class="item_photo">
          <a class="thumbs_photo touchajax" href="<?php echo $photo->getHref(); ?>" style="border: none;">
          <div style="background-image: url(application/modules/Touch/modules/Article/externals/images/add_photos.png); "></div>
        </a>
        </div>
      </li>

    </ul>
  </div>
  <?php endif; ?>
  <div class="article_tool_links" style="clear: both;">
    <?php if($hasphoto) echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'article', 'id' => $this->article->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
  </div>

<?php
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('touch.article.rate-widget', 1)){ ?>
  <div style="clear: both; padding-top: 30px;">
    <?php echo $this->content()->renderWidget('touch.rate-widget') ?>
  </div>
<?php } ?>
<div style="padding-bottom: 5px;"></div>

<?php echo $this->touchAction("list", "comment", "core", array("type"=>"article", "id"=>$this->article->getIdentity(), 'viewAllLikes'=>true)) ?>

</div>