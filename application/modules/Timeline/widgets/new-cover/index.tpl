<?php
$this->headScript()->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/cover.js');

if($this->item_type == 'page') {
    $page = Engine_Api::_()->core()->getSubject();

    $host = (isset($_SERVER['HTTPS']) ? "https" : "http");
    $page_url = $host . '://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_id' => $page->url), 'page_view', true);
    $widgets_pseudonyms = $this->widgets_pseudonyms;
}
if(!$this->subject){
    $this->subject = $this->subject();
}
?>

<script type="text/javascript">

    document.tl_cover = new TimelineCover();
    document.tl_cover.setOptions({
        element_id: 'cover-container-img',
        save_button: 'tl-cover-save-button',
        edit_buttons: 'tl-cover-edit-group',
        loader_id: 'tl-cover-loader',
        is_allowed: true,
        cover_url: '<?php echo $this->url(array('action' => 'get', 'item_type' => $this->item_type, 'item_id' => $this->subject->getIdentity()), 'timeline_photo', true); ?>',
        position_url: '<?php echo $this->url(array('action' => 'position', 'type'=>'cover', 'item_type' => $this->item_type, 'item_id' => $this->subject->getIdentity()), 'timeline_photo', true); ?>',
        imgSrc: '<?php echo $this->coverPhoto['photoSrc']; ?>'
    });
    try {
        document.tl_cover.position = JSON.parse('<?php echo $this->coverPhoto['position']; ?>');
    } catch (e) {
        console.log(e);
    }

    en4.core.runonce.add(function () {
        document.tl_cover.init();
        document.tl_cover.options.cover_width = document.tl_cover.get().getParent().getWidth();
    });

</script>

