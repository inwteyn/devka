<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2012-05-14 16:23 ratbek $
 * @author     Ratbek
 */
?>
<?php if ($this->step == 'thumb'): ?>

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;width:48px;height:48px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.icon'); ?>
  </a>

<?php elseif($this->step == 'details'): ?>

  <a href="<?php echo $this->item->getHref() ?>" style="font-weight: bold; color:<?php echo $this->linkColor?>; font-size: 12px;text-decoration: none">
    <font color="<?php echo $this->linkColor?>">
      <?php echo $this->item->getTitle(); ?>
    </font>
  </a>
  <div style="font-size: 10px">
    <?php echo $this->translate('UPDATES_Posted on ') . ' ' . date('d M  Y', strtotime($this->item->creation_date)) . ' '	. $this->translate('UPDATES_by')?>
    <a target="_blank" href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/page/'.$this->item->url; ?>" style="color:<?php echo $this->linkColor; ?>; text-decoration: none">
        <?php echo $this->item->url; ?>
    </a>
  </div>
  <div style="margin-top:5px;">
      <font size="2" style="font-size:11px; font-family: arial; color:<?php echo $this->fontColor; ?>">
        <?php echo Engine_String::substr(Engine_String::strip_tags($this->item->body), 0, 90);
              echo (Engine_String::strlen($this->item->body)>89)? "...":'';
        ?>
     </font>
  </div>

<?php elseif($this->step == 'more_link'): ?>

  <div align="right">
    <script type="text/javascript">
       $$('a#new_blogs_more_link').setStyle('display', 'none');
     </script>
    <a href="<?php echo $this->url(array('module'=>'blogs'), 'default', true); ?>" style="text-decoration:underline;color:<?php echo $this->linkColor; ?>">
      <?php echo $this->translate('UPDATES_More blogs...'); ?>
    </a>
  </div>
  
<?php endif; ?>