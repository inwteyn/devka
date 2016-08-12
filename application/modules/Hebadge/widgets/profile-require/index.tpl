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

<div class="hebadge_widget_description">
  <?php echo $this->translate('HEBADGE_REQUIRE_DESCRIPTION');?>
</div>

<?php if (!empty($this->require)):?>
  <div class="item_require">
    <ul>
      <?php
      $counter = 1;
      foreach ($this->require as $item):?>

        <?php
          $require = Engine_Api::_()->hebadge()->getRequire($item->type);
          if (empty($require)){
            continue ;
          }
          $link = 'javascript:void(0);';
          if (!empty($require['require_link'])){
            $link = $require['require_link'];
          }
        ?>
        <li class="<?php if (!$this->member && in_array($item->getIdentity(), $this->require_complete)):?>complete<?php endif;?>">
          <div class="item_title">
            <span class="item_counter"><?php echo $counter;?></span>
            <?php echo $this->translate('HEBADGE_REQUIRE_' . strtoupper($item->type), $item->params);?>
          </div>
        </li>
      <?php
        $counter++;
      endforeach;
      ?>
    </ul>
  </div>
<?php endif;?>