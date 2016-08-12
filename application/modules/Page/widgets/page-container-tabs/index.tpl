<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php
$page = Engine_Api::_()->core()->getSubject();
$host = (isset($_SERVER['HTTPS']) ? "https" : "http");
$page_url = $host . '://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_id' => $page->url), 'page_view', true);
$widgets_pseudonyms = array_flip($this->widgets_pseudonyms);
?>

<div class='tabs_alt tabs_parent'>
    <ul id='main_tabs'>
        <?php foreach ($this->tabs as $key => $tab): ?>
            <?php

            $class = array();
            $class[] = 'tab_' . $tab['id'];
            $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
            if ($this->activeTab == $tab['name'])
                $class[] = 'active';
            $class = join(' ', $class);
            ?>
            <?php if ($key < $this->max): ?>
                <li class="<?php echo $class ?>"><a
                        href="<?php echo $page_url . '/tab/' . strtolower($widgets_pseudonyms[$tab['name']]); ?>"><?php echo $this->translate($tab['title']) ?><?php if (!empty($tab['childCount'])): ?>
                            <span>(<?php echo $tab['childCount'] ?>)</span><?php endif; ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if (count($this->tabs) > $this->max): ?>
            <li class="tab_closed more_tab" onclick="moreTabSwitch($(this));">
                <div class="tab_pulldown_contents_wrapper">
                    <div class="tab_pulldown_contents">
                        <ul>
                            <?php foreach ($this->tabs as $key => $tab): ?>
                                <?php
                                $class = array();
                                $class[] = 'tab_' . $tab['id'];
                                $class[] = 'tab_' . trim(str_replace('generic_layout_container', '', $tab['containerClass']));
                                if ($this->activeTab == $tab['name']) $class[] = 'active';
                                $class = join(' ', array_filter($class));
                                ?>
                                <?php if ($key >= $this->max): ?>
                                    <li class="<?php echo $class ?>"
                                        onclick="go_to_page_url('<?php echo $page_url . '/tab/' . strtolower($widgets_pseudonyms[$tab['name']]); ?>')"><?php echo $this->translate($tab['title']) ?><?php if (!empty($tab['childCount'])): ?>
                                            <span> (<?php echo $tab['childCount'] ?>)</span><?php endif; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
            </li>
        <?php endif; ?>
    </ul>
</div>

<?php echo $this->childrenContent ?>


<script type="text/javascript">
    en4.core.runonce.add(function () {
        var go_to_page_url = window.go_to_page_url = function (href) {
            window.location.href = href;
        };
        var moreTabSwitch = window.moreTabSwitch = function (el) {
            el.toggleClass('tab_open');
            el.toggleClass('tab_closed');
        }
    });
</script>