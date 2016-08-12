<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: search.tpl 7443 2010-09-22 07:25:41Z john $
 * @author     John
 */
?>

<?php
  // Add params
?>
<style type="text/css">
.paginationControl > li > span{
	-moz-border-radius: 3px 3px 3px 3px;
	display: block;
	font-size: 1em;
	font-weight: bold;
	padding: 0.3em 0.6em;
}
</style>

<?php if( $this->pageCount > 1 ): ?>
  <div class="pages">
    <ul class="paginationControl" id="<?php echo $this->paginator_name ?>_list">
        <li id='<?php echo $this->paginator_name ?>_previous'>
					<a href="javascript://" onclick="<?php echo $this->paginator_name ?>.previous('<?php echo $this->paginator_name ?>')" <?php if( !isset($this->previous) ): ?> style="display:none"<?php endif; ?>><?php echo $this->translate('&#171; Previous'); ?></a>
					<span <?php if( isset($this->previous) ): ?> style="display:none"<?php endif; ?>><?php echo $this->translate('&#171; Previous'); ?></span>
				</li>
      <?php foreach ($this->pagesInRange as $page): ?>
        <li <?php if ($page == $this->current): ?> class="selected" <?php endif; ?> id="<?php echo $this->paginator_name ?>_<?php echo $page;?>">
				  <a href="javascript://" onclick="<?php echo $this->paginator_name ?>.page('<?php echo $this->paginator_name ?>', <?php echo $page; ?>)"><?php echo $page ?></a>
        </li>
      <?php endforeach; ?>
        <li id='<?php echo $this->paginator_name ?>_next'>
					<a href="javascript://" onclick="<?php echo $this->paginator_name ?>.next('<?php echo $this->paginator_name ?>')" id='<?php echo $this->paginator_name ?>_next_link' <?php if (!isset($this->next)): ?> style="display:none" <?php endif; ?>><?php echo $this->translate('Next &#187;') ?></a>
					<span id='<?php echo $this->paginator_name ?>_next_simple' <?php if( isset($this->next) ): ?> style="display:none"<?php endif; ?>><?php echo $this->translate('Next &#187;') ?></span>
        </li>
    </ul>
  </div>
<?php endif; ?>

