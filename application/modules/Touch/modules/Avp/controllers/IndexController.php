<?php
class Avp_IndexController extends Touch_Controller_Action_Standard
{


      public function init()
      {
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
            $ajaxContext->addActionContext('data', 'json')
                        ->addActionContext('rate', 'json')
                        ->initContext();
                        
            $id = $this->_getParam('video_id', $this->_getParam('id', null));
            
            if ($id)
            {
                  $video = Engine_Api::_()->getItem('avp_video', $id);

                  if ($video)
                  {
                        Engine_Api::_()->core()->setSubject($video);
                  }
            }
            
            $request = $this->getRequest();

            if ($request->getActionName() == 'browse' || $request->getActionName() == 'manage')
            {
                  $form = new Avp_Form_Search();
            
                  Zend_Registry::set('Avp_Search_Form', array(
                        'form' => &$form,
                        'request' => $request
                  ));
            }
            
            if ($request->isPost() && isset($_POST['avp_text']) && isset($_POST['avp_redirect']))
            {
                  $_SESSION['Avp_Composer'] = array($_POST['avp_text'], $_POST['avp_redirect']);
                  return $this->_helper->redirector->gotoRoute(array('action' => $request->getActionName()), 'avp_extended', true);
            }
      }

      public function deleteAction()
      {
            $this->view->form = $form = new Avp_Form_Delete();
            
            $video = Engine_Api::_()->core()->getSubject();
            
            if (!in_array($video->video_type, Engine_Api::_()->avp()->getVideoTypes())) return;
            
            $viewer = $this->_helper->api()->user()->getViewer();
            
            if (!$video->isOwner($viewer) && !$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid())
            {
                  return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'avp_general', true);
            }
            
            $form->video_id->setValue($video->video_id);
            
