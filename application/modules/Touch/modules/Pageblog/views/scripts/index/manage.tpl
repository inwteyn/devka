<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: mine.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php if( count($this->navigation) > 0 ): ?>

       <?php echo $this->paginationControl($this->paginator, null, array('pagination/page_filter.tpl', 'touch'),
          array(
          'search'=>$this->form_filter->getElement('search')->getValue(),
					'filter_default_value'=>$this->translate('TOUCH_Search Blogs'),
					'filterUrl'=>$this->url(array('module'=>'pageblog', 'controller'=>'index', 'action'=>'index', 'page_id'=>$this->subject()->page_id), 'page_blog', true),
				)
); ?>
            <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
         <div id="filter_block">

            <ul class="items">
                <?php foreach( $this->paginator as $blog ): ?>
                <li>
                  <div class='item_photo'>
                    <?php echo $this->htmlLink($blog->getOwner()->getHref(), $this->itemPhoto($blog->getOwner(), 'thumb.icon'),
                    array('class' => 'touchajax')) ?>
                  </div>

                    <div class="item_body">
                        <div class="item_title">
                            <?php echo $this->htmlLink(array('route' => 'page_blog', 'action' => 'view', 'blog_id' =>
                            $blog->getIdentity()), $this->string()->chunk(Engine_String::substr($blog->getTitle(), 0, 45), 10), array('onclick'=>'Touch.navigation.subNavRequest($(this)); return false;')) ?>
                        </div>
                        <div class="item_date">
                          <?php echo $this->translate('Posted');?>
                          <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
                          <?php echo $this->translate('by');?>
                          <?php echo $this->htmlLink($blog->getOwner()->getHref(), $blog->getOwner()->getTitle(), array('class' =>
                          'touchajax')) ?>
                        </div>
                      <div class="item_options">
                          <?php echo $this->htmlLink($this->url(array('action' => 'delete', 'blog_id' =>
                          $blog->getIdentity()), 'page_blog'), $this->translate("Delete"), array('class' =>
                          'smoothbox')); ?>
                        -
                          <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'blog_id' =>
                          $blog->getIdentity()), 'page_blog'), $this->translate("Edit")); ?>
                      </div>


                        <?php echo $this->touchSubstr(Engine_String::strip_tags($blog->body))?>

                    </div>
                </li>

                <?php endforeach;?>
            </ul>
         </div>
            <?php else: ?>
            <div class="tip">
                  <span>
                    <?php echo $this->translate('Nobody has created a blog yet.');?>
                  </span>
            </div>
            <?php endif; ?>
<?php endif; ?>