<?php if ($this->blogs->getTotalItemCount()){?>
<div id="widget_content">
  <div class="page_sub_navigation">
    <ul class="touch_sub_navigation">
      <li>
        <a href="<?php echo $this->url(array('action' => 'index', 'page_id' => $this->subject()->page_id), 'page_blog', true) ?>" class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("pageblog_Browse Blogs"); ?>
        </a>
      </li>

      <li>
        <a href="<?php echo $this->url(array('action' => 'manage', 'page_id' => $this->subject()->page_id), 'page_blog', true)?>" class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("pageblog_Manage Blogs"); ?>
        </a>
      </li>

      <li>
        <?php if ($this->can_create){?>
        <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject()->getIdentity()), 'page_blog', true)?>" class="sub_nav_item" onclick="Touch.navigation.subNavRequest($(this)); return false;">
          <?php echo $this->translate("pageblog_Compose New Blog Entry"); ?>
        </a>
        <?php } ?>
      </li>

    </ul>
  </div>
  <div style="clear: both; height: 8px;"></div>
  <div id="sub_navigation_loading"  style="display: none;">
    <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
  </div>

  <div id="sub_navigation_content" >
  <?php echo $this->paginationControl($this->blogs, null, array('pagination/page_filter.tpl', 'touch'),
  array(
  'search'=>$this->form_filter->getElement('search')->getValue(),
  'filter_default_value'=>$this->translate('TOUCH_Search Blogs'),
  'filterUrl'=>$this->url(array('module'=>'pageblog', 'controller'=>'index', 'action'=>'index', 'page_id'=>$this->subject()->page_id), 'page_blog', true),
)
); ?>
    <div id="filter_block">

<ul class="items">
    <?php foreach ($this->blogs as $blog): ?>
    <li>
      <div class='item_photo'>
        <?php echo $this->htmlLink($blog->getOwner()->getHref(), $this->itemPhoto($blog->getOwner(), 'thumb.icon'),
        array('class' => 'touchajax')) ?>
      </div>

        <div class="touch_box">
            <div class="blogs_title">
                <?php echo $this->htmlLink(array('route' => 'page_blog', 'action' => 'view',
                'blog_id' => $blog->getIdentity()), $this->string()->chunk(substr($blog->getTitle(), 0, 45), 10), array('onclick'=>'Touch.navigation.subNavRequest($(this)); return false;')) ?>
            </div>

            <div class="item_date" style="font-size: .8em; color: #999;">
              <?php echo $this->translate('Posted');?>
              <?php echo $this->timestamp(strtotime($blog->creation_date)) ?>
              <?php echo $this->translate('by');?>
              <?php echo $this->htmlLink($blog->getOwner()->getHref(), $blog->getOwner()->getTitle(), array('class' =>
              'touchajax')) ?>
            </div>

            <div class="prof_blogs_body">
                <?php echo $blog->body ?><br>
            </div>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php }else{ ?>
  <div id="sub_navigation_loading"  style="display: none;">
    <a class="loader"><?php echo $this->translate("Loading"); ?>...</a>
  </div>

  <div id="sub_navigation_content" >

    <div class="tip">
      <span>
        <?php echo $this->translate('Nobody has created a blog yet.');?>
        <?php if ($this->isAllowedPost){?>
          <?php echo $this->translate('Be the first to %1$spost%2$s one!', "<a href=".$this->url(array('action' => 'create', 'page_id' => $this->subject()->page_id), 'page_blog', true)." onclick = 'Touch.navigation.subNavRequest($(this)); return false;'>", '</a>'); ?>
        <?php }; ?>
      </span>
    </div>
  </div>
<?php } ?>
</div>
</div>