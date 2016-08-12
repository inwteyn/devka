<?php return array(
    'package' =>
        array(
            'type' => 'module',
            'name' => 'heemoticon',
            'version' => '4.0.2p2',
            'path' => 'application/modules/Heemoticon',
            'title' => 'Emoticon extension for Wall Plugin',
            'description' => 'Emoticon extension for Wall Plugin',
            'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
          'dependencies' => array(
            array(
              'type' => 'module',
              'name' => 'core',
              'minVersion' => '4.1.7',
            ),
            array(
              'type' => 'module',
              'name' => 'hecore',
              'minVersion' => '4.2.3',
            ),
            array(
              'type' => 'module',
              'name' => 'wall',
              'minVersion' => '4.3.1p6',
            ),
          ),
          'callback' =>
            array(
              'class' => 'Heemoticon_Installer',
              'path' => 'application/modules/Heemoticon/settings/install.php',
            ),
            'actions' =>
                array(
                    0 => 'install',
                    1 => 'upgrade',
                    2 => 'refresh',
                    3 => 'enable',
                    4 => 'disable',
                ),
            'directories' =>
                array(
                    0 => 'application/modules/Heemoticon',
                    1 => 'public/stickers',
                ),
            'files' =>
                array(
                    0 => 'application/languages/en/heemoticon.csv',
                ),
        ),
    'items' => array(
        'sticker',
        'collection',
    ),
    'routes' => array(
        'heemoticon_admin_index' => array(
            'route' => 'admin/heemoticon/index/:action/:collection_id/*',
            'defaults' => array(
                'module' => 'heemoticon',
                'controller' => 'admin-index',
                'action' => 'collections',
                'collection_id' => 0
            ),
            'reqs' => array(
                'collection_id' => '\d+'
            )
        ),
        'heemoticon_admin_stickers' => array(
            'route' => 'upload-stickers/:action',
            'defaults' => array(
                'module' => 'heemoticon',
                'controller' => 'admin-index',
                'action' => 'upload-stickers'
            )
        )
    )
); ?>