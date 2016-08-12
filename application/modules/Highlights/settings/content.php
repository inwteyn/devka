<?php
/***/
return array(
  array(
    'title' => 'HIGHLIGHT_Highlighted Members',
    'description' => 'Displays highlighted members',
    'category' => 'Highlights',
    'type' => 'widget',
    'name' => 'highlights.highlight-horizontal',
    'isPaginated' => false,
    'defaultParams' => array(
      'title' => 'HIGHLIGHT_Highlighted Members',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'HIGHLIGHT_Highlighted Members sidebar',
    'description' => 'Displays highlighted members sidebar',
    'category' => 'Highlights',
    'type' => 'widget',
    'name' => 'highlights.highlight-vertical',
    'isPaginated' => false,
    'defaultParams' => array(
      'title' => 'HIGHLIGHT_Highlighted Members sidebar',
    ),
    'adminForm' => 'Highlights_Form_Admin_Widget_Vertical',
    'requirements' => array(
      'no-subject',
    ),
  )
);