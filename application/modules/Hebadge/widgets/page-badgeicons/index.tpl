<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 02.04.12 09:12 michael $
 * @author     Michael
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function (){
    $$('.hebadge_page_profile_icons a').each(function (item){
      Hebadge.elementClass(Hebadge.Tips, item, {'title': item.getParent('li').getElement('.item_info').get('html')});
    });
  });
</script>

<?php if ($this->paginator->getTotalItemCount()):?>
  <ul class="hebadge_page_profile_icons">
    <?php foreach ($this->paginator as $badge):?>
      <li>
        <a href="<?php echo $badge->getHref()?>">
          <?php echo $this->itemPhoto($badge, 'thumb.icon')?>
        </a>
        <div style="display: none;" class="item_info">
          <div class="item_title"><?php echo $badge->getTitle()?></div>
          <div class="item_description"><?php echo $badge->getDescription()?></div>
        </div>
      </li>
    <?php endforeach;?>
  </ul>
<?php endif;?>