<div class="timeline-profile-tabs">
<div class="profile-container he-thumbnail">
<div class="cover-container">

    <a href="<?php echo($this->coverPhoto['photoHref']); ?>" id="cover-container" class="cover">
        <img id="cover-container-img" src="<?php echo $this->coverPhoto['photoSrc']; ?>"/>
    </a>

    <?php if ($this->canEdit): ?>
        <div class="cover-edit he-button-group">
            <a id="tl-cover-save-button" class="he-btn wp_init" type="button" style="display: none;">
                Save Position
            </a>

            <div id="tl-cover-edit-group" class="he-btn-group" style="display: inline-block;">
                <a data-toggle="dropdown" class="he-btn he-dropdown-toggle wp_init" type="button">
                    <i class="hei hei-picture-o"> </i><?php echo $this->translate('TIMELINE_Edit Cover'); ?> <i
                        class="he-caret"></i>
                </a>
                <ul class="he-dropdown-menu">
                    <?php if (in_array($this->item_type, $this->allowFromAlbums)): ?>
                        <?php if ($this->isAlbumEnabled): ?>
                            <li><?php echo $this->htmlLink(array(
                                        'route' => 'timeline_photo',
                                        'item_type' => $this->item_type,
                                        'item_id' => $this->subject->getIdentity(),
                                        'type' => 'cover',
                                        'reset' => true
                                    ),
                                    $this->translate('TIMELINE_Choose from Photos...'),
                                    array(
                                        'class' => 'cover-albums smoothbox',
                                    )); ?>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li>
                        <?php echo $this->htmlLink(array(
                                'route' => 'timeline_photo',
                                'action' => 'upload',
                                'item_type' => $this->item_type,
                                'type' => 'cover',
                                'item_id' => $this->subject->getIdentity(),
                                'reset' => true
                            ),
                            $this->translate('TIMELINE_Upload Photo...'),
                            array(
                                'class' => 'cover-albums smoothbox',
                            )); ?>
                    </li>
                    <?php if (!$this->coverScreen): ?>
                        <li><?php echo $this->htmlLink(
                                'javascript:document.tl_cover.reposition.start()',
                                $this->translate('TIMELINE_Reposition...'),
                                array('class' => 'cover-reposition')); ?>
                        </li>
                        <li><?php echo $this->htmlLink(array(
                                    'route' => 'timeline_photo',
                                    'action' => 'remove',
                                    'type' => 'cover',
                                    'item_type' => $this->item_type,
                                    'item_id' => $this->subject->getIdentity(),
                                ),
                                $this->translate('TIMELINE_Remove...'), array(
                                    'class' => 'cover-remove smoothbox')); ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif;
    if ($this->subject->getPhotoUrl()) {
        $profile_photo = $this->subject->getPhotoUrl();
    } else {
        $profile_photo = 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
    }

    ?>

    <a href="<?php echo $this->subject->getHref(); ?>" id="profile-photo"
       class="profile-photo he-thumbnail hetips_tips_active"
       style="background-image: url('<?php echo $profile_photo; ?>'); background-position: center center;">

        <?php if ($this->item_type == 'page'): ?>
            <?php if ($this->subject->featured) : ?>
                <div class="page_featured">
                    <span><?php echo $this->translate('Featured') ?></span>
                </div>
            <?php endif; ?>
            <?php if ($this->subject->sponsored) : ?>
                <div class="sponsored_page"><?php echo $this->translate('SPONSORED') ?></div>
            <?php endif; ?>
        <?php endif; ?>
    </a>

    <?php if (($this->item_type == 'page' || $this->item_type == 'user') && $this->canEdit): ?>
        <?php
        if ($this->item_type == 'page') {
            echo $this->htmlLink(array('route' => 'timeline_profile_photo', 'action' => 'edit-page-photo', 'format' => 'smoothbox', 'id' => $this->subject->getIdentity()), '<i class="hei hei-camera"> </i>' . $this->translate('TIMELINE_Edit Page Photo'), array(
                'class' => 'buttonlink smoothbox timeline-edit-photo-btn he-btn'));
        } else {
            echo $this->htmlLink(array('route' => 'timeline_profile_photo', 'action' => 'edit-user-photo', 'format' => 'smoothbox', 'id' => $this->subject->getIdentity()), '<i class="hei hei-camera"> </i>' . $this->translate('TIMELINE_Edit Profile Photo'), array(
                'class' => 'buttonlink smoothbox timeline-edit-photo-btn he-btn'));
        }
        ?>
    <?php endif; ?>


    <div class="title-n-options"> <!--Title And option Buttons Container-->
        <div class="title-n-category"> <!--Title & Category-->

            <h2><?php echo $this->subject->getTitle(); ?>
                <?php if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('headvancedmembers')){ ?>
                <?php if (($this->item_type == 'user') && Engine_Api::_()->headvancedmembers()->isActive($this->subject)){ ?>
                <img class="irc_mi" style="margin-bottom: -5px;cursor: pointer;"
                     src="<?php echo $this->advmembersBaseUrl() ?>application/modules/Headvancedmembers/externals/images/icon_verified.png"
                     width="24" height="24" title="verified"></h2>
            <?php }
            } ?>
            <?php if ($this->subjectAdditionalInfo): ?>
                <div class="subject-additional-info" id="subject-additional-info">
                    <?php echo $this->subjectAdditionalInfo; ?>
                </div>
            <?php endif; ?>

            <?php if ($this->item_type != 'page'): ?>
                <div class="cover-rate">
                    <?php echo $this->content()->renderWidget('rate.widget-rate'); ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="option-buttons">
            <?php if ($this->likeEnabled): ?>
                <?php if ($this->liked): ?>
                    <a class="he-btn he-btn-unlike"
                       data-like="0"
                       onclick="document.tl_core.likeSubject(this, '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->subject->getType(); ?>');">
                        <span class="he-glyphicon he-glyphicon-thumbs-down"></span>
                        <span id="tl-like-btn-text"><?php echo $this->translate('like_Unlike'); ?></span>
                    </a>
                <?php else: ?>
                    <a class="he-btn he-btn-like"
                       data-like="1"
                       onclick="document.tl_core.likeSubject(this, '<?php echo $this->subject->getIdentity(); ?>', '<?php echo $this->subject->getType(); ?>');">
                        <span class="he-glyphicon he-glyphicon-thumbs-up"></span>
                        <span id="tl-like-btn-text"><?php echo $this->translate('like_Like'); ?></span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <div class="he-btn-group">
                <?php if ($this->profile_options && $this->profile_options->count() > 0): $navigation = $this->profile_options->toArray(); ?>
                    <?php for ($i = 0; $i < 2; $i++): if (!array_key_exists($i, $navigation)) continue;
                        $nav = $navigation[$i];
                        $nav['params'] = ((array_key_exists('params', $nav)) && is_array($nav['params'])) ? $nav['params'] : array();
                        $onClikc = false;
                        $tmp_params = array();
                        if (isset($nav['module']) && !empty($nav['module']))
                            $tmp_params['module'] = $nav['module'];
                        if (isset($nav['controller']) && !empty($nav['controller']))
                            $tmp_params['controller'] = $nav['controller'];
                        if (isset($nav['action']) && !empty($nav['action']))
                            $tmp_params['action'] = $nav['action'];
                        if (isset($nav['route'])) {
                            $link = $this->url(array_merge($tmp_params, $nav['params']), $nav['route']);
                        } elseif (isset($nav['url'])) {
                            $link = $nav['url'];
                        } else {
                            $link = $nav['uri'];
                        }
                        $attrs = array('class' => 'he-btn ' . $nav['class']);
                        if (isset($nav['href'])) {
                            $link = $nav['href'];
                        }
                        if (isset($nav['onClick'])) {
                            $attrs['onclick'] = $nav['onClick'];
                        }

                        echo $this->htmlLink($link, $this->translate($nav['label']), $attrs);
                        ?>
                    <?php endfor; ?>
                    <?php if (count($navigation) > 2): ?>
                        <div class="he-btn-group">
                            <a type="button" class="he-btn he-dropdown-toggle" data-toggle="dropdown">
                                <i class="hei hei-ellipsis-h"></i>
                            </a>
                            <ul class="he-dropdown-menu">
                                <?php for ($i = 2; $i < count($navigation); $i++):
                                    $nav = $navigation[$i];
                                    $nav['params'] = ((array_key_exists('params', $nav)) && is_array($nav['params'])) ? $nav['params'] : array();
                                    ?>
                                    <li> <?php
                                        if (isset($nav['route'])) {
                                            $link = $this->url(array_merge($tmp_params, $nav['params']), $nav['route']);
                                        } elseif (isset($nav['url'])) {
                                            $link = $nav['url'];
                                        } else {
                                            $link = $nav['uri'];
                                        }
                                        echo $this->htmlLink($link, $this->translate($nav['label']), array('class' => $nav['class'])); ?>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($this->single_widget): ?>
    <div class="tab-buttons"><!--Tab Button Cantainer-->
        <ul class="he-nav he-nav-tabs"> <!--Tabs-->
            <li id='time_line_widget' class="page-single-widget <?php if(!$this->activeTab) echo 'he-active';?>" onclick="go_to_page_url('<?php echo $page_url; ?>')">
                <a href="">
                    <?php echo $this->translate('Timeline') ?>
                </a>
            </li>
            <?php $cnt = 0;
            $last = 0;
            $isPage = ($this->item_type == 'page') ? true : false;

            foreach ($this->tabs as $tab): ?>
                <?php


                    if (!$tab['childCount'] && !$isPage && $tab['childCount'] != -2) continue;

                $last++; ?>
                <?php $tabCnt = $tab['childCount']; ?>
                <?php if ($cnt < $this->menuitems): ?>
                    <li class="page-single-widget <?php if ($this->activeTab == $tab['name']) echo 'he-active'; ?>"
                        onclick="go_to_page_url('<?php echo $page_url . '/tab/' . strtolower($widgets_pseudonyms[$tab['name']]); ?>')">
                        <a href="">
                            <?php echo $this->translate($tab['title']) ?> <?php echo ($tabCnt && $tabCnt > 0) ? ' (' . $tabCnt . ')' : ''; ?>
                        </a>
                    </li>
                    <?php $cnt++; endif; ?>
            <?php endforeach; ?>

            <?php if ($last > $cnt): ?>
                <li class="he-dropdown">
                    <a data-toggle="dropdown" class="he-dropdown-toggle" id="myTabDrop1"
                       href="javascript://">More
                        <b class="he-caret"></b></a>
                    <ul aria-labelledby="myTabDrop1" role="menu" class="he-dropdown-menu">
                        <?php for ($i = $cnt; $i < count($this->tabs); $i++): ?>
                            <?php $tab = $this->tabs[$i]; ?>
                            <?php if (!$tab['childCount'] && !$isPage && $tab['childCount'] != -2) continue; ?>
                            <?php $tabCnt = $tab['childCount']; ?>
                            <li class="page-single-widget <?php if ($this->activeTab == $tab['name']) echo 'he-active'; ?>"
                                onclick="go_to_page_url('<?php echo $page_url . '/tab/' . strtolower($widgets_pseudonyms[$tab['name']]); ?>')">
                                <a data-toggle="tab" href="">
                                    <?php echo $this->translate($tab['title']) ?> <?php echo ($tabCnt && $tabCnt > 0) ? ' (' . $tabCnt . ')' : ''; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                var go_to_page_url = window.go_to_page_url = function (href) {
                    window.location.href = href;
                };
            });
        </script>
    </div>
