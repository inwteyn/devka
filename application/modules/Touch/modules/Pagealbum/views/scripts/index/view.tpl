<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>
  <?php echo $this->htmlLink($this->album->getOwner()->getHref(), $this->album->getOwner()->getTitle(), array('class' =>
              'touchajax')); echo $this->translate("'s");?> <?php echo $this->translate("Album"); echo $this->translate(":") ?> <?php echo $this->htmlLink(array('route' => 'page_album', 'action' => 'view', 'album_id' => $this->album->getIdentity()), ($this->album->getTitle()) ? $this->album->getTitle() : $this->translate('Untitled')) ?>
</h4>

<div class="layout_content">
	<?php if( '' != trim($this->album->getDescription()) ): ?>
		<p class="description">
			<?php echo $this->album->getDescription() ?>
		</p>
		<br />
	<?php endif ?>
    <div class="page_ext_timestamp">
      <?php echo $this->translate('Posted');?>
      <?php echo $this->timestamp(strtotime($this->album->creation_date)) ?>
    </div>
		<ul class="items">
			<?php foreach( $this->paginator as $photo ): ?>
				<li style="border-top: none; float: left;">
					<a class="thumbs_photo" href="<?php echo $this->url(array('action' => 'view-photo', 'photo_id' => $photo->getIdentity()), 'page_album') ?>" onclick="Touch.navigation.subNavRequest($(this)); return false;">
						<div class="item_photo">
							<img src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" height="80px"/>
						</div>
						<div class="item_body">
							<?php
                 $title = $photo->getTitle();
                 if(strlen($title) > 14)
                   $title = substr($title, 0, 14).'...';
                 if(!$title || $title == null)
                   $title = 'Untitled';
               echo $title;
              ?>
						</div>
					</a>
				</li>
			<?php endforeach;?>
		 </ul>
	</div>

<?php echo $this->touchAction("list", "comment", "core", array("type"=>"pagealbum", "id"=>$this->album->getIdentity(), 'viewAllLikes'=>true)); ?>

<br/>