            if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) return;

            $values = $form->getValues();
            
            $api = Engine_Api::_()->getApi('core', 'avp');
            
            $video = $api->getItem('avp_video', $values['video_id']);
            
            if (!$video->isOwner($viewer) && !$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid())
            {
                  return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'avp_general', true);
            }
            
            $event = Engine_Hooks_Dispatcher::_()->callEvent('Avp_onVideoDeleteBefore', array(
                  'video' => $video
            ));
            
            $api->deleteVideo($video);
            
            return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRedirect' => $this->view->url(array('action' => 'manage'), 'avp_general', true),
                  'messages' => array($this->view->translate('The video was deleted.')),
                  'layout' => 'default-simple'
            ));
      }

      public function browseAction()
      {
            $settings = Engine_Api::_()->getApi('settings', 'avp');
            if (@$settings->hide_browse_page && !Engine_Api::_()->user()->getViewer()->getIdentity()) $this->_helper->requireAuth()->forward();
      
            unset($_SESSION['Avp_Manage_Search']);
      
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('avp_main', array(), 'avp_main_browse');
            
            $this->view->content = $this->_helper->content;
      }

      public function editAction()
      {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('avp_main', array(), 'avp_main_manage');

            $this->view->form = $form = new Avp_Form_Edit();
            $this->view->video = $video = Engine_Api::_()->core()->getSubject();
            $this->view->viewer = $viewer = $this->_helper->api()->user()->getViewer();

            if ((!$video->isOwner($viewer) && !$this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->isValid()) || $video->status != 1)
            {
                  return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'avp_general', true);
            }

            $user_list = explode(";", $video->can_view);

            $this->view->can_view = array();

            if (!empty($user_list))
            {
                  $users_table = Engine_Api::_()->getDbtable('users', 'user');
                  $users_select = $users_table->select()->where('user_id IN(?)', $user_list);
                  $usrs = $users_table->fetchAll($users_select);

                  foreach ($usrs as $usr)
                  {
                        if ($usr->getIdentity() > 0)
                        {
                              $this->view->can_view[] = array(
                                    'title' => $usr->getTitle(),
                                    'id' => $usr->getIdentity()
                              );
                        }
                  }
            }

            $this->view->can_view = Zend_Json::encode($this->view->can_view);

            $form->search->setValue($video->search);
            $form->title->setValue($video->title);
            $form->description->setValue(str_replace("<br />", "\n", $video->description));

            if ($form->category_id && $video->category_id > 0)
            {
                  $form->category_id->setValue($video->category_id);
            }

            $auth = Engine_Api::_()->authorization()->context;

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone', 'group');

            foreach ($roles as $role)
            {
                  if (1 === $auth->isAllowed($video, $role, 'view'))
                  {
                        $form->auth_view->setValue($role);
                  }

                  if (1 === $auth->isAllowed($video, $role, 'comment'))
                  {
                        $form->auth_comment->setValue($role);
                  }
            }

            $db = Engine_Db_Table::getDefaultAdapter();

            $roleq = $db->query("SELECT value FROM engine4_authorization_allow WHERE resource_id = '{$video->video_id}' AND value = 1 AND role = 'group' AND resource_type = 'avp_video'")->fetchAll();

            if (!empty($roleq)) $form->auth_view->setValue('group');

            $form->toValues->setValue($video->can_view);

            if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) return;

            $values = $form->getValues();

            $db = Engine_Api::_()->getDbtable('videos', 'avp')->getAdapter();
            $db->beginTransaction();

            $api = Engine_Api::_()->getApi('core', 'avp');

            $api->removeTags($video->tags);
            $values['tags'] = $api->saveTags($values['tags']);
            $values['can_view'] = preg_replace("/[^0-9;]/", "", $values['toValues']);

            try
            {
                  $table = $this->_helper->api()->getDbtable('videos', 'avp');

                  $video->setFromArray($values);

                  $auth = Engine_Api::_()->authorization()->context;
                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone', 'group');
                  $viewMax = array_search($values['auth_view'], $roles);

                  foreach( $roles as $i=>$role )
                  {
                        $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
                  }

                  $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
                  $commentMax = array_search($values['auth_comment'], $roles);

                  foreach ($roles as $i => $role)
                  {
                        $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
                  }

                  $event = Engine_Hooks_Dispatcher::_()->callEvent('Avp_onVideoEditAfter', array(
                        'video' => $video
                  ));

                  $video->save();

                  $db->commit();
            }
            catch (Exception $e)
            {
                  $db->rollBack();
                  throw $e;
            }

            return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'avp_general', true);
      }

      public function manageAction()
      {
            unset($_SESSION['Avp_Browse_Search']);
      
            if (!$this->_helper->requireUser->isValid()) return;
            
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('avp_main', array(), 'avp_main_manage');
            
            $this->view->form = $form = new Avp_Form_Search();
            
            $request = $this->getRequest();
            
            $viewer = $this->_helper->api()->user()->getViewer();
            $form->isValid($request->getPost());
            $values = $form->getValues();
            $values['user_id'] = $viewer->getIdentity();

            if ($request->getPost())
            {
                  $this->view->is_search = false;
                  
                  foreach ($values as $value)
                  {
                        if (!empty($value))
                        {
                              $this->view->is_search = true;
                              break;
                        }
                  }

                  $_SESSION['Avp_Manage_Search'] = $values;
            }
            else if ((int)$request->getParam('page', 0) > 0)
            {     
                  $this->view->is_search = false;
                  
                  foreach ($values as $value)
                  {
                        if (!empty($value))
                        {
                              $this->view->is_search = true;
                              break;
                        }
                  }
            
                  $values = array();
                  $values['user_id'] = $viewer->getIdentity();

                  $old_values = (isset($_SESSION['Avp_Manage_Search']) && !empty($_SESSION['Avp_Manage_Search']) ? $_SESSION['Avp_Manage_Search'] : array());
                  $values = array_merge($values, $old_values);

                  $form->populate($values);
            }
            else
            {
                  unset($_SESSION['Avp_Manage_Search']);
            }
            
            $values['only_types'] = true;
            
            $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'avp')->getVideosPaginator($values);
            
            $this->view->paginator->setItemCountPerPage(20);
            $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));

            $this->view->message = $this->_getParam('message', 0);
      }

      public function favoriteAction()
      {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('avp_main', array(), 'avp_main_favorite');

            $values['only_types'] = true;
            $values['viewer_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
            $values['favorites'] = true;

            $this->view->paginator = $paginator = Engine_Api::_()->getApi('core', 'avp')->getVideosPaginator($values);

            $this->view->paginator->setItemCountPerPage(15);
            $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));
      }

      public function doFavoriteAction()
      {
            if (!$this->_helper->requireSubject()->isValid()) return;
      
            $video_id = $this->_getParam('id');
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            
            $table  = Engine_Api::_()->getDbTable('favorites', 'avp');
            $favorite = Engine_Api::_()->avp()->getFavorites($user_id);
            
            $message = $this->view->translate('The video was added to your favorites.');
            
            $type = true;
            
            if (empty($favorite))
            {
                  $favorites = Zend_Json::encode(array($video_id));
            
                  Engine_Api::_()->getDbTable('favorites', 'avp')->insert(array(
                        'user_id' => $user_id,
                        'favorites' => $favorites
                  ));
            }
            else
            {
                  $favorites = Zend_Json::decode($favorite->favorites);
                  
                  if (!in_array($video_id, $favorites))
                  {
                        $favorites[] = $video_id;
                        
                        $favorite->favorites = Zend_Json::encode($favorites);
                        $favorite->save();
                  }
                  else
                  {
                        $message = $this->view->translate('The video was removed from your favorites.');
                        $type = false;
                  
                        $favorites = array_diff($favorites, array($video_id));
                        
                        $favorite->favorites = Zend_Json::encode($favorites);
                        $favorite->save();
                  }
            }
            
            return $this->_forward('rating-success', 'utility', 'avp', array(
                  'messages' => array($message),
                  'type' => $type
            ));
      }

      public function viewAction()
      {
            $viewer = $this->_helper->api()->user()->getViewer();
            $video = Engine_Api::_()->core()->getSubject();

            if ($video->hasGroupPrivacy() && !in_array($viewer->getIdentity(), array_merge(explode(";", $video->can_view), array($video->owner_id)))) $this->_helper->requireAuth()->forward();
            if (!$this->_helper->requireSubject()->isValid()) $this->_helper->requireAuth()->forward();
            
            if (!in_array($video->video_type, Engine_Api::_()->getApi('core', 'avp')->getVideoTypes())) return;
            if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()) return;

            if (!$video->isOwner(Engine_Api::_()->user()->getViewer()))
            {
                  $video->view_count++;
                  $video->save();
            }
            
            if ($video->status != 1) return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'avp_general', true); 
            
            if ($video->embed != "")
            {
                  $importer = new Avp_Import;

                  try
                  {
                        $adapter = $importer->getAdapter($video->url);
                        
                        $settings = Engine_Api::_()->getApi('settings', 'avp');
                              
                        $disabled = $settings->disabled;
                              
                        if (!empty($disabled))
                        {
                              $disabled = Zend_Json::decode($settings->disabled);
                        }
                        else
                        {
                              $disabled = array();
                        }
                        
                        if (in_array($adapter->key(), $disabled))
                        {
                              echo $this->view->translate('We currently have problems with videos imported from ').$adapter->name().'. '.$this->view->translate('Please try again later.');
                        }
                        else
                        {
                              //$this->_helper->content->render();
                              $this->viewVideoContent();
                        }
                  }
                  catch (Exception $e)
                  {
                        echo $this->view->translate('We currently have problems with videos imported from ').$adapter->name().'. '.$this->view->translate('Please try again later.');
                  }
            }
            else
            {
                  //$this->_helper->content->render();
              $this->viewVideoContent();
            }
      }

      public function importAction()
      {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('avp_main', array(), 'avp_main_import');

            if (!$this->_helper->requireUser->isValid()) return;
            if (!$this->_helper->requireAuth()->setAuthParams('avp_video', null, 'import')->isValid()) return;

            $this->view->form = $form = new Avp_Form_Import();
            $request = $this->getRequest();
            
            if (!$request->isPost() && preg_match("/composer$/", Zend_Controller_Front::getInstance()->getRouter()->getCurrentRoute()->assemble()))
            {
                  $form->setDescription("You will be redirect back to previous page after you have filled required video information.");
            }

            if (!$request->isPost() || !$form->isValid($request->getPost())) return;

            $values = $form->getValues();
            
            $this->view->viewer = $viewer = $this->_helper->api()->user()->getViewer();

            $values['video_type'] = 'import';
            $values['owner_id'] = $viewer->getIdentity();
            $values['owner_type'] = $viewer->getType();
            $values['description'] = preg_replace("/\n|\r\n|\r$/", "<br />", $values['description']);
            $values['can_view'] = preg_replace("/[^0-9;]/", "", $values['toValues']);

            $importer = new Avp_Import;

            try
            {
                  $adapter = $importer->getAdapter($values['url']);
                  
                  $allowed = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('avp_video', $viewer->level_id, 'sites');
                  
                  $settings = Engine_Api::_()->getApi('settings', 'avp');
                        
                  $disabled = @$settings->disabled;
                        
                  if (!empty($disabled))
                  {
                        $disabled = Zend_Json::decode($settings->disabled);
                  }
                  else
                  {
                        $disabled = array();
                  }
                  
                  if (!in_array($adapter->key(), $allowed) || in_array($adapter->key(), $disabled))
                  {
                        $form->addError($this->view->translate('You are not allowed to import videos from %1$s.', $adapter->name()));
                        return;
                  }
            }
            catch (Exception $e)
            {
                  $form->addError($this->view->translate('The video URL is not valid. Please check the URL and try again.'));
                  return;
            }

            $db = Engine_Api::_()->getDbtable('videos', 'avp')->getAdapter();
            $db->beginTransaction();

            try
            {
                  $table = $this->_helper->api()->getDbtable('videos', 'avp');

                  $video = $table->createRow();

                  $video->setFromArray($values);
                  $video->save();

                  if (($img = $adapter->img()) !== false && ($url = $adapter->url()) !== false && ($embed = $adapter->embed()) !== false && ($oid = $adapter->id()) !== false)
                  {
                        $tmp_file = APPLICATION_PATH."/temporary/avp/thumb_{$video->video_id}_default.jpg";
                        $thumb_file = APPLICATION_PATH."/temporary/avp/thumb_{$video->video_id}.jpg";
                        
                        $tmp_path = dirname($tmp_file);
                        if (!file_exists($tmp_path)) mkdir($tmp_path, 0777, true);            

                        $encode = Engine_Api::_()->getApi('encode', 'avp');

                        $src_fh = fopen($img, 'r');
                        $tmp_fh = fopen($tmp_file, 'w');
                        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
                        
                        if (!file_exists($tmp_file) || !@filesize($tmp_file))
                        {
                              $form->addError($this->view->translate('The server was not able to import the video.'));
                              return;
                        }

                        list($org_width, $org_height, $type, $attr) = getimagesize($tmp_file);

                        list($thumb_width, $thumb_height, $thumb_x, $thumb_y) = $encode->get_crop_values($org_width, $org_height);

                        $image = Engine_Image::factory();

                        $image->open($tmp_file)
                              ->crop($thumb_x, $thumb_y, $thumb_width, $thumb_height)
                              ->resize(153, 86)
                              ->write($thumb_file)
                              ->destroy();

                        try
                        {
                              $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                                    'parent_type' => $video->getType(),
                                    'parent_id' => $video->getIdentity()
                              ));
                        }
                        catch (Exception $e)
                        {

                        }

                        $video->url = $url;
                        $video->embed = $embed;
                        $video->oid = $oid;
                        $video->duration = $adapter->duration();
                        $video->photo_id = $thumbFileRow->file_id;
                        $video->save();
                        
                        // AUTHORIzATION STUFF
                        $auth = Engine_Api::_()->authorization()->context;
                        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone', 'group');
                        $viewMax = array_search($values['auth_view'], $roles);

                        foreach ($roles as $i => $role)
                        {
                              $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
                        }

                        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
                        $commentMax = array_search($values['auth_comment'], $roles);

                        foreach ($roles as $i => $role)
                        {
                              $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
                        }
                        
                        // TAGS
                        $api = Engine_Api::_()->getApi('core', 'avp');
                        
                        $values['tags'] = $api->saveTags($values['tags']);

                        // CREATE ACTION
                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($video->getOwner(), $video, 'avp_video_new_import', null, array('website' => $adapter->name()));
                                                
                        if ($action != null) Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
                        
                        $video->status = 1;
                        
                        $event = Engine_Hooks_Dispatcher::_()->callEvent('Avp_onVideoImportAfter', array(
                              'video' => $video
                        ));
                        
                        $video->save();

                        $db->commit();
                        
                        return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'message' => 2), 'avp_general', true);
                  }
            }
            catch(Exception $e)
            {
                  $db->rollBack();
                  throw $e;
            }
      }

      public function feedImportAction()
      {
            if (!$this->_helper->requireUser->isValid()) return;
            if (!$this->_helper->requireAuth()->setAuthParams('avp_video', null, 'import')->isValid()) return;

            $this->view->form = $form = new Touch_Form_Avp_FeedImport();
            $request = $this->getRequest();

            if (!$request->isPost() || !$form->isValid($request->getPost())) return;

            $values = $form->getValues();
            
            $this->view->viewer = $viewer = $this->_helper->api()->user()->getViewer();

            $values['video_type'] = 'import';
            $values['owner_id'] = $viewer->getIdentity();
            $values['owner_type'] = $viewer->getType();
            $values['description'] = preg_replace("/\n|\r\n|\r$/", "<br />", $values['description']);

            $importer = new Avp_Import;

            try
            {
                  $adapter = $importer->getAdapter($values['url']);
                  $allowed = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('avp_video', $viewer->level_id, 'sites');
                  $settings = Engine_Api::_()->getApi('settings', 'avp'); 
                  $disabled = @$settings->disabled;
                        
                  if (!empty($disabled))
                  {
                        $disabled = Zend_Json::decode($settings->disabled);
                  }
                  else
                  {
                        $disabled = array();
                  }
                  
                  if (!in_array($adapter->key(), $allowed) || in_array($adapter->key(), $disabled))
                  {
                        $form->addError($this->view->translate('You are not allowed to import videos from %1$s.', $adapter->name()));
                        return;
                  }
            }
            catch (Exception $e)
            {
                  $form->addError($this->view->translate('The video URL is not valid. Please check the URL and try again.'));
                  return;
            }

            $db = Engine_Api::_()->getDbtable('videos', 'avp')->getAdapter();
            $db->beginTransaction();

            try
            {
                  $table = $this->_helper->api()->getDbtable('videos', 'avp');

                  $video = $table->createRow();

                  $video->setFromArray($values);
                  $video->save();

                  if (($img = $adapter->img()) !== false && ($url = $adapter->url()) !== false && ($embed = $adapter->embed()) !== false && ($oid = $adapter->id()) !== false)
                  {
                        $tmp_file = APPLICATION_PATH."/temporary/avp/thumb_{$video->video_id}_default.jpg";
                        $thumb_file = APPLICATION_PATH."/temporary/avp/thumb_{$video->video_id}.jpg";
                        
                        $tmp_path = dirname($tmp_file);
                        if (!file_exists($tmp_path)) mkdir($tmp_path, 0777, true);            

                        $encode = Engine_Api::_()->getApi('encode', 'avp');

                        $src_fh = fopen($img, 'r');
                        $tmp_fh = fopen($tmp_file, 'w');
                        stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

                        list($org_width, $org_height, $type, $attr) = getimagesize($tmp_file);

                        list($thumb_width, $thumb_height, $thumb_x, $thumb_y) = $encode->get_crop_values($org_width, $org_height);

                        $image = Engine_Image::factory();

                        $image->open($tmp_file)
                              ->crop($thumb_x, $thumb_y, $thumb_width, $thumb_height)
                              ->resize(153, 86)
                              ->write($thumb_file)
                              ->destroy();

                        try
                        {
                              $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                                    'parent_type' => $video->getType(),
                                    'parent_id' => $video->getIdentity()
                              ));
                        }
                        catch (Exception $e) {}

                        $video->url = $url;
                        $video->embed = $embed;
                        $video->oid = $oid;
                        $video->duration = $adapter->duration();
                        $video->photo_id = $thumbFileRow->file_id;
                        $video->feed = 1;
                        $video->search = 1;
                        $video->save();
                        
                        $auth = Engine_Api::_()->authorization()->context;
                        
                        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
                        $viewMax = array_search($values['auth_view'], $roles);

                        foreach ($roles as $i => $role)
                        {
                              $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
                        }

                        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

                        foreach ($roles as $role)
                        {
                              $auth->setAllowed($video, $role, 'comment', true);
                        }
                        
                        $video->save();

                        // CREATE ACTION
                        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($video->getOwner(), $video, 'avp_video_new_import', null, array('website' => $adapter->name()));
                                                
                        if ($action != null) Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
                        
                        $video->status = 1;
                        $video->save();
                        $db->commit();
                        
                        return $this->_forward('success', 'utility', 'touch', array(
                              'messages' => array('The video was successfully imported.')
                        ));
                        
                        /*return $this->_forward('success', 'utility', 'core', array(
                            'smoothboxClose' => 5,
                            'parentRefresh'=> 5,
                            'messages' => array('The video was successfully imported.')
                        ));*/
                  }
            }
            catch(Exception $e)
            {
                  $db->rollBack();
                  throw $e;
            }
      }

      public function dataAction()
      {
            if ($this->getRequest()->isPost())
            {
                  $url = urldecode($this->_getParam('url', null));

                  $importer = new Avp_Import;

                  try
                  {
                        $adapter = $importer->getAdapter($url);
                        
                        $allowed = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('avp_video', Engine_Api::_()->user()->getViewer()->level_id, 'sites');
                        
                        $settings = Engine_Api::_()->getApi('settings', 'avp');
                        
                        $disabled = $settings->disabled;
                        
                        if (!empty($disabled))
                        {
                              $disabled = Zend_Json::decode($settings->disabled);
                        }
                        else
                        {
                              $disabled = array();
                        }

                        if (in_array($adapter->key(), $allowed) && !in_array($adapter->key(), $disabled))
                        {
                              $this->view->status = true;
                              $this->view->data = $adapter->data();
                        }
                        else
                        {
                              $this->view->status = false;
                              $this->view->message = $this->view->translate('You are not allowed to import videos from %1$s.', $adapter->name());
                        }
                  }
                  catch (Exception $e)
                  {
                        $this->view->status = false;
                        $this->view->message = $this->view->translate('Video URL is not valid.');
                  }
            }
      }

      public function rateAction()
      {
            $rating = $this->_getParam('rating');
            $video_id = $this->_getParam('video_id');
            
            Engine_Api::_()->avp()->setRating($video_id, $rating);

            $rating = Engine_Api::_()->avp()->getRating($video_id);
            
            $this->view->total = count(Zend_Json::decode($rating->users));
            $this->view->rating = $rating->rating;
            $this->view->message = $this->view->translate(array('%s rating', '%s ratings', $this->view->total), $this->view->total);
      }

      private  function viewVideoContent(){

        if (!Engine_Api::_()->core()->hasSubject() || empty(Engine_Api::_()->core()->getSubject()->video_id)) return $this->setNoRender();

        $this->view->video = $video = Engine_Api::_()->core()->getSubject();

        list($url, $embed, $bbcode) = Engine_Api::_()->avp()->embedCodes($video);

        $this->view->embed = $embed;

        if ($video->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'edit')) $this->view->can_edit = true;
        else $this->view->can_edit = false;

        if ($video->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'delete')) $this->view->can_delete = true;
        else $this->view->can_delete = false;

        $this->view->favorite = false;

        if (!Engine_Api::_()->user()->getViewer()->getIdentity()) return;

        $favorites = Engine_Api::_()->avp()->getFavorites(Engine_Api::_()->user()->getViewer()->getIdentity());

        if (!empty($favorites))
        {
              $favorites = Zend_Json::decode($favorites->favorites);

              if (in_array($video->video_id, $favorites)) $this->view->favorite = true;
        }

      }
}