<?php else: ?>
    <div class="tab-buttons"><!--Tab Button Cantainer-->
        <ul class="he-nav he-nav-tabs"> <!--Tabs-->
            <li id='time_line_widget' style="display: none">
                <a href="#tab-timeline">
                    <?php echo $this->translate('Timeline') ?>
                </a>
            </li>
            <?php $cnt = 0;
            $last = 0;
            $isPage = ($this->item_type == 'page') ? true : false;
            foreach ($this->tabs as $tab): ?>
                <?php if (!$tab['childCount'] && !$isPage && $tab['childCount'] != -2) continue;
                $last++; ?>
                <?php $tabCnt = $tab['childCount']; ?>
                <?php if ($cnt < $this->menuitems): ?>
                    <li <?php if ($cnt == 0) {
                        echo 'class="he-active"';
                    } ?>>
                        <a id="tab-<?php echo $tab['id']; ?>"
                           href="#tab-<?php echo $tab['id']; ?>" <?php if ($cnt == 0) {
                            echo 'class="he-active"';
                            echo 'id="timeLine-active"';
                        } else {
                            echo 'class="tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass'])) . '"';
                        } ?>>
                            <?php echo $this->translate($tab['title']) ?> <?php echo ($tabCnt && $tabCnt > 0) ? ' (' . $tabCnt . ')' : ''; ?>
                        </a>
                    </li>
                    <?php $cnt++; endif; ?>
            <?php endforeach; ?>

            <?php if ($last > $cnt): ?>
                <li class="he-dropdown">
                    <a data-toggle="dropdown" class="he-dropdown-toggle" id="myTabDrop1"
                       href="javascript://">More
                        <b class="he-caret"></b></a>
                    <ul aria-labelledby="myTabDrop1" role="menu" class="he-dropdown-menu">
                        <?php for ($i = $cnt; $i < count($this->tabs); $i++): ?>
                            <?php $tab = $this->tabs[$i]; ?>
                            <?php if (!$tab['childCount'] && !$isPage && $tab['childCount'] != -2) continue; ?>
                            <?php $tabCnt = $tab['childCount']; ?>
                            <li><a data-toggle="tab"
                                   tabindex="-1" <?php echo 'class="tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass'])) . '"'; ?>
                                   href="#tab-<?php echo $tab['id']; ?>">
                                    <?php echo $this->translate($tab['title']) ?> <?php echo ($tabCnt && $tabCnt > 0) ? ' (' . $tabCnt . ')' : ''; ?>
                                </a></li>
                        <?php endfor; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>
