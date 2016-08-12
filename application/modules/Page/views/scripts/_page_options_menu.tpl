<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _page_optoins_menu.tpl  11.11.11 16:44 TeaJay $
 * @author     Taalay
 */
$layout_page=false;
if(!$this->action){
    $p = Zend_Controller_Front::getInstance()->getRequest();
    if($p->getParam('module') == 'page' && $p->getParam('controller')=='editor'){
      $layout_page = true;
    }
}

?>



<style>
    body .layout_page_footer, body #global_content{
        clear: both;
    }
</style>




<div class="page_edit_title" ">

<nav role="navigation" class="he-page-dashboard-nav he-navbar">
    <div class="he-container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="he-navbar-header">
            <button data-target="#bs-example-navbar-collapse-9" data-toggle="collapse" class="he-navbar-toggle" type="button">
                <span class="he-sr-only">Toggle navigation</span>
                <span class="he-icon-bar"></span>
                <span class="he-icon-bar"></span>
                <span class="he-icon-bar"></span>
            </button>
            <a href="<?php echo $this->page->getHref() ?>" class="he-navbar-brand"><?php echo $this->page->getTitle(); ?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div id="bs-example-navbar-collapse-9" class="he-collapse he-navbar-collapse">
            <ul class="he-nav he-navbar-nav">
                <!--          Start new layout          -->

                <li class="sideNavItem <?php if ($this->action == 'get-started') echo 'selectedItem' ?>">
                    <a class="item clearfix" href="<?php echo $this->url(array('action' => 'get-started', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">
                        <div>
                            <!--            <span class="imgWrap">
                                          <i class="img get_started_icon"></i>
                                        </span>
                                                 --> <div class="linkWrap">
                                <?php echo $this->translate('Tour')?>
                            </div>
                        </div>
                    </a>
                </li>

                <li class="sideNavItem <?php if ($this->action == 'edit' || $this->action == 'edit-photo' ) echo 'selectedItem' ?>">
                    <a class="item clearfix" href="<?php echo $this->url(array('action' => 'edit', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">
                        <div>
                            <!--          <span class="imgWrap">
                                        <i class="img basic_info_icon"></i>
                                      </span>-->
                            <div class="linkWrap">
                                <?php echo $this->translate('Information')?>
                            </div>
                        </div>
                    </a>
                </li>




                <li id="apps" class="sideNavItem <?php if ($this->action == 'apps') echo 'selectedItem' ?>">
                    <a class="item clearfix" href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'update'), 'page_team', true)?>"  id="show_apps">
                        <div>
                            <!--          <span class="imgWrap">
                                        <i class="img edit_apps_icon"></i>
                                      </span>-->
                            <div class="linkWrap sub_menus">
                                <?php echo $this->translate('Apps')?>
                            </div>
                        </div>
                    </a>

                </li>


                <?php if(!$this->isAllowLayout){$this->isAllowLayout = $this->page->isAllowLayout();}
                if ($this->isAllowLayout) : ?>
                    <li class="sideNavItem <?php if($layout_page){echo 'selectedItem';}?>" id="layout">
                        <a class="item clearfix" href="<?php echo $this->url(array('page' => $this->page->getIdentity()), 'page_editor', true)?>">
                            <div>
                                <div class="linkWrap external">
                                    <?php echo $this->translate('Layout')?>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="sideNavItem <?php if ($this->action == 'privacy' || $this->action == 'manage-admins' || $this->action == 'style' ||  $this->action == 'badges' ||  $this->action == 'delete') echo 'selectedItem' ?>" id="privacy">
                    <a class="item clearfix" href="<?php echo $this->url(array('action' => 'privacy', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>">
                        <div>
                            <!--          <span class="imgWrap">
                                        <i class="img privacy_icon"></i>
                                      </span>-->
                            <div class="linkWrap">
                                <?php echo $this->translate('Settings')?>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="sideNavItem" id="statistic">
                    <a class="item clearfix" href="<?php echo $this->url(array('page_id' => $this->page->getIdentity()), 'page_stat', true)?>">
                        <div>
                            <!--   <span class="imgWrap">
                                 <i class="img view_stat_icon"></i>
                               </span>-->
                            <div class="linkWrap external">
                                <?php echo $this->translate('Statistics')?>
                            </div>
                        </div>
                    </a>
                </li>






            </ul>
            <ul class="he-nav he-navbar-nav he-navbar-right">
                <li id="pages_layoutbox_menu_createpage">
                    <a class="item clearfix" href="<?php echo$this->url(array(), 'page_create')?>">
                        <div>
                            <!--   <span class="imgWrap">
                                 <i class="img view_stat_icon"></i>
                               </span>-->
                            <div class="linkWrap external">
                                <?php echo $this->translate('Create Page')?>
                            </div>
                        </div>
                    </a>

                </li>

            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>


  </div>
  <div class="clr"></div>
  <?php if( $this->packageEnabled && $this->isDefaultPackage) :?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Your pages package is default package. Please change to another package to get more options');?>
    </span>
  </div>
  <?php endif;?>
<div class="clr"></div>
