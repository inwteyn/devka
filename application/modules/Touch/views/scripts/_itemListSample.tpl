<?php
$params = array(

  /* Anonymous functions { */

    // Required
  'function' => array(

    // Required function
    'item_photo' => function ($item, $view){
      // Function body
      return; // Some string
    },

    // Required function
    'item_title' => function ($item, $view){
      // Function body
      return; // Some string
    },

    // Required function
    'item_rate' => function ($item, $view){
      // Function body
      return; // Some string
    }
  ),
  /* } Anonymous functions  */
  // May be missed
  'filter_params' => array(
    'search'=>$this->form->getElement('search')->getValue(),
    'filter_default_value'=>$this->translate('TOUCH_Search Blogs'),
    'filterUrl'=>$this->url(array(), 'blog_general', true)
  ),

  /* Messages {*/
  // May be missed

  'lang_no_item_found' => $this->translate('Nobody has written a blog entry yet.'),
  'lang_no_search_results' => $this->translate('Nobody has written a blog entry with that criteria.'),
  'lang_create_item' => $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array('action' => 'create'), 'blog_general').'" class="touchajax">', '</a>'),
  /*} Messages */

  // Permissions {
  // May be missed
  'create' => $this->canCreate,
  // } Permissions

  // Required
  'paginator' => $this->paginator,

  'search' => $this->search
);
/*} Params */

// Render Partial
  echo $this->partial('_itemList.tpl', 'touch', $params);
?>


