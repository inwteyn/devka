<?php

class Register_Api_Core extends Core_Api_Abstract
{
  public function addUser($userInfo)
  {
    $usersTable = Engine_Api::_()->getItemTable('user');

    $user = $usersTable->createRow();
    $user->setFromArray($userInfo);
    $user->save();

    Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');

    $photo = $this->randomPhoto();
    $user->setPhoto($photo);

    $values = Engine_Api::_()->fields()->getFieldsValues($user);

    $userName = explode(' ', $userInfo['displayname']);

    $fields = array(
      array('field_id' => 1, 'value' => 1),
      array('field_id' => 3, 'value' => ($userName[0] ? $userName[0] : 'John')),
      array('field_id' => 4, 'value' => ($userName[1] ? $userName[1] : 'Doe')),
      array('field_id' => 5, 'value' => 2),
      array('field_id' => 6, 'value' => rand(1960, 1990) . '-' . rand(1, 12) . '-' . rand(1, 28))
    );

    foreach ($fields as $field) {
      $valueRow = $values->createRow();

      $valueRow->field_id = $field['field_id'];
      $valueRow->item_id = $user->getIdentity();
      $valueRow->value = $field['value'];

      $valueRow->save();
    }


    Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'signup');

    return $user->getIdentity();
  }

  public function processAddUser($count = 10)
  {
    $moduleDir = Engine_Api::_()->getModuleBootstrap('register')->getModulePath();
    $randomUsers = array();
    
    include_once $moduleDir . DS . 'Model' . DS . 'Users.php';

    $usersTable = Engine_Api::_()->getItemTable('user');

    $select = $usersTable->select()
      ->setIntegrityCheck(false)
      ->from($usersTable->info('name'), new Zend_Db_Expr('MAX(user_id)'));

    $newIndex = $usersTable->getAdapter()->fetchOne($select) + 1;
    $levelId = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel()->level_id;

    $newUserIds = array();
    for ($userId = $newIndex; $userId < $newIndex + $count; $userId++) {
      $displayName = $randomUsers[rand(0, 40963)];
      
      $userInfo = array(
        'email' => "test$userId@mail.com",
        'username' => "test$userId",
        'displayname' => ($displayName) ? ucwords(strtolower($displayName)) : 'John Doe',
        'photo_id' => 0,
        'password' => '123456',
        'salt' => (string) rand(1000000, 9999999),
        'locale' => 'auto',
        'language' => 'en',
        'timezone' => 'US/Pacific',
        'verified' => 1,
        'creation_date' => date('Y-m-d H:i:s', time()),
        'creation_ip' => '2130706433',
        'modified_date' => date('Y-m-d H:i:s', time()),
        'level_id' => $levelId,
      );

      $result = $this->addUser($userInfo);

      if ($result) {
        $newUserIds[] = $result;
      }
    }

    return $newUserIds;
  }

  public function randomPhoto()
  {
    $moduleDir = Engine_Api::_()->getModuleBootstrap('register')->getModulePath();
    $photoDir = $moduleDir . DS . 'externals' . DS . 'images' . DS . 'Photos';

    $photoList = array();

    if ($dh = opendir($photoDir)) {
      while (($file = readdir($dh)) !== false) {
        if ($file == '.' || $file == '..') {
          continue;
        }
        
        $photoList[] = $photoDir . DS .  $file;
      }
      closedir($dh);
    }

    shuffle($photoList);

    $photoInfo = array('tmp_name' => $photoList[0]);

    return $photoInfo;
  }

  public function addBlogs($count = 10)
  {
    $moduleDir = Engine_Api::_()->getModuleBootstrap('register')->getModulePath();
    $randomBlogs = array();

    include_once $moduleDir . DS . 'Model' . DS . 'Blogs.php';

    $usersTable = Engine_Api::_()->getItemTable('user');
    $usersSel = $usersTable->select();
    $usersSel->from($usersTable->info('name'), array('user_id'))
      ->limit($count);
    $user_ids = $usersTable->getAdapter()->fetchCol($usersSel);

    $tmp_blogs = $randomBlogs;
    $tmp_user_ids = $user_ids;
    shuffle($tmp_blogs);
    shuffle($tmp_user_ids);
    $index = 0;
    $blog_ids = array();
    while ($index < $count) {
      $blog_info = array_shift($tmp_blogs);
      $user_id = array_shift($tmp_user_ids);

      unset($blog_info['blog_id']);
      unset($blog_info['photo_id']);
      unset($blog_info['view_count']);
      unset($blog_info['comment_count']);

      $blog_info['owner_id'] = $user_id;
      $blog_info['tags'] = '';
      $privacy = $this->getBlogPrivacy();
      $blog_info += $privacy;

      $blog_ids[] = $this->addBlog($blog_info);

      if (count($tmp_blogs) == 0) {
        $tmp_blogs = $randomBlogs;
        shuffle($tmp_blogs);
      }
      if (count($tmp_user_ids) == 0) {
        $tmp_user_ids = $user_ids;
        shuffle($tmp_user_ids);
      }

      $index++;
    }



    $blogInfo = array();
    $this->addBlog($blogInfo);

    return $blog_ids;
  }

  public function addBlog($values)
  {
    if (!$values) {
      return 0;
    }
    // Process
    $table = Engine_Api::_()->getItemTable('blog');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      // Create blog
      $viewer = Engine_Api::_()->user()->getUser($values['owner_id']);

      $blog = $table->createRow();
      $blog->setFromArray($values);
      $blog->save();

      // Auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }

      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
      }

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $blog->tags()->addTagMaps($viewer, $tags);

      // Add activity only if blog is published
      if( $values['draft'] == 0 ) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'blog_new');

        // make sure action exists before attaching the blog to the activity
        if( $action ) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
        }

      }

      // Send notifications for subscribers
      Engine_Api::_()->getDbtable('subscriptions', 'blog')
        ->sendNotifications($blog);

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $blog->getIdentity();
  }

  public function getBlogPrivacy()
  {
    $values = array();
    $values['auth_view'] = array();
    $values['auth_comment'] = array();

    $values['auth_view'] += array_fill(0, 15, 'everyone');
    $values['auth_view'] += array_fill(15, 1, 'owner_network');
    $values['auth_view'] += array_fill(16, 1, 'owner_member_member');
    $values['auth_view'] += array_fill(17, 1, 'owner_member');
    $values['auth_view'] += array_fill(18, 1, 'owner');

    $values['auth_comment'] += array_fill(0, 15, 'everyone');
    $values['auth_comment'] += array_fill(15, 1, 'owner_network');
    $values['auth_comment'] += array_fill(16, 1, 'owner_member_member');
    $values['auth_comment'] += array_fill(17, 1, 'owner_member');
    $values['auth_comment'] += array_fill(18, 1, 'owner');

    shuffle($values['auth_view']);
    shuffle($values['auth_comment']);

    return array('auth_view' => $values['auth_view'][0], 'auth_comment' => $values['auth_comment'][0]);
  }

  public function printResult($var, $return = false)
  {
    $type = gettype( $var );

    $out = print_r( $var, true );
    $out = htmlspecialchars( $out );
    $out = str_replace('  ', '&nbsp; ', $out );
    if( $type == 'boolean' )
      $content = $var ? 'true' : 'false';
    else
      $content = nl2br( $out );
    $out = '<div style="
      border:2px inset #666;
      background:black;
      font-family:Verdana;
      font-size:11px;
      color:#6F6;
      text-align:left;
      margin:20px;
      padding:16px">
        <span style="color: #F66">('.$type.')</span> '.$content.'</div><br /><br />';

    if( !$return )
      echo $out;
    else
      return $out;
  }
}