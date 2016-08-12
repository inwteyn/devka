
<?php
/**111111111
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
        
<?php if ($this->paginator->getTotalItemCount()){?>
<div id="widget_content">
  <div class="page_sub_navigation">
    <ul class="touch_sub_navigation">
      <li>
        <a href="<?php echo $this->url(array('action' => 'index', 'page_id' => $this->subject()->page_id), 'page_album', true) ?>"
      class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("Browse Albums")?>
        </a>
      </li>
      <li>
        <a href="<?php echo $this->url(array('action' => 'mine', 'page_id' => $this->subject()->page_id), 'page_album', true)?>"
      class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("My Albums"); ?>
        </a>
      </li>
      <?php if ($this->can_create){?>
      <li>
        <a href="<?php echo $this->url(array('action' => 'upload', 'page_id' => $this->subject()->page_id), 'page_album', true)?>"
      class="sub_nav_item " onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("Add Photos"); ?>
        </a>
      </li>
      <?php }; ?>
    </ul>
  </div>
  <div style="clear: both; height: 8px;"></div>

  <div id="sub_navigation_loading"  style="display: none;">
    <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
  </div>

  <div id="sub_navigation_content" >
  <?php echo $this->paginationControl($this->paginator, null, array('pagination/page_filter.tpl', 'touch'),
  array(
  'search'=>$this->form_filter->getElement('search')->getValue(),
  'filter_default_value'=>$this->translate('TOUCH_Search Albums'),
  'filterUrl'=>$this->url(array('module'=>'pagealbum', 'controller'=>'index', 'action'=>'index', 'page_id'=>$this->subject()->page_id), 'page_album', true),
)
); ?>
    <div id="filter_block">
      <ul class="items">
        <?php foreach( $this->paginator as $album ): ?>
          <li>
            <div class="item_photo">
              <a href="<?php echo $this->url(array('route' => 'page_album', 'action' => 'view', 'album_id' =>
                            $album->getIdentity()), 'page_album', true); ?>" onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
                <img src="<?php echo $album->getPhotoUrl('thumb.normal'); ?>" width="60px"/>
              </a>
            </div>
          <div class="item_body">
            <div class="item_title">
              <?php echo $this->htmlLink(array('route' => 'page_album', 'action' => 'view', 'album_id' => $album->getIdentity()), $this->string()->chunk(Engine_String::substr($album->getTitle(), 0, 45), 10), array('onclick'=>'Touch.navigation.subNavRequest($(this)); return false;')) ?>
            </div>
            <div class="item_date">
              <?php echo $this->translate('Posted');?>
              <?php echo $this->timestamp(strtotime($album->creation_date)) ?>
              <?php echo $this->translate('By');?>
              <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
              -
              <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
            </div>
          </div>
          </li>
        <?php endforeach;?>
      </ul>
    </div>
  </div>

    <?php }else{?>
  <div id="sub_navigation_loading"  style="display: none;">
    <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
  </div>

  <div id="sub_navigation_content" >

      <div class="tip">
        <span><?php echo $this->translate('TOUCH_WIDGET_NOITEMS')?>
        <a href="<?php echo $this->url(array('action' => 'upload', 'page_id' => $this->subject()->page_id), 'page_album', true)?>"
            onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>
                <?php echo $this->translate("Nobody has created an album yet."); ?>
        </a>
        </span>
      </div>
  </div>

	  <?php };?>
</div>
