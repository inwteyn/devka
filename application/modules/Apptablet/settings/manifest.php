<?php return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'apptablet',
        'version' => '4.2.2p6',
        'path' => 'application/modules/Apptablet',
        'title' => 'Touch-Tablet',
        'description' => 'Tablet Extension',
        'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
        'meta' =>
        array(
            'title' => 'Touch-Tablet',
            'description' => 'Tablet Extension',
            'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
        ),
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'core',
                'minVersion' => '4.1.8',
            ),
            array(
                'type' => 'module',
                'name' => 'hecore',
                'minVersion' => '4.2.0p5',
            ),
            array(
                'type' => 'module',
                'name' => 'apptouch',
                'minVersion' => '4.2.6',
            ),
        ),

        'callback' =>
        array(
            'path' => 'application/modules/Apptablet/settings/install.php',
            'class' => 'Apptablet_Installer',
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
            0 => 'application/modules/Apptablet',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/apptablet.csv',
        ),
    ),
); ?>