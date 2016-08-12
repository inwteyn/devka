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

<?php if( count($this->navigation) > 0 ): ?>
          <?php echo $this->paginationControl($this->paginator, null, array('pagination/page_filter.tpl', 'touch'),
          array(
					'search'=>$this->form_filter->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Albums'),
					'filterUrl'=>$this->url(array('module'=>'pagealbum', 'controller'=>'index', 'action'=>'index', 'page_id'=>$this->subject()->page_id), 'page_album', true),
				)
); ?>
     <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
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
                            <?php echo $this->htmlLink(array('route' => 'page_album', 'action' => 'view', 'album_id' =>
                            $album->getIdentity()), $this->string()->chunk(Engine_String::substr($album->getTitle(), 0, 45), 10), array('onclick'=>'Touch.navigation.subNavRequest($(this)); return false;')) ?>
                        </div>
                        <div class="item_date">
                          <?php echo $this->translate('Posted');?>
                          <?php echo $this->timestamp(strtotime($album->creation_date)) ?>
                          <?php echo $this->translate('by');?>
                          <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' =>
                          'touchajax')) ?>
                            -
                            <?php echo $this->translate(array('%s photo', '%s photos',
                            $album->count()),$this->locale()->toNumber($album->count())) ?>
                        </div>
                    </div>
                </li>
     <?php endforeach;?>
            </ul>
        </div>
            <?php else: ?>
            <div class="tip">
                  <span>
                    <?php echo $this->translate('Nobody has created an album yet.');?>
                  </span>
            </div>
            <?php endif; ?>
<?php endif; ?>