<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _activeList.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
$list_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.disabled', ''));
?>

<!--<a href="javascript:void(0);" class="wall-list-button wall-button wall_blurlink">

  <?php /*if ($this->list_params['mode'] == 'recent'){ */?>
    <span class="wall_icon wall-most-recent">&nbsp;</span>
    <span class="wall_text"><?php /*echo $this->translate('WALL_RECENT');*/?></span>
  <?php /*} else if ($this->list_params['mode'] == 'type'){*/?>
    <span class="wall_icon wall-type-<?php /*echo $this->list_params['type']*/?>">&nbsp;</span>
    <span class="wall_text"><?php /*echo $this->translate('WALL_TYPE_' . strtoupper($this->list_params['type']))*/?></span>
  <?php /*} else if ($this->list_params['mode'] == 'list'){ */?>
    <span class="wall_icon wall-type-list">&nbsp;</span>
    <span class="wall_text">
    <?php
/*      foreach ($this->lists as $list){
        if ($list->list_id != $this->list_params['list_id']){
          continue ;
        }
        echo $list->label;
      }
    */?>
    </span>
  <?php /*} */?>

</a>
-->
<div class="fake-lists">
    <span class="wall_loader"></span>
    <div class="wall-element-external">
        <ul class="wall_types" id="wall_types">
            <li class="<?php if ($this->list_params['mode'] == 'recent'):?>active<?php endif;?>">
                <a href="javascript:void(0);" rev="recent" class="item <?php if ($this->list_params['mode'] == 'recent'):?>active<?php endif;?> wall_blurlink">
                    <?php echo $this->translate('WALL_RECENT'  )?>
                </a>
            </li>
            <?php if (count($this->types)):
                $i = 1;

                ?>

                <?php foreach ($this->types as $type):
                if (@in_array($type, $list_disabled)){ continue ; }
                if($i>=7){ break; }
                ?>
                <li>
                    <a href="javascript:void(0);" rev="type-<?php echo $type?>" class="item <?php if ($this->list_params['mode'] == 'type' && $type == $this->list_params['type']):?>is_active<?php endif;?> wall_blurlink">

                        <?php echo $this->translate('WALL_TYPE_' . strtoupper($type) )?>
                    </a>
                </li>
                <?php
                $i++;
            endforeach ;?>


            <?php endif;?>

            <li id = "wall_menu_more"><a href="javascript:void(0);" class="wall-list-button  wall_blurlink"><span class="wall_text" id="wall_more">More<i class="hei hei-chevron-down"></i> </span></a></li>
        </ul>
    </div>
</div>