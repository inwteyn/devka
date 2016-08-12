<?php echo $this->render('_page_options_menu.tpl'); ?>

<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_edit_tabs.tpl'); ?>
</div>

<div class='layout_middle'>
  <div class="page_get_started">
    <ul>
    <?php $index = 0; ?>

    <li>
        <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'edit', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank">
      <div class="get_started_main">
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>

        <div class="get_started_title">
          <?php echo $this->translate('PAGE_EDIT_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_EDIT_DESCRIPTION') ?>
        </div>
        <div class="get_started_link">

<!--            --><?php //echo $this->translate('Edit My Page') . ' >>'?>

        </div>
      </div>
        </a>
    </li>

    <li>  <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'privacy', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank" class="get_started_link_new">
      <div class="get_started_main">
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_PRIVACY_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_PRIVACY_DESCRIPTION') ?>
        </div>
        <div class="get_started_link">

<!--            --><?php //echo $this->translate('My Page\'s Privacy') . ' >>'?>

        </div>
      </div> </a>
    </li>

    <li> <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'edit-photo', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank">
      <div class="get_started_main">
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_PHOTO_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_PHOTO_DESCRIPTION') ?>
        </div>
        <div class="get_started_link">

<!--            --><?php //echo $this->translate('My Page\'s Photo') . ' >>'?>

        </div>
      </div> </a>
    </li>

    <?php if($this->isAllowLayout) :?>
    <li>  <a class="get_started_link_new"href="<?php echo $this->url(array('page' => $this->page->getIdentity()), 'page_editor', true)?>" target="_blank">
      <div class="get_started_main">
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_LAYOUT_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_LAYOUT_DESCRIPTION') ?>
        </div>
        <div class="get_started_link">

<!--            --><?php //echo $this->translate('My Page\'s Layout') . ' >>'?>

        </div>
      </div>  </a>
    </li>
      <?php endif;?>

    <li>   <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'manage-admins', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank">
        <div class="get_started_main">
          <span class="get_started_photo"><?php $index++; echo $index; ?></span>
          <div class="get_started_title">
            <?php echo $this->translate('PAGE_TEAM_TITLE'); ?>
          </div>
         <div class="get_started_description">
            <?php echo $this->translate('PAGE_TEAM_DESCRIPTION') ?>
          </div>
          <div class="get_started_link">

<!--              --><?php //echo $this->translate('My Page\'s Team') . ' >>'?>

          </div>
        </div> </a>
      </li>

    <?php if ($this->isAllowInvite) : ?>

    <li>   <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'invite'), 'page_team', true)?>" target="_blank">
      <div>
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_INVITE_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_INVITE_DESCRIPTION'); ?>
        </div>
        <div class="get_started_link">

<!--            --><?php //echo $this->translate('Invite') . ' >>'?>

        </div>
      </div> </a>
    </li>
      <?php endif; ?>

    <li> <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'promote'), 'page_team', true)?>" target="_blank">

        <div>
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_PROMOTE_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_PROMOTE_DESCRIPTION'); ?>
        </div>
        <div class="get_started_link">
<!--            --><?php //echo $this->translate('Promote') . ' >>'?>

        </div>
      </div></a>
    </li>


    <li>  <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'update'), 'page_team', true)?>" target="_blank">
      <div>
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_UPDATE_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_UPDATE_DESCRIPTION'); ?>
        </div>
        <div class="get_started_link">

<!--            --><?php //echo $this->translate('Seand Updates') . ' >>'?>

        </div>
      </div>  </a>
    </li>

    <?php if( $this->isAllowedBadge ) : ?>
      <li> <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'badges', 'page_id' => $this->page->getIdentity()), 'page_team', true)?>" target="_blank">
        <div class="get_started_main">
          <span class="get_started_photo"><?php $index++; echo $index; ?></span>
          <div class="get_started_title">
            <?php echo $this->translate('PAGE_BADGE_TITLE'); ?>
          </div>
          <div class="get_started_description">
            <?php echo $this->translate('PAGE_BADGE_DESCRIPTION') ?>
          </div>
          <div class="get_started_link">

<!--              --><?php //echo $this->translate('My Page\'s Badges') . ' >>'?>

          </div>
        </div></a>
      </li>
    <?php endif;?>

    <?php if ($this->isAllowStore && $this->page->getStorePrivacy()) : ?>
    <li>  <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'store'), 'page_team', true)?>" target="_blank">
      <div>
        <span class="get_started_photo"><?php $index++; echo $index; ?></span>
        <div class="get_started_title">
          <?php echo $this->translate('PAGE_STORE_TITLE'); ?>
        </div>
        <div class="get_started_description">
          <?php echo $this->translate('PAGE_STORE_DESCRIPTION'); ?>
        </div>
        <div class="get_started_link">

<!--            --><?php //echo $this->translate('My Store Product\'s') . ' >>'?>

        </div>
      </div> </a>
    </li>
    <?php endif; ?>

      <?php if ($this->isAllowPagecontact) : ?>
      <li> <a class="get_started_link_new"href="<?php echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'contact'), 'page_team', true)?>" target="_blank">
        <div>
          <span class="get_started_photo"><?php $index++; echo $index; ?></span>
          <div class="get_started_title">
            <?php echo $this->translate('PAGE_CONTACT_TITLE'); ?>
          </div>
          <div class="get_started_description">
            <?php echo $this->translate('PAGE_CONTACT_DESCRIPTION'); ?>
          </div>
          <div class="get_started_link">

<!--              --><?php //echo $this->translate('My Page\'s contact') . ' >>'?>

          </div>
        </div>  </a>
      </li>
        <?php endif; ?>

      <?php if ($this->isAllowPagefaq) : ?>
<!--      <li> <a class="get_started_link_new"href="<?php /*echo $this->url(array('action' => 'apps', 'page_id' => $this->page->getIdentity(), 'sub-menu' => 'faq'), 'page_team', true)*/?>" target="_blank">
        <div>
                <span class="get_started_photo"><?php /*$index++; echo $index; */?></span>
          <div class="get_started_title">
            <?php /*echo $this->translate('PAGE_FAQ_TITLE'); */?>
          </div>
          <div class="get_started_description">
            <?php /*echo $this->translate('PAGE_FAQ_DESCRIPTION'); */?>
          </div>
          <div class="get_started_link">

              <?php /*//echo $this->translate('My Page\'s FAQ >>')*/?>

          </div>
        </div></a>
      </li>-->
        <?php endif; ?>

      <li> <a class="get_started_link_new"href="<?php echo $this->url(array('page_id' => $this->page->getIdentity()), 'page_stat', true)?>" target="_blank">
        <div class="get_started_main">
          <div class="get_started_title">
            <span class="get_started_photo"><?php $index++; echo $index; ?></span>
            <?php echo $this->translate('PAGE_STATISTIC_TITLE'); ?>
          </div>
          <div class="get_started_description">
            <?php echo $this->translate('PAGE_STATISTIC_DESCRIPTION') ?>
          </div>
          <div class="get_started_link">

<!--              --><?php //echo $this->translate('My Page\'s Statistics') . ' >>'?>

          </div>
        </div></a>
      </li>

    </ul>
  </div>
</div>