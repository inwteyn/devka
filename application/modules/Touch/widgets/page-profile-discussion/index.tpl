<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php if (count($this->paginator)):?>

<ul class="items">
<?php if ($this->can_create):?>
        <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject()->page_id), 'page_discussion', true)?>"
           class="sub_menu touchajax">
            <?php echo $this->translate('PAGEDISCUSSION_CREATE')?>
        </a>

<div style="height: 8px;"></div>

<?php endif; ?>
    <?php foreach ($this->paginator as $topic):?>
    <li>
        <div class="item_body">

            <a href="<?php echo $this->url(array('action' => 'topic', 'topic_id' => $topic->getIdentity()), 'page_discussion', true)?>">

                <?php if ($topic->sticky):?>
                <?php echo $this->htmlImage($this->baseUrl() .
                '/application/modules/Pagediscussion/externals/images/stick.png')?>
                <?php endif;?>
                <?php if ($topic->closed):?>
                <?php echo $this->htmlImage($this->baseUrl() .
                '/application/modules/Pagediscussion/externals/images/close.png')?>
                <?php endif;?>

                <?php echo $topic->getTitle()?>
            </a>


            <div class="item_date">

                <?php echo $this->locale()->toNumber($topic->getCountPost())?>
                <?php echo $this->translate(array('reply', 'replies', $topic->getCountPost())) ?>
                /
                <?php echo $this->timestamp(strtotime($topic->modified_date)) ?>
            </div>

            <?php echo $this->touchSubstr($topic->getDescription())?>

        </div>
    </li>
    <?php endforeach;?>

</ul>

<?php if( $this->paginator->count() > 1 ): ?>
<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'touch')); ?>
<?php endif; ?>

<?php else:?>
<div id="sub_navigation_loading"  style="display: none;">
  <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
</div>

<div id="sub_navigation_content" >
<div class="tip">
        <span><?php echo $this->translate('TOUCH_WIDGET_NOITEMS')?>
        <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject()->page_id), 'page_discussion', true)?>"
           onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
            <?php echo "Be the first"; ?>
        </a>
        </span>
</div>
</div>
<?php endif?>