<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2010-07-02 18:53 mirlan $
 * @author     Mirlan
 */
?>
	<?php $owner = $this->item->getOwner();
		try{
		$itemThumb = ($this->item->getPhotoUrl('thumb.icon'))
			? $this->item->getPhotoUrl('thumb.icon')
			: $this->baseUrl() . '/application/modules/Article/externals/images/nophoto_article_thumb_icon.png';
		}catch(Exception $e){
		}
	?>

	<?php if ($this->step == 'thumb'): ?>

		<a href="http://<?php echo $_SERVER['HTTP_HOST'] . $owner->getHref(); ?>">
			<img style="border:1px solid #DDDDDD; width: 48px; height:48px;" src="http://<?php echo $_SERVER['HTTP_HOST'] . $itemThumb; ?>"/>
		</a>

	<?php elseif($this->step == 'details'): ?>

		<a href="http://<?php echo $_SERVER['HTTP_HOST'] . $this->item->getHref() ?>" style="font-weight: bold; color:<?php echo $this->linkColor?>; font-size: 12px;text-decoration: none" class="msgLink">
			<?php echo $this->item->getTitle(); ?>
		</a>

		<div style="font-size: 10px">
			<?php echo $this->translate('UPDATES_Posted on ') . ' ' . date('d M  Y', strtotime($this->item->creation_date)) . ' '	. $this->translate('UPDATES_by')?>
			<a style="color:<?php echo $this->linkColor; ?>; text-decoration: none" class="msgLink" href="http://<?php echo $_SERVER['HTTP_HOST'] . $owner->getHref(); ?>">
				<?php echo $owner->getTitle(); ?>
			</a>
		</div>
		<div style="margin-top:5px;">
			<?php echo Engine_String::substr(Engine_String::strip_tags($this->item->body), 0, 90);
						echo (Engine_String::strlen($this->item->body)>89)? "...":'';
			?>
		</div>

	<?php elseif($this->step == 'more_link'): ?>
    <div align="right">
      <a style="text-decoration:underline;color:<?php echo $this->linkColor; ?>" class="msgLink" href="http://<?php echo $_SERVER['HTTP_HOST'] . $this->url(array('module'=>'blogs'), 'default', true); ?>" target="blank">
        <?php echo $this->translate('UPDATES_More articles...'); ?>
      </a>
    </div>
	<?php endif; ?>	