</div>

<?php if ($this->single_widget): ?>

    <div class="he-tab-content">
        <?php foreach ($this->tabs as $tab): if ($this->activeTab == $tab['name']): ?>
            <div id="#tab-<?php echo $tab['id']; ?>" class="he-tab-pane" style="display: block">
                <div>
                    <?php echo $tab['content']; ?>
                </div>
            </div>
        <?php endif; endforeach; ?>
        <div id="#tab-timeline" class="he-tab-pane he-row he-show-grid page-single-content <?php if($this->activeTab) echo 'hide-feed-for-widget';?>">

        </div>
    </div>

<?php else: ?>

    <div class="he-tab-content">
        <?php $cnt = 0;
        foreach ($this->tabs as $tab): ?>
            <div id="#tab-<?php echo $tab['id']; ?>"
                 class="he-tab-pane <?php if ($cnt == 0) echo ' active_tabInTimeLine' ?>"
                <?php if ($cnt == 0) echo 'style="display:block" '; else echo 'style="display:none"'; ?> >
                <div>
                    <?php echo $tab['content']; ?>
                </div>
            </div>
            <?php $cnt++; endforeach; ?>
        <div id="#tab-timeline" class="he-tab-pane he-row he-show-grid">

        </div>
    </div>

<?php endif; ?>

</div>