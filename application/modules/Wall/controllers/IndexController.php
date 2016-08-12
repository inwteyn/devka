<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Wall_IndexController extends Core_Controller_Action_Standard
{
    protected $_script_module;

    public function init()
    {
        $this->_script_module = ($this->_getParam('is_timeline', false)) ? 'timeline' : 'wall';
    }

    public function postAction()
    {
        // Check license
        $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
        $product_result = $hecoreApi->checkProduct('wall');
        if (isset($product_result['result']) && !$product_result['result']) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Your license invalid!');
            $this->view->headScript()->appendScript($product_result['script']);
            return;

        }
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Get subject if necessary
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = null;
        $subject_guid = $this->_getParam('subject', null);
        if ($subject_guid) {
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        }
        // Use viewer as subject if no subject
        if (null === $subject) {
            $subject = $viewer;
        }

        // Make form
        $form = $this->view->form = new Wall_Form_Post();

        // Check auth
        if (!$subject->authorization()->isAllowed($viewer, 'comment')) {
            return $this->_helper->requireAuth()->forward();
        }

        // Check if post
        if (!$this->getRequest()->isPost()) {
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
        if (!$form->isValid($postData)) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        // Check one more thing
        if ($form->body->getValue() === '' && $form->getValue('attachment_type') === '') {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        $attachment_photo = $this->_getParam('attachment');

        if ($attachment_photo['type'] == 'photo') {
            $ids = explode(',', $attachment_photo['photo_id']);
            foreach ($ids as $id) {
                $eneble_all = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('advalbum');

                if ($eneble_all) {
                    $photo = Engine_Api::_()->getItem('advalbum_photo', $id);
                } else {
                    $photo = Engine_Api::_()->getItem('album_photo', $id);
                }

                $auth_allow_table = Engine_Api::_()->getDbTable('allow', 'authorization');

                $update_photo_privacy = $auth_allow_table->update(array('role' => $this->_getParam('privacy')),
                    array(
                        'resource_id = ?' => $photo->getIdentity(),
                        'resource_type = ?' => 'album_photo',
                        'action = ?' => 'view'
                    ));

                if (!$update_photo_privacy) {
                    $auth_allow_table->update(array('role' => $this->_getParam('privacy')),
                        array(
                            'resource_id = ?' => $photo->getIdentity(),
                            'resource_type = ?' => 'headvancedalbum_photo',
                            'action = ?' => 'view'
                        ));
                }
            }
        }

        /**
         * set up action variable
         *
         * @var $action Wall_Model_Action
         */

        $action = null;

        // Process
        $db = Engine_Api::_()->getDbtable('actions', 'wall')->getAdapter();
        $db->beginTransaction();

        try {
            // Try attachment gettingf stuff
            $attachment = null;

            $attachmentData = $this->getRequest()->getParam('attachment');

            if (!empty($attachmentData) && !empty($attachmentData['type'])) {
                $type = $attachmentData['type'];
                $config = null;

                $composer = Engine_Api::_()->wall()->getManifestType('wall_composer');
                if (!empty($composer[$type])) {
                    $config = $composer[$type];
                }

                if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
                    $config = null;
                }

                if ($config) {

                    $plugin = Engine_Api::_()->loadClass($config['plugin']);
                    $method = 'onAttach' . ucfirst($type);
                    if (method_exists($plugin, $method)) {
                        $attachment = $plugin->$method($attachmentData);
                    }

                }
            }
            // Get body
            $body = $form->getValue('body');
            $body = preg_replace('/<br[^<>]*>/', "\n", $body) . ' ';

            // Is double encoded because of design mode
            //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');

            // Special case=>status
            if (!$attachment && $viewer->isSelf($subject)) {
                if ($body != '') {
                    $viewer->status = $body;
                    $viewer->status_date = date('Y-m-d H:i:s');
                    $viewer->save();

                    $viewer->status()->setStatus($body);
                }

                $action = Engine_Api::_()->getDbtable('actions', 'wall')->addActivity($viewer, $subject, 'status', $body, null, $this->_getParam('privacy'));


            } // General post
            else {

                $type = 'post';
                if ($viewer->isSelf($subject)) {
                    $type = 'post_self';
                }

                // Add notification for <del>owner</del> user
                $subjectOwner = $subject->getOwner();
                //if( !$viewer->isSelf($subjectOwner) )
                if (!$viewer->isSelf($subject) && $subject instanceof User_Model_User) {
                    $notificationType = 'post_' . $subject->getType();
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                        'url1' => $subject->getHref(),
                    ));
                }

                if (!$viewer->isSelf($subject)) {
                }

                // Add activity
                $action = $this->_helper->api()->getDbtable('actions', 'wall')->addActivity($viewer, $subject, $type, $body, null, $this->_getParam('privacy'));

                // Try to attach if necessary

                if ($action && $attachment) {
                    if ($attachmentData['type'] == 'photo') {
                        foreach ($attachment as $photo_attachment) {
                            $this->_helper->api()->getDbtable('actions', 'wall')->attachActivity($action, $photo_attachment, Activity_Model_Action::ATTACH_MULTI);
                        }
                    } else {
                        $this->_helper->api()->getDbtable('actions', 'wall')->attachActivity($action, $attachment);
                    }

                }

            }


            if ($action) {

                $composerData = $this->getRequest()->getParam('composer');

                if (!empty($composerData)) {

                    foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $config) {

                        if (empty($config['composer'])) {
                            continue;
                        }

                        $plugin = Engine_Api::_()->loadClass($config['plugin']);
                        $method = 'onComposer' . ucfirst($config['type']);
                        if (method_exists($plugin, $method)) {
                            $plugin->$method($composerData, array('action' => $action));
                        }

                    }

                }

                $tableToken = Engine_Api::_()->getDbTable('tokens', 'wall');
                $stream_services = $this->_getParam('share');

                try {

                    if (!empty($stream_services)) {


                        foreach ($stream_services as $provider => $enabled) {

                            if (!$enabled) {
                                continue;
                            }
                            $tokenRow = $tableToken->getUserToken($viewer, $provider);

                            if (!$tokenRow) {
                                continue;
                            }
                            $service = Engine_Api::_()->wall()->getServiceClass($provider);
                            if (!$service->check($tokenRow)) {
                                continue;
                            }

                            // :)))
                            if (!empty($composerData) && !empty($composerData['fbpage_id']) && $composerData['fbpage_id'] != 'undefined' && $provider == 'facebook') {

                                $fbpage_id = $composerData['fbpage_id'];

                                $fbpageTable = Engine_Api::_()->getDbTable('fbpages', 'wall');
                                $select = $fbpageTable->select()
                                    ->where('user_id = ?', $viewer->getIdentity())
                                    ->where('fbpage_id = ?', $fbpage_id);

                                $fbpage = $fbpageTable->fetchRow($select);

                                if ($fbpage) {

                                    $tokenRow->oauth_token = $fbpage->access_token; // :)
                                    $service->postAction($tokenRow, $action, $viewer);

                                }

                            } else {

                                $service->postAction($tokenRow, $action, $viewer);


                            }


                        }
                    }

                    if ($action) {
                        Engine_Api::_()->getDbTable('userSettings', 'wall')->saveLastPrivacy($action, $this->_getParam('privacy'), $viewer);
                    }


                } catch (Exception $e) {
                }

            }


            $db->commit();
        } // end "try"
        catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->message = $e->getMessage();
            return;
        }


        // If we're here, we're done
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Success!');
        if ($action) {
            $mod = $this->_script_module;
            if ($this->getRequest()->getParam('pinfeed') == 1) {
                $mod = 'pinfeed';
            }

            $this->view->body = $this->view->wallActivity($action, array(
                'comment_pagination' => $this->_getParam('comment_pagination'),
                'module' => $mod
            ));
            $this->view->last_id = $action->getIdentity();
            $this->view->last_date = $action->date;
        }

        // Check if action was created
        $post_fail = "";
        if (!$action) {
            $post_fail = "?pf=1";
        }
        //redirect if from pinfeed

        // Redirect if in normal context


        if (null === $this->_helper->contextSwitch->getCurrentContext()) {

            $return_url = $form->getValue('return_url', false);

            if ($return_url) {
                return $this->_helper->redirector->gotoUrl($return_url . $post_fail, array('prependBase' => false));
            }
        }
    }


    public function likeAction()
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Collect params
        $checkin = $this->_getParam('checkin', false);
        $pinfeed = $this->_getParam('pinfeed', false);
        $action_id = $this->_getParam('action_id');
        $comment_id = $this->_getParam('comment_id');
        $viewer = $this->_helper->api()->user()->getViewer();

        // Start transaction
        $db = $this->_helper->api()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);
            // Action
            if (!$comment_id) {

                // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment')) {
                    throw new Engine_Exception('This user is not allowed to like this item');
                }

                $action->likes()->addLike($viewer);

                // Add notification for owner of activity (if user and not viewer)
                if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                    $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);

                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
                        'label' => 'post'
                    ));
                }

            } // Comment -----------------------------------------------
            else {
                $comment = $action->comments()->getComment($comment_id);
                $reply = 0;
                //Check comment of reply
                if ($comment && $this->_getParam('rev', false) && $this->_getParam('rev') == 'reply') {
                    switch (get_class($comment)) {
                        case 'Activity_Model_Comment' :
                            $commentType = $comment->poster_type;
                            break;
                        case 'Core_Model_Comment' :
                            $commentType = $comment->resource_type;
                            break;

                    }
                    $reply = 1;
                }
                // Check authorization
                if (!$comment && $comment->poster_type != 'comment') {
                    if (!$comment) {
                        if ($comment_id) {
                            try {
                                $comment = $this->comments($action)->getComment($comment_id);
                                $reply = 1;
                            } catch (Exception $e) {
                                print_die($e . '');
                            }
                        }
                    }
                    if (!$comment && $comment->poster_type != 'comment') {
                        if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment->getAuthorizationItem(), null, 'comment')) {
                            throw new Engine_Exception('This user is not allowed to like this item');
                        }
                    }
                }

                // Liking
                if ($reply && $commentType == 'comment') {
                    Engine_Api::_()->wall()->addLikeReplyComment($comment, $viewer);
                } else {
                    $comment->likes()->addLike($viewer);
                }


                // @todo make sure notifications work right
              $ch = 0;
                if ($comment->poster_id != $viewer->getIdentity()) {
                 if($comment->poster_type=='comment'){$comment->poster_type = 'user'; $ch=1;}
                    Engine_Api::_()->getDbtable('notifications', 'activity')
                        ->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array(
                            'label' => 'comment'
                        ));
                 if($ch == 1) $comment->poster_type = 'comment';
                }

                // Add notification for owner of activity (if user and not viewer)
                if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                    $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);

                }
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->message = $e->getMessage();
            return;
        }

        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You now like this action.');

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);

        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
            if ($pinfeed) {
                $module = 'pinfeed';
            } else {
                $module = $this->_script_module;
            }
            $this->view->body = $this->view->wallActivity($action, array(
                'checkin' => $checkin,
                'noList' => true,
                'comment_pagination' => $this->_getParam('comment_pagination'),
                'module' => $module,
            ));

        }
    }

    public function unlikeAction()
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Collect params
        $checkin = $this->_getParam('checkin', false);
        $pinfeed = $this->_getParam('pinfeed', false);
        $action_id = $this->_getParam('action_id');
        $comment_id = $this->_getParam('comment_id');
        $viewer = $this->_helper->api()->user()->getViewer();

        // Start transaction
        $db = $this->_helper->api()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);

            // Action
            if (!$comment_id) {

                // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment')) {
                    throw new Engine_Exception('This user is not allowed to unlike this item');
                }

                $action->likes()->removeLike($viewer);
            } // Comment-------------------------------------
            else {
                $comment = $action->comments()->getComment($comment_id);
                $reply = 0;

                //Check comment of reply
                if ($comment && $this->_getParam('rev', false) && $this->_getParam('rev') == 'reply') {
                    switch (get_class($comment)) {
                        case 'Activity_Model_Comment' :
                            $commentType = $comment->poster_type;
                            break;
                        case 'Core_Model_Comment' :
                            $commentType = $comment->resource_type;
                            break;
                    }
                    $reply = 1;
                }

                if (!$comment && $comment->poster_type != 'comment') {
                    if (!$comment) {
                        if ($comment_id) {
                            try {
                                $comment = $this->comments($action)->getComment($comment_id);
                                $reply = 1;
                            } catch (Exception $e) {
                                print_die($e . '');
                            }
                        }
                    }
                    if (!$comment && $comment->poster_type != 'comment') {
                        if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment->getAuthorizationItem(), null, 'comment')) {
                            throw new Engine_Exception('This user is not allowed to like this item');
                        }
                    }
                }

                //Unliking
                if ($reply && $commentType == 'comment') {
                    Engine_Api::_()->wall()->removeLikeReplyComment($comment, $viewer);
                } else {
                    $comment->likes()->removeLike($viewer);
                }

            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->message = $e->getMessage();
            return;
        }

        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You no longer like this action.');

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
            if ($pinfeed) {
                $module = 'pinfeed';
            } else {
                $module = $this->_script_module;
            }
            $this->view->body = $this->view->wallActivity($action, array(
                'checkin' => $checkin,
                'noList' => true,
                'comment_pagination' => $this->_getParam('comment_pagination'),
                'module' => $module,
            ));
        }
    }

    public function viewcommentAction()
    {
        // Collect params
        $action_id = $this->_getParam('action_id');
        $viewer = $this->_helper->api()->user()->getViewer();

        $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);
        $form = $this->view->form = new Wall_Form_Comment();
        $form->setActionIdentity($action_id);


        // Redirect if not json context
        if (null === $this->_getParam('format', null)) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_getParam('format', null)) {
            $this->view->body = $this->view->wallActivity($action, array(
                'viewAllComments' => true,
                'noList' => $this->_getParam('nolist', false),
                'comment_pagination' => $this->_getParam('comment_pagination'),
                'module' => $this->_script_module,
            ));
        }
    }

    public function commentAction()
    {
        // Check license
        $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
        $product_result = $hecoreApi->checkProduct('wall');
        if (isset($product_result['result']) && !$product_result['result']) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Your license invalid!');
            $this->view->headScript()->appendScript($product_result['script']);
            return;

        }
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Make form
        $this->view->form = $form = new Wall_Form_Comment();

        // Not post
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not a post');
            return;
        }

        // Not valid
        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            $this->view->html = $form->render();
            return;
        }

        $viewer = $this->_helper->api()->user()->getViewer();
        $checkin = $this->_getParam('checkin', false);
        $pinfeed = $this->_getParam('pinfeed', false);
        $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
        $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);
        $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
        $img = $this->_getParam('img', 0);
        if ($img) {
            $body = $form->getValue('body') . ' <br>' . $img;
        } else {
            $body = $form->getValue('body');
        }
        $moduleTable = Engine_Api::_()->getDbTable('modules', 'core');


        $body = Engine_Api::_()->getApi('core', 'wall')->TagPeople($body, $viewer, $action);


        if ($this->_getParam('is_edit')) {
            $comments = $action->comments()->getReceiver();
            try {
                $comments->update(array('body' => $body),
                    array(
                        'comment_id = ?' => $this->_getParam('is_edit'))
                );
                if ($moduleTable->isModuleEnabled('hashtag')) {
                    Engine_Api::_()->getApi('core', 'hashtag')->createHashtag($body, $action, $this->_getParam('is_edit'));
                }
            } catch (Exception $e) {
                $this->view->status = false;
                $this->view->message = $e->getMessage();
                return;
            }

        } else
            if ($this->_getParam('is_reply')) {
                $proxyTable = $action->comments();
                if ($proxyTable instanceof Engine_ProxyObject) {
                    $table = $proxyTable->getReceiver();
                }
                try {
                    $comments = $table->createRow();

                    $subjectOwner = Engine_Api::_()->user()->getUser($this->_getParam('to_user'));
                    if (get_class($table) == 'Core_Model_DbTable_Comments') {
                        $comments->setFromArray(
                            array(
                                'resource_type' => 'comment',
                                'resource_id' => $this->_getParam('comment_id'),
                                'poster_type' => $viewer->getType(),
                                'poster_id' => $viewer->getIdentity(),
                                'body' => $body,
                                'creation_date' => date('Y-m-d H:i:s'),
                            )
                        );

                    }
                    if (get_class($table) == 'Activity_Model_DbTable_Comments') {
                        $comments->setFromArray(array(
                            'resource_id' => $this->_getParam('comment_id'),
                            'poster_type' => 'comment',
                            'poster_id' => $viewer->getIdentity(),
                            'body' => $body,
                            'creation_date' => date('Y-m-d H:i:s'),
                        ));
                    }
                    $comments->save();
                    // Notifications
                    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');


                    // Add notification for owner of activity (if user and not viewer)
                    if($subjectOwner->getIdentity() != $viewer->getIdentity()) {
                        $notifyApi->addNotification($subjectOwner, $viewer, $action, 'he_reply_commented', array(
                            'label' => 'post'
                        ));
                    }

                    if ($moduleTable->isModuleEnabled('hashtag')) {
                        Engine_Api::_()->getApi('core', 'hashtag')->createHashtag($body, $action, $this->_getParam('is_edit'));
                    }
                } catch (Exception $e) {
                    $this->view->status = false;
                    $this->view->message = $e->getMessage();
                    return;
                }

            } else {
                // Start transaction
                $db = $this->_helper->api()->getDbtable('actions', 'wall')->getAdapter();
                $db->beginTransaction();

                try {
                    // Check authorization
                    if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'))
                        throw new Engine_Exception('This user is not allowed to comment on this item.');

                    // Add the comment
                    $comment = $action->comments()->addComment($viewer, $body);
                    if ($moduleTable->isModuleEnabled('hashtag')) {
                        Engine_Api::_()->getApi('core', 'hashtag')->createHashtag($body, $action, $comment->getIdentity());
                    }

                    // Notifications
                    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

                    // Add notification for owner of activity (if user and not viewer)
                    if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                        $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
                            'label' => 'post'
                        ));
                    }

                    // Add a notification for all users that commented or like except the viewer and poster
                    // @todo we should probably limit this
                    foreach ($action->comments()->getAllCommentsUsers() as $notifyUser) {
                        if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                            $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
                                'label' => 'post'
                            ));
                        }
                    }

                    // Add a notification for all users that commented or like except the viewer and poster
                    // @todo we should probably limit this
                    foreach ($action->likes()->getAllLikesUsers() as $notifyUser) {
                        if ($notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                            $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
                                'label' => 'post'
                            ));
                        }
                    }

                    // Stats
                    Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    $this->view->status = false;
                    $this->view->message = $e->getMessage();
                    return;
                }
            }

        // Assign message for json
        $this->view->status = true;
        $this->view->message = 'Comment posted';


        // Redirect if not json
        if (null === $this->_getParam('format', null)) {
            $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
        } else if ('json' === $this->_getParam('format', null)) {
            if ($pinfeed) {
                $module = 'pinfeed';
            } else {
                $module = ($this->_getParam('is_timeline', false)) ? 'timeline' : 'wall';;
            }
            $this->view->body = $this->view->wallActivity($action, array(
                'checkin' => $checkin,
                'noList' => true,
                'comment_pagination' => $this->_getParam('comment_pagination'),
                'module' => $module,
            ));
            $this->view->id = ($action) ? $action->getIdentity() : 0;
        }
    }

    public function shareAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;

        $this->view->type = $type = $this->_getParam('type');
        $this->view->id = $id = $this->_getParam('id');

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->attachment = $attachment = Engine_Api::_()->getItem($type, $id);
        $this->view->form = $form = new Wall_Form_Share();

        $this->view->services = array_keys(Engine_Api::_()->wall()->getManifestType('wall_service'));

        if (!$attachment) {
            // tell smoothbox to close
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
            $this->view->smoothboxClose = true;
            return $this->render('deletedItem');
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
            return;
        }

        // Process

        $db = Engine_Api::_()->getDbtable('actions', 'wall')->getAdapter();
        $db->beginTransaction();

        try {
            // Get body
            $body = $form->getValue('body');
            // Set Params for Attachment
            $params = array(
                'type' => '<a href="' . $attachment->getHref() . '">' . $attachment->getMediaType() . '</a>',
            );

            // Add activity
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            //$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
            $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $params);
            if ($action) {
                $api->attachActivity($action, $attachment);
            }
            $db->commit();

            // Notifications
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity()) {
                $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
                    'label' => $attachment->getMediaType(),
                ));
            }

            $tableToken = Engine_Api::_()->getDbTable('tokens', 'wall');
            $stream_services = $this->_getParam('share');

            foreach ($stream_services as $provider => $enabled) {

                if (!$enabled) {
                    continue;
                }
                $tokenRow = $tableToken->getUserToken($viewer, $provider);
                if (!$tokenRow) {
                    continue;
                }
                $service = Engine_Api::_()->wall()->getServiceClass($provider);
                if (!$service->check($tokenRow)) {
                    continue;
                }
                $service->postAction($tokenRow, $action, $viewer);

            }


        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->message = $e->getMessage();
            return;
        }

        // If we're here, we're done
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Success!');

        // Redirect if in normal context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $return_url = $form->getValue('return_url', false);
            if (!$return_url) {
                $return_url = $this->view->url(array(), 'default', true);
            }
            return $this->_helper->redirector->gotoUrl($return_url, array('prependBase' => false));
        } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
    }

    function deleteAction()
    {
        $moduleTable = Engine_Api::_()->getDbTable('modules', 'core');
        $viewer = Engine_Api::_()->user()->getViewer();
        $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

        if (!$this->_helper->requireUser()->isValid()) return;

        // Identify if it's an action_id or comment_id being deleted
        $checkin = $this->_getParam('checkin', false);
        $pinfeed = $this->_getParam('pinfeed', false);
        $this->view->comment_id = $comment_id = $this->_getParam('comment_id', null);
        $this->view->action_id = $action_id = $this->_getParam('action_id', null);

        $this->view->result = false;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');

        $action = Engine_Api::_()->getDbtable('actions', 'wall')->getActionById($action_id);
        if (!$action) {
            return;
        }

        // Send to view script if not POST
        if (!$this->getRequest()->isPost())
            return;


        // Both the author and the person being written about get to delete the action_id
        if (!$comment_id && (
                $activity_moderate ||
                ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
                ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id))
        )   // commenter
        {
            // Delete action item and all comments/likes
            $db = Engine_Api::_()->getDbtable('actions', 'wall')->getAdapter();
            $db->beginTransaction();
            try {
                $action->deleteItem();
                $db->commit();
                if ($moduleTable->isModuleEnabled('hashtag')) {
                    $new = Engine_Api::_()->getDbTable('maps', 'hashtag');
                    $select_b = $new->select()->where('resource_id = ?', $action_id);
                    $tag = $new->fetchRow($select_b);
                    if ($tag->map_id > 0) {
                        $tags_hash = Engine_Api::_()->getDbTable('tags', 'hashtag');
                        $map = $tags_hash->fetchRow($tags_hash->select()->where('map_id = ?', $tag->map_id));
                        $map->delete();
                    }
                    $new->delete(array('resource_id = ?' => $action_id));
                }
                $this->view->result = true;
                $this->view->message = Zend_Registry::get('Zend_Translate')->_('This activity item has been removed.');

                return;

            } catch (Exception $e) {
                $db->rollback();
            }

        } elseif ($comment_id) {
            $comment = $action->comments()->getComment($comment_id);
            $reply = 0;
            if (!$comment) {
                $comment = $this->comments($action)->getComment($comment_id);
                $reply = 1;
            }
            if ($comment && $this->_getParam('rev', false) && $this->_getParam('rev') == 'reply') {
                $reply = 1;
            }
            $resource = $action->comments()->getSender();

            // allow delete if profile/entry owner
            $db = $action->comments()->getReceiver()->getAdapter(); //Get adapter
            $db->beginTransaction();
            if ($activity_moderate ||
                ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
                ('comment' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
                ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id)
            ) {
                try {

                    if ($reply) {
                        Engine_Api::_()->wall()->removeReplyComment($resource, $comment); //Remove Reply Comment count not --
                    } else {
                        $action->comments()->removeComment($comment_id);  //Remove Comment
                    }

                    $db->commit();
                    $this->view->result = true;
                    if ($moduleTable->isModuleEnabled('hashtag')) {
                        $new = Engine_Api::_()->getDbTable('maps', 'hashtag');
                        $select_b = $new->select()->where('resource_id = ?', $action_id)->where('comment=?', $comment_id);
                        $tag = $new->fetchRow($select_b);
                        if ($tag->map_id > 0) {
                            $tags_hash = Engine_Api::_()->getDbTable('tags', 'hashtag');
                            $map = $tags_hash->fetchRow($tags_hash->select()->where('map_id = ?', $tag->map_id));
                            if ($map->tag_id > 0) {
                                $map->delete();
                            }
                            $tag->delete();
                        }

                    }
                    if ($pinfeed) {
                        $module = 'pinfeed';
                    } else {
                        $module = $this->_script_module;
                    }

                    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment has been deleted ');
                    $this->view->body = $this->view->wallActivity($action, array(
                        'checkin' => $checkin,
                        'noList' => true,
                        'comment_pagination' => $this->_getParam('comment_pagination'),
                        'module' => $module,
                    ));
                    return;
                } catch (Exception $e) {
                    $db->rollback();
                }
            } else {
                $this->view->message = Zend_Registry::get('Zend_Translate')->_('You do not have the privilege to delete this comment');
                return;
            }
        }

    }

    public function getLikesAction()
    {
        $action_id = $this->_getParam('action_id');
        $comment_id = $this->_getParam('comment_id');


        if (!$action_id ||
            !$comment_id ||
            !($action = Engine_Api::_()->getItem('activity_action', $action_id)) ||
            !($comment = $action->comments()->getComment($comment_id))
        ) {
            $this->view->status = false;
            $this->view->body = '-';
            return;
        }

        $likes = $comment->likes()->getAllLikesUsers();
        $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
            count($likes)), strip_tags($this->view->fluentList($likes)));
        $this->view->status = true;
    }


    public function viewAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbTable('actions', 'wall');

        $this->view->action = $action = $table->getPageAction($viewer, (int)$this->_getParam('id'));
        $this->view->comment_pagination = true;
        $this->view->comment_page = (int)$this->_getParam('comment_page');
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', $this->_getParam('show_likes', false));


        if ($action && $action->getObject()) {
            Engine_Api::_()->core()->setSubject($action->getObject());
        }


        // Instance
        $unique = rand(11111, 99999);
        $this->view->feed_uid = 'wall_' . $unique;

        if ($this->_getParam('format') == 'json') {

            if ($action) {

                $activity_moderate = null;
                $viewer = Engine_Api::_()->user()->getViewer();
                if ($viewer->getIdentity()) {
                    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
                        ->getAllowed('user', $viewer->level_id, 'activity');
                }

                $form = new Wall_Form_Comment();
                $formreply = new Wall_Form_Reply();
                $this->view->assign(array(
                    'actions' => array($action),
                    'itemAction' => true,
                    'commentForm' => $form,
                    'commentFormReply' => $formreply,
                    'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
                    'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
                    'activity_moderate' => $activity_moderate,
                    'viewAllLikes' => $this->view->viewAllLikes
                ));
            }

            $this->view->result = true;
            $this->view->html = $this->view->render('_comments.tpl');

            return;

        }

        $this->_helper->content
            //->setNoRender()
            ->setEnabled();

    }

    public function serviceShareAction()
    {
        $provider = $this->_getParam('provider');
        $viewer = Engine_Api::_()->user()->getViewer();

        $setting_key = 'share_' . $provider . '_enabled';

        $setting = Engine_Api::_()->wall()->getUserSetting($viewer);

        if (isset($setting->{$setting_key})) {
            $setting->setFromArray(array($setting_key => (int)$this->_getParam('status', 0)));
            $setting->save();
        }
    }


    public function servicesRequestAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return;
        }

        foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service) {
            if ($this->_getParam($service)) {
                $this->view->$service = false;
                $class = Engine_Api::_()->wall()->getServiceClass($service);
                if (!$class) {
                    continue;
                }
                $token = Engine_Api::_()->getDbTable('tokens', 'wall')->getUserToken($viewer, $service);
                if (!$token) {
                    continue;
                }
                if (!$token->check()) {
                    continue;
                }
                $data = array_merge(array('enabled' => true), $token->publicArray());

                if ($service == 'facebook') {
                    $data['fb_pages'] = $class->getPages($token);
                }

                $this->view->$service = $data;
            }
        }

    }


    public function suggestAction()
    {

        $select = Engine_Api::_()->wall()->getTagSuggest(Engine_Api::_()->user()->getViewer(), array('search' => $this->_getParam('value')));
        $paginator = Zend_Paginator::factory($select);


        $data = array();

        $paginator->setItemCountPerPage(50);
        foreach (Engine_Api::_()->wall()->getItems($paginator->getCurrentItems()) as $item) {
            $data[] = array(
                'type' => $item->getType(),
                'id' => $item->getIdentity(),
                'guid' => $item->getGuid(),
                'label' => $item->getTitle(),
                'photo' => $this->view->itemPhoto($item, 'thumb.icon'),
                'url' => $item->getHref(),
            );
        }

        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }

    }

    public function suggestuserAction()
    {

        $select = Engine_Api::_()->wall()->getTagSuggest(Engine_Api::_()->user()->getViewer(), array('search' => $this->_getParam('value')));
        $paginator = Zend_Paginator::factory($select);


        $data = array();

        $paginator->setItemCountPerPage(50);
        foreach (Engine_Api::_()->wall()->getItems($paginator->getCurrentItems()) as $item) {
            if ($item->getType() == 'user')
                $data[] = array(
                    'type' => $item->getType(),
                    'id' => $item->getIdentity(),
                    'guid' => $item->getGuid(),
                    'label' => $item->getTitle(),
                    'photo' => $this->itemPhoto($item, 'thumb.icon'),
                    'url' => $item->getHref(),
                    'username' => $item->username,
                );
        }

        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }

    }


    public function suggestPeopleAction()
    {
        $select = Engine_Api::_()->wall()->getSuggestPeople(Engine_Api::_()->user()->getViewer(), array('search' => $this->_getParam('value')));
        $paginator = Zend_Paginator::factory($select);


        $data = array();

        $paginator->setItemCountPerPage(50);
        foreach (Engine_Api::_()->wall()->getItems($paginator->getCurrentItems()) as $item) {
            $data[] = array(
                'type' => $item->getType(),
                'id' => $item->getIdentity(),
                'guid' => $item->getGuid(),
                'label' => $item->getTitle(),
                'photo' => $this->view->itemPhoto($item, 'thumb.icon'),
                'url' => $item->getHref(),
            );
        }

        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }

    }


    public function changePrivacyAction()
    {
        $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));

        if (!$action || !$action->canChangePrivacy(Engine_Api::_()->user()->getViewer())) {
            return;
        }

        $action->changePrivacy($this->_getParam('privacy'));

    }


    public function muteAction()
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Collect params
        $checkin = $this->_getParam('checkin', false);

        $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
        if (!$action) {
            return;
        }
        $table = Engine_Api::_()->getDbTable('mute', 'wall');
        $viewer = Engine_Api::_()->user()->getViewer();

        $select = $table->select()
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('action_id = ?', $action->getIdentity());

        $mute = $table->fetchRow($select);

        if (!$mute) {

            $mute = $table->createRow();
            $mute->setFromArray(array(
                'user_id' => $viewer->getIdentity(),
                'action_id' => $action->getIdentity()
            ));
            $mute->save();

        }

    }

    public function unmuteAction()
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Collect params
        $checkin = $this->_getParam('checkin', false);

        $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
        if (!$action) {
            return;
        }
        $table = Engine_Api::_()->getDbTable('mute', 'wall');
        $viewer = Engine_Api::_()->user()->getViewer();

        $select = $table->select()
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('action_id = ?', $action->getIdentity());

        $mute = $table->fetchRow($select);

        if ($mute) {
            $mute->delete();
        }


        $this->view->status = true;

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->view->body = $this->view->wallActivity($action, array(
                'checkin' => $checkin,
                'noList' => true,
                'comment_pagination' => $this->_getParam('comment_pagination'),
                'module' => $this->_script_module,
            ));
        }

    }


    public function removeTagAction()
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Collect params
        $checkin = $this->_getParam('checkin', false);

        $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
        if (!$action) {
            return;
        }
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$action->canRemoveTag($viewer)) {
            return;
        }

        $table = Engine_Api::_()->getDbTable('tags', 'wall');

        $select = $table->select()
            ->where('action_id = ?', $action->getIdentity())
            ->where('object_type = ?', $viewer->getType())
            ->where('object_id = ?', $viewer->getIdentity());

        foreach ($table->fetchAll($select) as $tag) {
            $tag->delete();
        }

        $this->view->status = true;

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->view->body = $this->view->wallActivity($action, array(
                'checkin' => $checkin,
                'noList' => true,
                'comment_pagination' => $this->_getParam('comment_pagination'),
                'module' => $this->_script_module,
            ));
        }
    }

    public function albumAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($this->_getParam('do')) {

            $files = $this->_getParam('photos_id_del');
            if (is_string($files)) {
                $temp = explode(',', $files);
                if ($temp > 1) {
                    $files = $temp;
                }
            }
            if (count($files) <= 0) {
                return;
            }
            if (count($files) == 1) {
                $files = array(
                    0 => $files
                );
            }
            $eneble_all = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('advalbum');
            $i = 0;
            foreach ($files as $key => $file) {

                if ($eneble_all) {
                    $photo = Engine_Api::_()->getItem('advalbum_photo', $file);
                } else {
                    $photo = Engine_Api::_()->getItem('album_photo', $file);
                }
                if (!$photo) {
                    continue;
                }
                if (!$photo->isOwner(Engine_Api::_()->user()->getViewer())) {
                    die('false');
                }
                try {
                    // delete photo
                    if ($eneble_all) {
                        $album = Engine_Api::_()->getDbtable('photos', 'advalbum');
                    } else {
                        $album = Engine_Api::_()->getDbtable('photos', 'album');
                    }

                    $album->delete(array('photo_id = ?' => $photo->photo_id));

                    // delete files from server
                    $filesDB = Engine_Api::_()->getDbtable('files', 'storage');

                    $filePath = $filesDB->fetchRow($filesDB->select()->where('file_id = ?', $photo->file_id))->storage_path;
                    unlink($filePath);

                    $thumbPath = $filesDB->fetchRow($filesDB->select()->where('parent_file_id = ?', $photo->file_id))->storage_path;
                    unlink($thumbPath);

                    // Delete image and thumbnail
                    $filesDB->delete(array('file_id = ?' => $photo->file_id));
                    $filesDB->delete(array('parent_file_id = ?' => $photo->file_id));

                    // Check activity actions
                    $attachDB = Engine_Api::_()->getDbtable('attachments', 'activity');
                    $actions = $attachDB->fetchAll($attachDB->select()->where('type = ?', 'album_photo')->where('id = ?', $photo->photo_id));
                    $actionsDB = Engine_Api::_()->getDbtable('actions', 'activity');

                    foreach ($actions as $action) {
                        $action_id = $action->action_id;
                        $attachDB->delete(array('type = ?' => 'album_photo', 'id = ?' => $photo->photo_id));

                        $action = $actionsDB->fetchRow($actionsDB->select()->where('action_id = ?', $action_id));
                        $count = $action->params['count'];
                        if (!is_null($count) && ($count > 1)) {
                            $action->params = array('count' => (integer)$count - 1);
                            $action->save();
                        } else {
                            $actionsDB->delete(array('action_id = ?' => $action_id));
                        }
                    }
                } catch (Exception $e) {
                    print_die($e . '');
                    throw $e;
                }
                $i++;
            }
            die('true');
            return;
        }
        if ($this->_getParam('user_album')) {
            $p = $this->_getParam('p');
            if ($p == 'adv') {
                $album = Engine_Api::_()->getItem('advalbum_album', $this->_getParam('user_album'));
            } else {
                $album = Engine_Api::_()->getItem('album', $this->_getParam('user_album'));
            }
        } else {
            $params = Array();
            $params['owner_id'] = $viewer->getIdentity();
            $params['owner_type'] = 'user';
            $params['title'] = htmlspecialchars($this->_getParam('title'));
            $params['category_id'] = 0;
            $params['description'] = htmlspecialchars($this->_getParam('desc'));
            $params['search'] = 1;
            $p = $this->_getParam('p');
            if ($p == 'adv') {
                $album = Engine_Api::_()->getDbtable('albums', 'advalbum')->createRow();
            } else {
                $album = Engine_Api::_()->getDbtable('albums', 'album')->createRow();
            }
            $album->setFromArray($params);
            $album->save();

            $auth = Engine_Api::_()->authorization()->context;
            $values = array();
            $roles = array(
                'owner',
                'owner_member',
                'owner_member_member',
                'owner_network',
                'everyone'
            );
            $set_cover = true;
            $order_number = 0;
            $auth_view = $this->_getParam('privacy');
            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
            }
            $auth_comment = $values['auth_comment'] = "everyone";
            $commentMax = array_search($values['auth_comment'], $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
            }
        }

        $files = $this->_getParam('photo_ids');

        $values['file'] = $this->_getParam('photo_ids');
        $api = Engine_Api::_()->getDbtable('actions', 'activity');

        if ($p == 'adv') {
            $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'advalbum_photo_new', null, array('count' => count($values['file'])));
        } else {
            $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['file'])));
        }
        $cover = 0;
        foreach ($values['file'] as $photo) {
            if ($cover <= 0) {
                if ($photo[2] == 1) {
                    $cover = $photo[0];
                } else {
                    $cover = 0;
                }
            }
        }
        $count = 0;
        $check = array();
        foreach ($values['file'] as $photo) {
            if (in_array($photo[0], $check)) {
                continue;
            }
            $check[$count] = $photo[0];
            $photo_id = $photo[0];
            $order = $photo[1]++;
            if (!$photo_id)
                continue;
            if ($p == 'adv') {
                $photo = Engine_Api::_()->getItem("advalbum_photo", trim($photo_id));
            } else {
                $photo = Engine_Api::_()->getItem("album_photo", trim($photo_id));
            }

            if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
                continue;

            if ($set_cover) {
                if ($cover > 0) {
                    $album->photo_id = $cover;
                } else {
                    $album->photo_id = $photo_id;
                }

                $album->save();
                $set_cover = false;
            }

            $photo->album_id = $album->album_id;
            $photo->order = $order;

            $photo->save();

            if ($action instanceof Activity_Model_Action && $count < 8) {
                $cty = $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
            }
            $count++;
        }

        $this->view->last_id = $action->getIdentity();

    }

    public function titlephotoAction()
    {
        $title = $this->_getParam('name');
        $id = $this->_getParam('id');
        $eneble_all = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('advalbum');
        if ($eneble_all) {
            $photo = Engine_Api::_()->getItem('advalbum_photo', $id);
        } else {
            $photo = Engine_Api::_()->getItem('album_photo', $id);
        }
        if (!$photo) {
            die('false');
        }
        if (!$photo->isOwner(Engine_Api::_()->user()->getViewer())) {
            die('false');
        }
        $photo->title = $title;

        $photo->save();
        die($photo->title);
    }

    public function rotatephotoAction()
    {
        $id = $this->_getParam('id');
        $degrees = -90;// $this->_getParam('rotate');
        $eneble_all = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('advalbum');
        if ($eneble_all) {
            $photo = Engine_Api::_()->getItem('advalbum_photo', $id);
        } else {
            $photo = Engine_Api::_()->getItem('album_photo', $id);
        }
        if (!$photo) {
            die('false');
        }
        if (!$photo->isOwner(Engine_Api::_()->user()->getViewer())) {
            die('false');
        }

        $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
        if (!$file) {
            throw new Exception('File is not available');
        }


        $tmpFile = $file->temporary();

        // Operate on the file
        $image = Engine_Image::factory();
        $image->open($tmpFile)
            ->rotate($degrees)
            ->write()
            ->destroy();

        // Set the photo
        $db = $photo->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $photo->setPhoto($tmpFile);
            @unlink($tmpFile);
            $db->commit();
        } catch (Exception $e) {
            @unlink($tmpFile);
            $db->rollBack();
            throw $e;
        }


        die($photo->getPhotoUrl());
        return 'true';
    }

    public function scloudAction()
    {
        $client = Engine_Api::_()->wall()->getServiceClass('soundcloud');

        $client->setCurlOptions(array(CURLOPT_FOLLOWLOCATION => 1));
        $session = new Zend_Session_Namespace("wall_service_soundcloud_token");
        $client->setAccessToken($session->token);
// get a tracks oembed data
        $track_url = $this->_getParam('url');

        $embed_info = json_decode($client->get('oembed', array('url' => $track_url)));

// render the html for the player widget
        print $embed_info->html;
        die();

    }

    public function commentphotoAction()
    {

        if (empty($_FILES['photo_comment'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }

        // Get album
        $viewer = Engine_Api::_()->user()->getViewer();
        $eneble_all = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('advalbum');
        if ($eneble_all) {
            $table = Engine_Api::_()->getDbtable('albums', 'advalbum');
        } else {
            $table = Engine_Api::_()->getDbtable('albums', 'album');
        }

        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $type = 'wall';

            $album = $table->getSpecialAlbum($viewer, $type);

            if ($eneble_all) {
                $photoTable = Engine_Api::_()->getDbtable('photos', 'advalbum');
            } else {
                $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
            }
            $photo = $photoTable->createRow();
            $photo->setFromArray(array(
                'owner_type' => 'user',
                'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
            ));
            $photo->save();
            $photo = $this->setPhoto($photo, $_FILES['photo_comment']);

            if ($type == 'comment') {
                $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
            }

            $photo->album_id = $album->album_id;
            $photo->save();

            if (!$album->photo_id) {
                $album->photo_id = $photo->getIdentity();
                $album->save();
            }

            if ($type != 'wall') {
                // Authorizations
                $auth = Engine_Api::_()->authorization()->context;
                $auth->setAllowed($photo, 'everyone', 'view', true);
                $auth->setAllowed($photo, 'everyone', 'comment', true);
            }

            $db->commit();

            $this->view->status = true;
            $this->view->photo_id = $photo->photo_id;
            $this->view->album_id = $album->album_id;
            $this->view->src = $photo->getPhotoUrl();
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Photo saved successfully');
        } catch (Exception $e) {

            $db->rollBack();
            //throw $e;
            $this->view->status = false;
        }

        $photo_element = '<a href="' . $photo->getHref() . '" class="comment_photo"><img   src="' . $photo->getPhotoUrl('thumb.profile') . '" class="img" /></a>';
        die($photo_element);

    }

    public function setPhoto($this_photo, $photo)
    {

        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = $file;
        }

        $name = basename($file);
        $extension = ltrim(strrchr($fileName, '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => $this_photo->getType(),
            'parent_id' => $this_photo->getIdentity(),
            'user_id' => $this_photo->owner_id,
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(720, 720)
            ->write($mainPath)
            ->destroy();

        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(140, 160)
            ->write($normalPath)
            ->destroy();

        // Store
        try {
            $iMain = $filesTable->createFile($mainPath, $params);
            $iIconNormal = $filesTable->createFile($normalPath, $params);

            $iMain->bridge($iIconNormal, 'thumb.normal');
        } catch (Exception $e) {
            // Remove temp files
            @unlink($mainPath);
            @unlink($normalPath);
            // Throw
            if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
                throw new Album_Model_Exception($e->getMessage(), $e->getCode());
            } else {
                throw $e;
            }
        }

        // Remove temp files
        @unlink($mainPath);
        @unlink($normalPath);

        // Update row
        $this_photo->modified_date = date('Y-m-d H:i:s');
        $this_photo->file_id = $iMain->file_id;
        $this_photo->save();

        // Delete the old file?
        if (!empty($tmpRow)) {
            $tmpRow->delete();
        }

        return $this_photo;
    }

    public function welcometabAction()
    {

        $page_table = Engine_Api::_()->getDbTable('pages', 'core');
        $content_table = Engine_Api::_()->getDbTable('content', 'core');

        $select = $content_table->select()
            ->from(array('p' => $page_table->info('name')), array('c.content_id'))
            ->joinLeft(array('c' => $content_table->info('name')), 'c.page_id = p.page_id AND c.name = "middle"', array())
            ->where('p.name = ?', 'wall_index_welcome');

        $content_id = 0;
        $content = $content_table->fetchRow($select);
        if ($content) {
            $content_id = $content->content_id;
        }

        $select = $content_table->select()
            ->where('parent_content_id = ?', $content_id)
            ->order('order ASC');

        $widgets = $content_table->fetchAll($select);
        $layout = '';
        foreach ($widgets as $widget) {

            try {

                $page_id = $widget->page_id;
                $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
                $content = $content_table->fetchAll($content_table->select()->where('page_id = ?', $page_id));
                $structure = $page_table->createElementParams($widget);
                $children = $page_table->prepareContentArea($content, $widget);
                if (!empty($children)) {
                    $structure['elements'] = $children;
                }
                //$structure['request'] = $this->getRequest();
                //$structure['action'] = $view;

                if (!Engine_Api::_()->wall()->checkWidgetIsEnabled($structure['name'])) {
                    continue;
                }

                $element = new Engine_Content_Element_Container(array(
                    'elements' => array($structure),
                    'decorators' => array(
                        'Children'
                    )
                ));

                /*      if( !$show_container ) {
                        foreach( $element->getElements() as $cel ) {
                          $cel->clearDecorators();
                        }
                      }*/

                $content = $element->render();

                $layout .= $content;

            } catch (Exception $e) {

            }

        }
        $this->view->content = $layout;


    }

    public function comments($action)
    {
        return new Engine_ProxyObject($action, Engine_Api::_()->getDbtable('comments', 'activity'));
    }

    public function editAction()
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid()) return;

        // Collect params
        $checkin = $this->_getParam('checkin', false);
        $pinfeed = $this->_getParam('pinfeed', false);
        $action_id = $this->_getParam('action_id');

        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $this->_getParam('content');
        $body = $filter->filter($body);

        $actions_tbl = Engine_Api::_()->getDbtable('actions', 'activity');
        $moduleTable = Engine_Api::_()->getDbTable('modules', 'core');

        try {
            $actions_tbl->update(array('body' => $body),
                array(
                    'action_id = ?' => $action_id)
            );

            $action = Engine_Api::_()->getItem('activity_action', $action_id);

            if ($moduleTable->isModuleEnabled('hashtag')) {
                Engine_Api::_()->getApi('core', 'hashtag')->createHashtag($body, $action);
            }
        } catch (Exception $e) {
            $this->view->status = false;
            $this->view->message = $e->getMessage();
            return;
        }

        $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);

        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You now edit this action.');

        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            $this->_helper->redirector->gotoRoute(array(), 'default', true);

        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
            if ($pinfeed) {
                $module = 'pinfeed';
            } else {
                $module = $this->_script_module;
            }
            $this->view->body = $this->view->wallActivity($action, array(
                'checkin' => $checkin,
                'noList' => true,
                'comment_pagination' => $this->_getParam('comment_pagination'),
                'module' => $module,
            ));
        }
    }

}
