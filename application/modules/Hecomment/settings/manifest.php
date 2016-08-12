<?php return array(
    'package' =>
        array(
            'type' => 'module',
            'name' => 'hecomment',
            'version' => '4.0.0p2',
            'path' => 'application/modules/Hecomment',
            'title' => 'HE-Comment',
            'description' => 'Comment',
            'author' => 'Hire-Experts',
            'callback' =>
                array(
                    'class' => 'Hecomment_Installer',
                    'path' => 'application/modules/Hecomment/settings/install.php',
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
                    0 => 'application/modules/Hecomment',
                ),
            'files' =>
                array(
                    0 => 'application/languages/en/hecomment.csv',
                ),
            'dependencies' => array(
                array(
                    'type' => 'module',
                    'name' => 'core',
                    'minVersion' => '4.1.7',
                ),
                array(
                    'type' => 'module',
                    'name' => 'hecore',
                    'minVersion' => '4.2.4',
                ),
                array(
                    'type' => 'module',
                    'name' => 'wall',
                    'minVersion' => '4.3.3',
                ),
            ),
        ),

); ?>