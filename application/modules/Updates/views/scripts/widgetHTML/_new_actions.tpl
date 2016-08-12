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
	<?php if ($this->step == 'thumb'): ?>

		<a href="<?php echo $this->item->getSubject()->getHref(); ?>" style="border:1px solid #DDDDDD;padding:4px;vertical-align:bottom;text-decoration:none;display:inline-block;width:48px;height:48px;overflow:hidden">
      <?php echo $this->itemPhoto($this->item->getSubject(), 'thumb.icon'); ?>
		</a>

	<?php elseif($this->step == 'details'): ?>

		<?php
			switch ($this->item->type)
			{
				case 'friends':
				case 'friends_follow':
				case 'network_join':
					$type_icon = '/application/modules/User/externals/images/types/friend.png';
					break;

				case 'profile_photo_update':
					$type_icon = '/application/modules/User/externals/images/types/photo.png';
					break;

				case 'tagged':
					$type_icon = '/application/modules/User/externals/images/types/tag.png';
					break;

				case 'signup':
					$type_icon = '/application/modules/User/externals/images/types/user.png';
					break;

        case 'group_create':
        case 'group_join':
        case 'group_photo_upload':
        case 'group_promote':
        case 'group_topic_create':
        case 'group_topic_reply':
          $type_icon = '/application/modules/Group/externals/images/types/group.png';
          break;

				default :
					$type_icon = '/application/modules/Core/externals/images/types/post.png';
					break;
			}

      $type_icon = $this->baseUrl() . $type_icon;
		?>



		<span style="font-size: 12px;">
			<?php echo str_replace( array('class=', 'href='),
                              array('','style="text-decoration:none;color:'.$this->linkColor.'" href='),
                              $this->item->getContent()
      );?>

		</span>


		<div style="padding:5px;">
			<?php if( $this->item->getTypeInfo()->attachable && count($this->item->getAttachments()) > 0 ): ?>
          <table width="100%">

					<?php foreach( $this->item->getAttachments() as $attachment ): ?>
            <tr>

              <?php if (null !== $attachment->item->getPhotoUrl('thumb.normal')): ?>
                <td width="33%">
                <a href="<?php echo $attachment->item->getHref();?>">
                  <img src="<?php echo $attachment->item->getPhotoUrl('thumb.normal');?>" style="border: 4px solid #555555;"/>
                </a>
                </td>
              <?php endif; ?>

              <?php if (trim($attachment->item->getTitle()) != '' || trim($attachment->item->getDescription())): ?>
                <td valign="top">

                  <?php if (trim($attachment->item->getTitle()) != ''): ?>
                    <a href="<?php echo $attachment->item->getHref();?>" style="text-decoration:none;color:<?php echo $this->linkColor; ?>">
                      <?php echo $attachment->item->getTitle(); ?>
                    </a>
                  <?php endif; ?>

                  <?php if (trim($attachment->item->getDescription()) != ''): ?>
                    <br/>
                    <p><?php echo substr($attachment->item->getDescription(), 0, 100) . ((strlen($attachment->item->getDescription()) > 100)? '...': ''); ?></p>
                  <?php endif; ?>

                </td>
              <?php endif; ?>

            </tr>
				  <?php endforeach; ?>
        </table>
			<?php endif;?>
			</div>

			<div style="clear: both;">
				<img src="<?php echo $type_icon; ?>" border="0" style="vertical-align: top;"/>&nbsp;
				<?php echo $this->timestamp($this->item->getTimeValue());?>
			</div>

<?php elseif($this->step == 'more_link'): ?>
    <div align="right">
      <a style="text-decoration:underline;color:<?php echo $this->linkColor; ?>" href="/">
        <?php echo $this->translate('UPDATES_More actions...'); ?>
      </a>
    </div>
<?php endif; ?>