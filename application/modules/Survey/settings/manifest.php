<?php return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'survey',
    'version' => '4.1.5',
    'path' => 'application/modules/Survey',
    'title' => 'surveys',
    'description' => 'surveys Plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => array(
      'title' => 'surveys',
      'description' => 'surveys Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'callback' => array(
      'path' => 'application/modules/Survey/settings/install.php',
      'class' => 'Survey_Installer',
    ),
   'actions' => array(
       'preinstall',
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable'
     ),
    'directories' => array(
      'application/modules/Survey',
    ),
    'files' => array(
      'application/languages/en/survey.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Survey_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Survey_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'surveys',
    'survey_result'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    'survey_browse' => array(
      'route' => 'surveys/:page/*',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'index',
        'page' => 1
      )
    ),
    'survey_manage' => array(
      'route' => 'surveys/manage/:page',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'manage',
        'page' => '1'
      )
    ),
    'survey_view' => array(
      'route' => 'surveys/:survey_id/:slug',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'survey_id' => '\d+'
      )
    ),
    // User
    
    'survey_create' => array(
      'route' => 'surveys/create',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'create'
      )
    ),
    'survey_specific' => array(
      'route' => 'surveys/:action/:survey_id/*',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(edit|delete|create-result|create-question|publish|take)',
        'survey_id' => '\d+',
      )
    ),
    'survey_delete_result' => array(
      'route' => 'survey/delete-result/:survey_id/:result_id',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'delete-result'
      )
    ),
    'survey_edit_result' => array(
      'route' => 'survey/edit-result/:survey_id/:result_id',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'edit-result'
      )
    ),
    'survey_delete_question' => array(
      'route' => 'survey/delete-question/:survey_id/:question_id',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'delete-question'
      )
    ),
    'survey_edit_question' => array(
      'route' => 'survey/edit-question/:survey_id/:question_id',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'index',
        'action' => 'edit-question'
      )
    ),
    'survey_admin_manage_level' => array(
      'route' => 'admin/survey/level_id/:level_id',
      'defaults' => array(
        'module' => 'survey',
        'controller' => 'admin-level',
        'action' => 'index'
      ),
      'reqs' => array(
        'level_id' => '\d+'
      )
    ),
  ),
);