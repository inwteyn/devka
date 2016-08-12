<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Newsletter Updates
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: module.tpl 2012-05-14 16:25 ratbek $
 * @author     Ratbek
 */
?>
<?php if ($this->step == 'thumb'): ?>

  <a href="<?php echo $this->item->getHref(); ?>" style="border:1px solid #DDDDDD;display:inline-block;padding:4px;vertical-align:bottom;text-decoration:none;height:90px;overflow:hidden">
    <?php echo $this->itemPhoto($this->item, 'thumb.normal'); ?>
  </a>

<?php elseif($this->step == 'details'): ?>

  <div style="margin-top:4px;">
    <span style="font-weight:bold;font-size:12px">
    <a href="<?php echo $this->item->getHref()?> " style="text-decoration:none; color:<?php echo $this->linkColor ?>">
      <?php echo $this->item->getTitle(); ?>
    </a>
    </span> - <?php echo $this->translate(array('UPDATES_ %s song', '%s songs', count($this->item->getSongs())), $this->locale()->toNumber(count($this->item->getSongs()))); ?> <br/>

    <?php echo $this->translate('UPDATES_By'); ?>
    <a target="_blank" href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/page/'.$this->item->url; ?>" style="text-decoration:none; color:<?php echo $this->linkColor; ?>">
      <?php echo $this->item->url; ?>
    </a>
  </div>

<?php elseif($this->step == 'more_link'): ?>

  <div align="right">
    <script type="text/javascript">
       $$('a#new_playlists_more_link').setStyle('display', 'none');
     </script>
    <a href="<?php echo $this->url(array('module'=>'music'), 'default', true); ?>" style="text-decoration:underline;padding-top: 5px;color:<?php echo $this->linkColor; ?>">
      <?php echo $this->translate('UPDATES_More playlists...'); ?>
    </a>
  </div>
  
<?php endif; ?>