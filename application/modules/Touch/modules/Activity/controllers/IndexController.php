<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Activity_IndexController extends Touch_Controller_Action_Standard
{
  public function postAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Get subject if necessary
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    $subject_guid = $this->_getParam('subject', null);
    if( $subject_guid )
    {
      $subject = Engine_Api::_()->getItemByGuid($subject_guid);
    }
    // Use viewer as subject if no subject
    if( null === $subject )
    {
      $subject = $viewer;
    }

    // Make form
    $form = $this->view->form = new Activity_Form_Post();

    // Check auth
    if( !$subject->authorization()->isAllowed($viewer, 'comment') ) {
      return $this->_helper->requireAuth()->forward();
    }

    // Check if post
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
      return;
    }

    // Check if form is valid
    $postData = $this->getRequest()->getPost();
    $body = @$postData['body'];
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
    $postData['body'] = $body;
    if( !$form->isValid($postData) )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check one more thing
    if( $form->body->getValue() === '' && $form->getValue('attachment_type') === '' )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // set up action variable
    $action = null;

    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();

    try
    {
      // Try attachment getting stuff
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');
      if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
        $type = $attachmentData['type'];
        $config = null;
        foreach( Zend_Registry::get('Engine_Manifest') as $data )
        {
          if( !empty($data['composer'][$type]) )
          {
            $config = $data['composer'][$type];
          }
        }
        if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
          $config = null;
        }
        if( $config ) {
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach'.ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
        }
      }


      // Get body
      $body = $form->getValue('body');
      $body = preg_replace('/<br[^<>]*>/', "\n", $body);

      // Special case: status
      if( !$attachment && $viewer->isSelf($subject) )
      {
        if( $body != '' )
        {
          $viewer->status = $body;
          $viewer->status_date = date('Y-m-d H:i:s');
          $viewer->save();

          $viewer->status()->setStatus($body);
        }

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'status', $body, array('is_mobile' => true));
      }

      // General post
      else
      {
        $type = 'post';
        if( $viewer->isSelf($subject) )
        {
          $type = 'post_self';
        }
        
        // Add notification for <del>owner</del> user
        $subjectOwner = $subject->getOwner();
        if( !$viewer->isSelf($subject) && $subject instanceof User_Model_User )
        {
          $notificationType = 'post_'.$subject->getType();
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
            'url1' => $subject->getHref(),
          ));
        }

        // Add activity
        $action = $this->_helper->api()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, $type, $body, array('is_mobile' => true));
        
        // Try to attach if necessary
        if( $action && $attachment )
        {
          $this->_helper->api()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
        }
      }
      $db->commit();
    } // end "try"
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e; // This should be caught by error handler
    }


    // If we're here, we're done
    $this->view->status = true;
    $this->view->message =  Zend_Registry::get('Zend_Translate')->_('Success!');

		if ($this->_getParam('format') == 'json'){
			$this->view->action_id = $action->getIdentity();
			$this->view->body = $this->view->touchActivity($action, array('noList' => true));
		}	

    // Check if action was created
    $post_fail = null;
    if(!$action){
      $post_fail = "?pf=1";
    }

    // Redirect if in normal context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $return_url = $form->getValue('return_url', false);
      if( $return_url )
      {
        return $this->_helper->touchRedirector->gotoUrl($return_url.$post_fail, array('prependBase' => false));
      }
    }

  }
  
  public function viewlikeAction()
  {
    // Collect params
    $action_id = $this->_getParam('action_id');
    $viewer = $this->_helper->api()->user()->getViewer();

    $action = $this->_helper->api()->getDbtable('actions', 'activity')->getActionById($action_id);


    // Redirect if not json context
    if (null===$this->_getParam('format', null))
    {
      $this->_helper->touchRedirector->gotoRoute(array(), 'home');
    }
    else if ('json'===$this->_getParam('format', null))
    {
      $this->view->body = $this->view->touchActivity($action, array('viewAllLikes' => true, 'noList' => $this->_getParam('nolist', false)));
    }
  }

  public function likeAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = $this->_helper->api()->user()->getViewer();

    // Start transaction
    $db = $this->_helper->api()->getDbtable('likes', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      $action = $this->_helper->api()->getDbtable('actions', 'activity')->getActionById($action_id);

      // Action
      if( !$comment_id ) {

        // Check authorization
        if( !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') ) {
          throw new Engine_Exception('This user is not allowed to like this item');
        }

        $action->likes()->addLike($viewer);

        // Add notification for owner of activity (if user and not viewer)
        if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() ) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);

          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
            'label' => 'post'
          ));
        }

      }
      // Comment
      else {
        $comment = $action->comments()->getComment($comment_id);

        // Check authorization
        if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment') ) {
          throw new Engine_Exception('This user is not allowed to like this item');
        }

        $comment->likes()->addLike($viewer);

        // @todo make sure notifications work right

        // Add notification for owner of activity (if user and not viewer)
        if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() ) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);

        }
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Success
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You now like this action.');

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->touchRedirector->gotoRoute(array(), 'home');

    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      $this->view->body = $this->view->touchActivity($action, array('noList' => true));
    }
  }

  public function unlikeAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = $this->_helper->api()->user()->getViewer();

    // Start transaction
    $db = $this->_helper->api()->getDbtable('likes', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      $action = $this->_helper->api()->getDbtable('actions', 'activity')->getActionById($action_id);

      // Action
      if( !$comment_id ) {

        // Check authorization
        if( !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') ) {
          throw new Engine_Exception('This user is not allowed to unlike this item');
        }

        $action->likes()->removeLike($viewer);
      }

      // Comment
      else {
        $comment = $action->comments()->getComment($comment_id);

        // Check authorization
        if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment') ) {
          throw new Engine_Exception('This user is not allowed to like this item');
        }

        $comment->likes()->removeLike($viewer);
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Success
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You no longer like this action.');

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->touchRedirector->gotoRoute(array(), 'home');
    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      $this->view->body = $this->view->touchActivity($action, array('noList' => true));
    }
  }

  public function viewcommentAction()
  {
    // Collect params
    $action_id = $this->_getParam('action_id');
    $viewer    = $this->_helper->api()->user()->getViewer();

    $action    = $this->_helper->api()->getDbtable('actions', 'activity')->getActionById($action_id);
    $form      = $this->view->form = new Activity_Form_Comment();
    $form->setActionIdentity($action_id);
    

    // Redirect if not json context
    if (null===$this->_getParam('format', null))
    {
      $this->_helper->touchRedirector->gotoRoute(array(), 'home');
    }
    else if ('json'===$this->_getParam('format', null))
    {
      $this->view->body = $this->view->touchActivity($action, array('viewAllComments' => true, 'noList' => $this->_getParam('nolist', false)));
    }
  }

  public function commentAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Make form
    $this->view->form = $form = new Activity_Form_Comment();
    
    // Not post
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Not a post');
      return;
    }

    // Not valid
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Start transaction
    $db = $this->_helper->api()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
      if (!$action) {
        $this->view->status = false;
        $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Activity does not exist');
        return;
      }
      $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);
      $body = $form->getValue('body');

      // Check authorization
      if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'))
        throw new Engine_Exception('This user is not allowed to comment on this item.');

      // Add the comment
      $action->comments()->addComment($viewer, $body);

      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

      // Add notification for owner of activity (if user and not viewer)
      if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() )
      {
        $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
          'label' => 'post'
        ));
      }
      
      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach( $action->comments()->getAllCommentsUsers() as $notifyUser )
      {
        if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
        {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
            'label' => 'post'
          ));
        }
      }
      
      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach( $action->likes()->getAllLikesUsers() as $notifyUser )
      {
        if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
        {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
            'label' => 'post'
          ));
        }
      }
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Assign message for json
    $this->view->status = true;
    $this->view->message = 'Comment posted';

    // Redirect if not json
    if( null === $this->_getParam('format', null) )
    {
      $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
    }
    else if ('json'===$this->_getParam('format', null))
    {
      $this->view->body = $this->view->touchActivity($action, array('noList' => true));
    }


  }

  public function shareAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $type = $this->_getParam('type');
    $id = $this->_getParam('id');

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->attachment = $attachment = Engine_Api::_()->getItem($type, $id);
    $this->view->form = $form = new Activity_Form_Share();

    $this->view->formPosted = $this->getRequest()->isPost();

    if( !$attachment ) {
      // tell smoothbox to close
      $this->view->status  = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
      $this->view->smoothboxClose = true;
      return $this->render('deletedItem');
    }


    // hide facebook and twitter option if not logged in
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    if( !$facebookTable->isConnected() ) {
      $form->removeElement('post_to_facebook');
    }

    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    if( !$twitterTable->isConnected() ) {
      $form->removeElement('post_to_twitter');
    }



    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      // Get body
      $body = $form->getValue('body');

      // Add activity
      $api = $this->_helper->api()->getDbtable('actions', 'activity');
      $action = $api->addActivity($viewer, $viewer, 'post_self', $body, array('is_mobile' => true));
      if( $action ) {
        $api->attachActivity($action, $attachment);
      }
      $db->commit();


      // Publish to facebook, if checked & enabled
      if( $this->_getParam('post_to_facebook', false) &&
          'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
        try {

          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $facebookApi = $facebook = $facebookTable->getApi();
          $fb_uid = $facebookTable->find($viewer->getIdentity())->current();


          if( $fb_uid &&
              $fb_uid->facebook_uid &&
              $facebookApi &&
              $facebookApi->getUser() &&
              $facebookApi->getUser() == $fb_uid->facebook_uid ) {
            $url    = 'http://' . $_SERVER['HTTP_HOST'] . $this->getFrontController()->getBaseUrl();
            $name   = 'Activity Feed';
            $desc   = '';
            $picUrl = $viewer->getPhotoUrl('thumb.icon');
            if( $attachment ) {
              $url  = $attachment->getHref();
              $desc = $attachment->getDescription();
              $name = $attachment->getTitle();
              if( empty($name) ) {
                $name = ucwords($attachment->getShortType());
              }
              $tmpPicUrl = $attachment->getPhotoUrl();
              if( $tmpPicUrl ) {
                $picUrl = $tmpPicUrl;
              }
              // prevents OAuthException: (#100) FBCDN image is not allowed in stream
              if( preg_match('/fbcdn.net$/i', parse_url($picUrl, PHP_URL_HOST)) ) {
                $picUrl = $viewer->getPhotoUrl('thumb.icon');
              }
            }

            // Check stuff
            if( false === stripos($url, 'http://') ) {
              $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
            }
            if( false === stripos($picUrl, 'http://') ) {
              $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
            }

            // include the site name with the post:
            $name = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                . ": $name";

            $fb_data = array(
              'message' => html_entity_decode($form->getValue('body')),
              'link' => $url,
              'name' => $name,
              'description' => $desc,
            );

            if( $picUrl ) {
              $fb_data = array_merge($fb_data, array('picture' => $picUrl));
            }

            $res = $facebook->api('/me/feed', 'POST', $fb_data);
          }
        } catch( Exception $e ) {
          // Silence
        }
      } // end Facebook

      // Publish to twitter, if checked & enabled
      if( $this->_getParam('post_to_twitter', false) &&
          'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable ) {
        try {
          $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
          if( $twitterTable->isConnected() ) {

            // Get attachment info
            $title = $attachment->getTitle();
            $url = $attachment->getHref();
            $picUrl = $attachment->getPhotoUrl();

            // Check stuff
            if( $url && false === stripos($url, 'http://') ) {
              $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
            }
            if( $picUrl && false === stripos($picUrl, 'http://') ) {
              $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
            }

            // Try to keep full message
            // @todo url shortener?
            $message = html_entity_decode($form->getValue('body'));
            if( Engine_String::strlen($message) + Engine_String::strlen($title) + Engine_String::strlen($url) + Engine_String::strlen($picUrl) + 9 <= 140 ) {
              if( $title ) {
                $message .= ' - ' . $title;
              }
              if( $url ) {
                $message .= ' - ' . $url;
              }
              if( $picUrl ) {
                $message .= ' - ' . $picUrl;
              }
            } else if( Engine_String::strlen($message) + Engine_String::strlen($title) + Engine_String::strlen($url) + 6 <= 140 ) {
              if( $title ) {
                $message .= ' - ' . $title;
              }
              if( $url ) {
                $message .= ' - ' . $url;
              }
            } else {
              if( Engine_String::strlen($title) > 24 ) {
                $title = Engine_String::substr($title, 0, 21) . '...';
              }
              // Sigh truncate I guess
              if( Engine_String::strlen($message) + Engine_String::strlen($title) + Engine_String::strlen($url) + 9 > 140 ) {
                $message = Engine_String::substr($message, 0, 140 - (Engine_String::strlen($title) + Engine_String::strlen($url) + 9)) - 3 . '...';
              }
              if( $title ) {
                $message .= ' - ' . $title;
              }
              if( $url ) {
                $message .= ' - ' . $url;
              }
            }

            $twitter = $twitterTable->getApi();
            $twitter->statuses->update($message);
          }
        } catch( Exception $e ) {
          // Silence
        }
      }

    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e; // This should be caught by error handler
    }

    // If we're here, we're done
    $this->view->status = true;
    $this->view->message =  Zend_Registry::get('Zend_Translate')->_('Success!');

    // Redirect if in normal context
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      $return_url = $form->getValue('return_url', false);
      if( !$return_url ) {
        $return_url = $this->view->url(array(), 'default', true);
      }
      return $this->_helper->redirector->gotoUrl($return_url, array('prependBase' => false));
    } else if( 'smoothbox' === $this->_helper->contextSwitch->getCurrentContext() ) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    }
  }

  function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Identify if it's an action_id or comment_id being deleted
    $this->view->comment_id = $comment_id = $this->_getParam('comment_id', null);
    $this->view->action_id  = $action_id  = $this->_getParam('action_id', null);

    $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
    if (!isset($action)){
      // tell smoothbox to close
      return $this->_forward('success', 'utility', 'touch', array(
				'messages'=>array(Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.')),
				'error'=>true,
      ));
    }

    // Send to view script if not POST
    if (!$this->getRequest()->isPost())
      return;
      

    // Both the author and the person being written about get to delete the action_id
    if (!$comment_id && (
        $activity_moderate ||
        ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
        ('user' == $action->object_type  && $viewer->getIdentity() == $action->object_id)))   // commenter
    {
      // Delete action item and all comments/likes
      $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
      $db->beginTransaction();
      try {
        $action->deleteItem();
        $db->commit();

        // tell smoothbox to close
				return $this->_forward('success', 'utility', 'touch', array(
					'parentRefresh' => true,
					'messages'=>array(Zend_Registry::get('Zend_Translate')->_('This activity item has been removed.')),
				));
      } catch (Exception $e) {
        $db->rollback();
        $this->view->status = false;
      }

    } elseif ($comment_id) {
        $comment = $action->comments()->getComment($comment_id);
        // allow delete if profile/entry owner
        $db = Engine_Api::_()->getDbtable('comments', 'activity')->getAdapter();
        $db->beginTransaction();
        if ($activity_moderate ||
           ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
           ('user' == $action->object_type  && $viewer->getIdentity() == $action->object_id))
        {
          try {
            $action->comments()->removeComment($comment_id);
            $db->commit();

						return $this->_forward('success', 'utility', 'touch', array(
							'parentRefresh' => true,
							'messages'=>array(Zend_Registry::get('Zend_Translate')->_('Comment has been deleted')),
						));
          } catch (Exception $e) {
            $db->rollback();
            $this->view->status = false;
          }
        } else {
					return $this->_forward('success', 'utility', 'touch', array(
						'messages'=>array(Zend_Registry::get('Zend_Translate')->_('You do not have the privilege to delete this comment')),
						'error'=>true,
					));
          return $this->render('deletedComment');
        }
      
    } else {
      // neither the item owner, nor the item subject.  Denied!
      return $this->_forward('requireauth', 'error', 'core');
    }
  }

	public function getLikesAction()
  {
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');

    if( !$action_id ||
        !$comment_id ||
        !($action = Engine_Api::_()->getItem('activity_action', $action_id)) ||
        !($comment = $action->comments()->getComment($comment_id)) ) {
      $this->view->status = false;
      $this->view->body = '-';
      return;
    }

    $likes = $comment->likes()->getAllLikesUsers();
    $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
      count($likes)), Engine_String::strip_tags($this->view->fluentList($likes)));
    $this->view->status = true;
  }
}