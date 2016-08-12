<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: CommentController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Hecomment_CommentController extends Core_Controller_Action_Standard
{
    protected $_url_info = array();

    public function init()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $type = $this->_getParam('type');
        $identity = $this->_getParam('id');
        if ($type && $identity) {
            $item = Engine_Api::_()->getItem($type, $identity);
            if ($item instanceof Core_Model_Item_Abstract &&
                (method_exists($item, 'comments') || method_exists($item, 'likes'))
            ) {
                if (!Engine_Api::_()->core()->hasSubject()) {
                    Engine_Api::_()->core()->setSubject($item);
                }
                //$this->_helper->requireAuth()->setAuthParams($item, $viewer, 'comment');
            }
        }

        //$this->_helper->requireUser();
        // $this->_helper->requireSubject();
        //$this->_helper->requireAuth();
    }

    public function listAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        // Perms
        $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

        // Likes
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        $this->view->likes = $likes = $subject->likes()->getLikePaginator();

        // Comments

        // If has a page, display oldest to newest
        if (null !== ($page = $this->_getParam('page'))) {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id ASC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page);
            $comments->setItemCountPerPage(10);
            $this->view->comments = $comments;
            $this->view->page = $page;
        } // If not has a page, show the
        else {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id DESC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage(4);
            $this->view->comments = $comments;
            $this->view->page = $page;
        }
        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('hecomment');
        $path = dirname($path) . '/views/scripts';
        $this->view->addScriptPath($path);
        if ($viewer->getIdentity() && $canComment) {
            $form = new Hecomment_Form_Comment();
            $reply_form = new Hecomment_Form_Reply();
            $form
                ->setIdentity($subject->getIdentity());

            $this->view->wallSmiles = Engine_Api::_()->getDbTable('smiles', 'wall')->getPaginator()->getCurrentItems();

            $this->view->form = $form;
            $this->view->subject = $subject;
            $this->view->reply_form = $reply_form;
            $form->populate(array(
                'identity' => $subject->getIdentity(),
                'type' => $subject->getType(),
            ));
        }
    }

    public function createAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $subject = Engine_Api::_()->core()->getSubject();

        $settings = Engine_Api::_()->getApi('settings', 'core');

        $notification_enable = $settings->getSetting('hecomment.notification.enabled') ? true : false;


        $this->view->form = $form = new Hecomment_Form_Comment();

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");;
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
            return;
        }

        // Process

        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $form->getValue('body');
        $body = $filter->filter($body);

        // Get first URL from body
        $links = array();
        preg_match_all('$\b(https?|ftp|file)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]$i', $body, $links, PREG_PATTERN_ORDER);

        if ($this->_getParam('img')) {
            $body .= '</br>' . $this->_getParam('img');
        }

        $body = Engine_Api::_()->getApi('core', 'wall')->TagPeople($body, $viewer, $subject);
        $body = htmlspecialchars_decode($body);

        $link = str_replace('&quot', '', $links[0][0]);

        $body .= '</br>' . $this->preview($link);

        if ($this->_getParam('is_edit')) {
            try {
                Engine_Api::_()->getDbtable('comments', 'core')->update(
                    array('body' => $body),
                    array('comment_id = ?' => $this->_getParam('is_edit'))
                );
            } catch (Exception $e) {
                $this->view->status = false;
                $this->view->message = $e->getMessage();
                return;
            }

        } else {
            if ($this->_getParam('comment_type') != 'comment') {
                $db = $subject->comments()->getCommentTable()->getAdapter();

                $db->beginTransaction();
            }
            try {
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

                if ($this->_getParam('comment_type') == 'comment') {
                    $new = Engine_Api::_()->hecomment()->addComment($this->_getParam('comment_identity'), $viewer, $body);
                    if ($new) {

                        if ($this->_getParam('comment_owner') != $viewer->getIdentity() && $notification_enable) {
                            $subjectOwner = Engine_Api::_()->user()->getUser($this->_getParam('comment_owner'));

                            $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'he_reply_commented', array(
                                'label' => $subject->getShortType()
                            ));
                        }
                    }
                } else {

                    $subject->comments()->addComment($viewer, $body);
                    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                    $subjectOwner = $subject->getOwner('user');

                    // Activity
                    $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
                        'owner' => $subjectOwner->getGuid(),
                        'body' => $body
                    ));

                    //$activityApi->attachActivity($action, $subject);

                    // Notifications

                    // Add notification for owner (if user and not viewer)
                    $this->view->subject = $subject->getGuid();
                    $this->view->owner = $subjectOwner->getGuid();
                    if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity() && $notification_enable) {
                        $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
                            'label' => $subject->getShortType()
                        ));
                    }

                    // Add a notification for all users that commented or like except the viewer and poster
                    // @todo we should probably limit this
                    $commentedUserNotifications = array();
                    foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
                        if (($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity()) && $notification_enable) continue;

                        // Don't send a notification if the user both commented and liked this
                        $commentedUserNotifications[] = $notifyUser->getIdentity();

                        $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
                            'label' => $subject->getShortType()
                        ));
                    }

                    // Add a notification for all users that liked
                    // @todo we should probably limit this
                    foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
                        // Skip viewer and owner
                        if (($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity()) && $notification_enable) continue;

                        // Don't send a notification if the user both commented and liked this
                        if (in_array($notifyUser->getIdentity(), $commentedUserNotifications)) continue;

                        $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
                            'label' => $subject->getShortType()
                        ));
                    }

                    // Increment comment count
                    Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

                    $db->commit();
                }
            } catch (Exception $e) {
                print_die($e . '');
                // $db->rollBack();
                throw $e;
            }
        }

        $this->view->status = true;
        $this->view->message = 'Comment added';
        $this->view->body = $this->view->action('list', 'comment', 'hecomment', array(
            'type' => $this->_getParam('type'),
            'id' => $this->_getParam('id'),
            'format' => 'html',
            'page' => 1,
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function deleteAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        // Comment id
        $comment_id = $this->_getParam('comment_id');
        if (!$comment_id) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment');
            return;
        }

        // Comment
        $comment = $subject->comments()->getComment($comment_id);
        if (!$comment) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment or wrong parent');
            return;
        }

        // Authorization
        if (!$subject->authorization()->isAllowed($viewer, 'edit') &&
            ($comment->poster_type != $viewer->getType() ||
                $comment->poster_id != $viewer->getIdentity())
        ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
            return;
        }

        // Method
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        // Process
        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
            if ($comment->resource_type == 'comment'){
              Engine_Api::_()->hecomment()->removeReplyComment($subject, $comment); //Remove Reply Comment
            }
            else {
              $subject->comments()->removeComment($comment_id); //Remove Comment
            }

          $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment deleted');
    }

    public function likeAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $comment_id = $this->_getParam('comment_id');

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try {
          if ($commentedItem->resource_type == 'comment'){
            Engine_Api::_()->hecomment()->addLikeReplyComment($commentedItem, $viewer);
          } else {
            $commentedItem->likes()->addLike($viewer);
          }

            // Add notification
            $owner = $commentedItem->getOwner();
            $this->view->owner = $owner->getGuid();
            if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                if ($commentedItem->getType() == 'core_comment') {
                    $commentedItem = $subject;

                }
                $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                    'label' => $commentedItem->getShortType()
                ));
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
        $this->view->body = $this->view->action('list', 'comment', 'hecomment', array(
            'type' => $type,
            'id' => $id,
            'format' => 'html',
            'page' => 1,
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function unlikeAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $comment_id = $this->_getParam('comment_id');

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try {
          if ($commentedItem->resource_type == 'comment'){
            Engine_Api::_()->hecomment()->removeLikeReplyComment($commentedItem, $viewer);
          } else {
            $commentedItem->likes()->removeLike($viewer);
          }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
        $this->view->body = $this->view->action('list', 'comment', 'hecomment', array(
            'type' => $type,
            'id' => $id,
            'format' => 'html',
            'page' => 1,
        ));
        $this->_helper->contextSwitch->initContext();
    }

    public function getLikesAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        $likes = $subject->likes()->getAllLikesUsers();
        $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
            count($likes)), strip_tags($this->view->fluentList($likes)));
        $this->view->status = true;
    }

    public function preview($url)
    {
        if (!$url && $this->url_exists($url)) return '';
        if (!$this->_helper->requireUser()->isValid()) return;
        if (!$this->_helper->requireAuth()->setAuthParams('core_link', null, 'create')->isValid()) return;

        // clean URL for html code
        $uri = trim($url);
        $uri = htmlspecialchars($uri, ENT_QUOTES);

        try {
            $client = new Zend_Http_Client($uri, array(
                'maxredirects' => 2,
                'timeout' => 10,
            ));

            // Try to mimic the requesting user's UA
            $client->setHeaders(array(
                'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                'X-Powered-By' => 'Zend Framework'
            ));

            $response = $client->request();

            // Get content-type
            list($contentType) = explode(';', $response->getHeader('content-type'));
            $this->view->contentType = $contentType;

            // Prepare

            $this->_url_info['title'] = null;
            $this->_url_info['description'] = null;
            $this->_url_info['thumb'] = null;
            $this->_url_info['medium'] = null;
            $this->_url_info['imageCount'] = 0;
            $this->_url_info['images'] = array();


            // Handling based on content-type

            switch (strtolower($contentType)) {

                // Images
                case 'image/gif':
                case 'image/jpeg':
                case 'image/jpg':
                case 'image/tif': // Might not work
                case 'image/xbm':
                case 'image/xpm':
                case 'image/png':
                case 'image/bmp': // Might not work
                    $this->_previewImage($uri, $response);
                    break;

                // HTML
                case '':
                case 'text/html':
                    $this->_previewHtml($uri, $response);
                    break;

                // Plain text
                case 'text/plain':
                    $this->_previewText($uri, $response);
                    break;

                // Unknown
                default:
                    break;
            }
        } catch (Exception $e) {
            throw $e;
        }

        $img_src = '';
        $i = 0;

        while ($i < sizeof($this->_url_info['images']) && !strlen($img_src)) {
            if ($this->image_url_exists($this->_url_info['images'][$i])) {
                $img_src = $this->_url_info['images'][$i];
            };
            $i++;
        }

        $link_title = htmlspecialchars($this->_url_info['title'], ENT_QUOTES);
        $link_description = htmlspecialchars($this->_url_info['description'], ENT_QUOTES);

        if (!strlen($img_src)) {
            $html = <<<COVER
<div class="hecomment-attached-link-body"><div class="hecomment-link-preview-info"><div class="hecomment-link-preview-title"><a href="{$uri}">{$link_title}</a></div><div class="hecomment-link-preview-description">{$link_description}</div></div></div>
COVER;
        } else {
            $html = <<<COVER
<div class="hecomment-attached-link-body"><div class="hecomment-link-preview-image"><img src="{$img_src}"></div><div class="hecomment-link-preview-info">    <div class="hecomment-link-preview-title"><a href="{$uri}">{$link_title}</a></div><div class="hecomment-link-preview-description">{$link_description}</div></div></div>
COVER;
        }

        return $html;

    }

    protected function _previewImage($uri, Zend_Http_Response $response)
    {
        $this->_url_info['imageCount'] = 1;
        $this->_url_info['images'] = array($uri);
    }

    protected function _previewText($uri, Zend_Http_Response $response)
    {
        $body = $response->getBody();
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
            preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)
        ) {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }
        if (function_exists('mb_convert_encoding')) {
            $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
        }

        // Reduce whitespace
        $body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);

        $this->_url_info['title'] = substr($body, 0, 63);
        $this->_url_info['description'] = substr($body, 0, 255);
    }

    protected function _previewHtml($uri, Zend_Http_Response $response)
    {
        $body = $response->getBody();
        $body = trim($body);
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
            preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)
        ) {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }
        if (function_exists('mb_convert_encoding')) {
            $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
        }

        // Get DOM
        if (class_exists('DOMDocument')) {
            $dom = new Zend_Dom_Query($body);
        } else {
            $dom = null; // Maybe add b/c later
        }

        $title = null;
        if ($dom) {
            $titleList = $dom->query('title');
            if (count($titleList) > 0) {
                $title = trim($titleList->current()->textContent);
                $title = substr($title, 0, 255);
            }
        }
        $this->_url_info['title'] = $title;

        $description = null;
        if ($dom) {
            $descriptionList = $dom->queryXpath("//meta[@name='description']");
            // Why are they using caps? -_-
            if (count($descriptionList) == 0) {
                $descriptionList = $dom->queryXpath("//meta[@name='Description']");
            }
            if (count($descriptionList) > 0) {
                $description = trim($descriptionList->current()->getAttribute('content'));
                $description = substr($description, 0, 255);
            }
        }
        $this->_url_info['description'] = $description;

        $thumb = null;
        if ($dom) {
            $thumbList = $dom->queryXpath("//link[@rel='image_src']");
            if (count($thumbList) > 0) {
                $thumb = $thumbList->current()->getAttribute('href');
            }
        }
        $this->_url_info['thumb'] = $thumb;

        $medium = null;
        if ($dom) {
            $mediumList = $dom->queryXpath("//meta[@name='medium']");
            if (count($mediumList) > 0) {
                $medium = $mediumList->current()->getAttribute('content');
            }
        }
        $this->_url_info['medium'] = $medium;

        // Get baseUrl and baseHref to parse . paths
        $baseUrlInfo = parse_url($uri);
        $baseUrl = null;
        $baseHostUrl = null;
        if ($dom) {
            $baseUrlList = $dom->query('base');
            if ($baseUrlList && count($baseUrlList) > 0 && $baseUrlList->current()->getAttribute('href')) {
                $baseUrl = $baseUrlList->current()->getAttribute('href');
                $baseUrlInfo = parse_url($baseUrl);
                $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
            }
        }
        if (!$baseUrl) {
            $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
            if (empty($baseUrlInfo['path'])) {
                $baseUrl = $baseHostUrl;
            } else {
                $baseUrl = explode('/', $baseUrlInfo['path']);
                array_pop($baseUrl);
                $baseUrl = join('/', $baseUrl);
                $baseUrl = trim($baseUrl, '/');
                $baseUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . $baseUrl . '/';
            }
        }

        $images = array();
        if ($thumb) {
            $images[] = $thumb;
        }
        if ($dom) {
            $imageQuery = $dom->query('img');
            foreach ($imageQuery as $image) {
                $src = $image->getAttribute('src');
                // Ignore images that don't have a src
                if (!$src || false === ($srcInfo = @parse_url($src))) {
                    continue;
                }
                $ext = ltrim(strrchr($src, '.'), '.');
                // Detect absolute url
                if (strpos($src, '/') === 0) {
                    // If relative to root, add host
                    $src = $baseHostUrl . ltrim($src, '/');
                } else if (strpos($src, './') === 0) {
                    // If relative to current path, add baseUrl
                    $src = $baseUrl . substr($src, 2);
                } else if (!empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                    // Contians host and scheme, do nothing
                } else if (empty($srcInfo['scheme']) && empty($srcInfo['host'])) {
                    // if not contains scheme or host, add base
                    $src = $baseUrl . ltrim($src, '/');
                } else if (empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                    // if contains host, but not scheme, add scheme?
                    $src = $baseUrlInfo['scheme'] . ltrim($src, '/');
                } else {
                    // Just add base
                    $src = $baseUrl . ltrim($src, '/');
                }
                // Ignore images that don't come from the same domain
                //if( strpos($src, $srcInfo['host']) === false ) {
                // @todo should we do this? disabled for now
                //continue;
                //}
                // Ignore images that don't end in an image extension
                if (!in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                    // @todo should we do this? disabled for now
                    //continue;
                }
                if (!in_array($src, $images)) {
                    $images[] = $src;
                }
            }
        }

        // Unique
        $images = array_values(array_unique($images));

        // Truncate if greater than 20
        if (count($images) > 10) {
            array_splice($images, 10, count($images));
        }

        $this->_url_info['imageCount'] = count($images);
        $this->_url_info['images'] = $images;
    }

    function url_exists($url)
    {
        if (!$fp = curl_init($url)) return false;
        return true;
    }

    function image_url_exists($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) !== false && getimagesize($url) !== false) {
            $image_size = getimagesize($url);
            if ($image_size[0] > 39 && $image_size[0] < 641 && $image_size[1] > 39 && $image_size[1] < 481) {
                return true;
            }
        }
        return false;
    }

    public function deleteImageAction()
    {
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
    }
}