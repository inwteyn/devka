<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Pagealbum_IndexController extends Core_Controller_Action_Standard
{
    protected $params;

    protected $pageObject;

    protected $album;

    public function init()
    {
        if (isset($_GET['ul']) || isset($_FILES['Filedata'])) return $this->_forward('upload-photo', null, null, array('format' => 'json'));
        if (isset($_GET['rp'])) return $this->_forward('remove-photo', null, null, array('format' => 'json'));

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('pagealbum');
        $path = dirname($path) . '/views/scripts';

        $this->view->addScriptPath($path);

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
        $path = dirname($path) . '/views/scripts';

        $this->view->addScriptPath($path);

        $page_id = $this->_getParam('page_id');
        $photo_id = $this->_getParam('photo_id');
        $album_id = $this->_getParam('album');

        if ($album_id) {
            $this->album = Engine_Api::_()->getItem('pagealbum', $album_id);
        }

        if ($photo_id) {
            $photo = Engine_Api::_()->getItem('pagealbumphoto', $photo_id);
            if (!$this->album) {
                $this->album = $photo->getCollection();
            }
            $this->view->photo = $photo;
        }


        $this->view->pageObject = $this->pageObject = $pageObject = Engine_Api::_()->getItem('page', $page_id);
        $this->view->isAllowedView = $this->getPageApi()->isAllowedView($pageObject);

        if ($photo_id != null) {
            if (!Engine_Api::_()->core()->hasSubject()) {
                if ($photo_id !== null) {
                    $subject = Engine_Api::_()->getItem('pagealbumphoto', $photo_id);
                    if ($subject && $subject->getIdentity()) {
                        Engine_Api::_()->core()->setSubject($subject);
                    }
                }
            }
        }

        $this->params = array('page_id' => $page_id, 'ipp' => $this->_getParam('ipp', 10), 'p' => $this->_getParam('p', 1));

        if (!$this->view->isAllowedView) {
            $this->view->isAllowedPost = false;
            $this->view->isAllowedComment = false;
            return;
        }

        $this->view->isAllowedPost = $this->getApi()->isAllowedPost($pageObject);
        $this->view->isAllowedComment = $this->getPageApi()->isAllowedComment($pageObject);
    }

    public function indexAction()
    {
        if (!$this->view->isAllowedView) {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
            $this->view->html = $this->view->render('error.tpl');
            return;
        }

        $this->params['nonempty'] = true;

        $table = $this->getTable();
        $this->view->albums = $albums = $table->getAlbums($this->params);
        $temp = array();
        foreach ($albums as $album) {
            $temp[$album->getIdentity()]['title'] = $album->getTitle();
            $temp[$album->getIdentity()]['description'] = $album->getDescription();
            $tags = $album->tags()->getTagMaps();
            $tagString = '';
            foreach ($tags as $tagmap) {
                if ($tagString !== '') $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }
            $temp[$album->getIdentity()]['tags'] = $tagString;
        }
        $this->view->albums_js = Zend_Json_Encoder::encode($temp);

        if (!$this->pageObject->isTeamMember()) {
            $this->view->html = $this->view->render('index.tpl');
        } else {
            $this->view->html = $this->view->render('manage.tpl');
        }
    }

    public function mineAction()
    {
        $table = $this->getTable();

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = $this->pageObject;
        $this->view->subject = $subject;
        if (!$this->view->isAllowedView || !$viewer->getIdentity()) {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not view this.");
            $this->view->html = $this->view->render('error.tpl');
            return;
        }

        $this->params['user_id'] = $viewer->getIdentity();
        $this->view->albums = $albums = $table->getAlbums($this->params);
        $temp = array();
        foreach ($albums as $album) {
            $temp[$album->getIdentity()]['title'] = $album->getTitle();
            $temp[$album->getIdentity()]['description'] = $album->getDescription();
            $tags = $album->tags()->getTagMaps();
            $tagString = '';
            foreach ($tags as $tagmap) {
                if ($tagString !== '') $tagString .= ', ';
                $tagString .= $tagmap->getTag()->getTitle();
            }
            $temp[$album->getIdentity()]['tags'] = $tagString;
        }
        $this->view->albums_js = Zend_Json_Encoder::encode($temp);
        $this->view->html = $this->view->render('manage.tpl');
    }

    public function deleteAction()
    {
        $this->view->eval = "self.manage();";
        if ($this->view->isAllowedPost) {

            $table = $this->getTable();
            $album = $table->fetchRow($table->select()
                ->where("page_id = {$this->_getParam('page_id')}")
                ->where("pagealbum_id = {$this->_getParam('album')}"));

            $select = $album->getCollectiblesSelect();
            $photo_id = $this->getTable()->getAdapter()->fetchOne($select);

            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
                if (!empty($photo_id)) {
                    $attachmentTable = Engine_Api::_()->getDbtable('attachments', 'activity');
                    $name = $attachmentTable->info('name');
                    $select = $attachmentTable->select()
                        ->setIntegrityCheck(false)
                        ->from($name, array('action_id'))
                        ->where('type = ?', "pagealbumphoto")
                        ->where('id = ?', $photo_id);

                    $action_id = (int)$attachmentTable->getAdapter()->fetchOne($select);
                    $where = array('action_id = ?' => $action_id);

                    $actionsTable = Engine_Api::_()->getDbtable('actions', 'activity');
                    $actionsTable->delete($where);

                    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
                    $streamTable->delete($where);

                    $attachmentTable->delete($where);

                    $where = array('resource_id = ?' => $action_id);

                    $commentTable = Engine_Api::_()->getDbtable('comments', 'activity');
                    $commentTable->delete($where);

                    $likeTable = Engine_Api::_()->getDbtable('likes', 'activity');
                    $likeTable->delete($where);
                }

                $album->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->view->eval = "self.inc_count(-1); self.manage();";
            $this->view->error = 0;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("Album was deleted.");
            $this->view->html = $this->view->render('success.tpl');
        } else {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not delete albums.");
            $this->view->html = $this->view->render('error.tpl');
        }
    }

    public function uploadAction()
    {
        if (!$this->view->isAllowedPost) {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("You can not post albums.");
            $this->view->html = $this->view->render('error.tpl');
            return;
        }

        // Get form
        $form = new Pagealbum_Form_Album();
        $values = $this->getValues();

        $db = Engine_Api::_()->getDbTable('pagealbums', 'pagealbum')->getAdapter();
        $db->beginTransaction();

        $form->populate($values);
        $this->view->eval = "";

        try {
            $album = $form->saveValues();
            $db->commit();

            $this->view->album = $album_identity = $album->getIdentity();
            $this->view->title = $album->getTitle();
            $this->view->description = $album->getDescription();
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->eval = "self.list();";
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("Album was not created.");
            $this->view->html = $this->view->render('error.tpl');
            return;
        }

        if ($values['album'] == 0) {
            $this->view->eval = "self.inc_count(1); ";
        }
        $this->view->eval .= "self.manage_photos(" . $album_identity . ");";
        $this->view->error = 0;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_("Album was successfully created.");
        $this->view->html = $this->view->render('success.tpl');
    }

    public function uploadPhotoAction()
    {
        if (!$this->_helper->requireUser()->checkRequire()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        $values = $this->getRequest()->getPost();
        if (empty($values['Filename'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
            return;
        }

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
            return;
        }

        $db = Engine_Api::_()->getDbtable('pagealbumphotos', 'pagealbum')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $params = array('owner_id' => $viewer->getIdentity());

            $photo_id = Engine_Api::_()->pagealbum()->createPhoto($params, $_FILES['Filedata'])->getIdentity();

            $this->view->status = true;
            $this->view->name = $_FILES['Filedata']['name'];
            $this->view->photo_id = $photo_id;

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
            return;
        }
    }

    public function removePhotoAction()
    {
        $photo_id = $this->_getParam('photo_id');

        if ($photo_id == null) {
            return;
        }

        $photo = Engine_Api::_()->getItem('pagealbumphoto', $photo_id);
        $db = $this->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $photo->delete();
            $db->commit();
            $this->view->success = true;
        } catch (Exception $e) {
            $db->rollback();
            $this->view->success = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Unknown database error');
            throw $e;
        }
    }

    public function editAction()
    {
        $subject = $this->album;

        $tags = $subject->tags()->getTagMaps();
        $tagString = '';
        foreach ($tags as $tagmap) {
            if ($tagString !== '') $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }

        $this->view->album = array('title' => $subject->getTitle(), 'tags' => $tagString, 'description' => $subject->getDescription(), 'album' => $subject->getIdentity());
    }

    public function viewAction()
    {

        if (!$this->view->isAllowedView) {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view photos.');
            $this->view->html = $this->view->render('error.tpl');
            return;
        }

        $this->view->comment_form_id = "album-comment-form";

        $subject = $this->album;
        $this->view->albumTags = $subject->tags()->getTagMaps();

        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->album = clone $subject;
        $this->view->paginator = $paginator = $this->album->getCollectiblesPaginator();
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());

        $this->view->mine = false;
        $this->view->can_edit = false;

        if ($this->view->album->user_id == $viewer->getIdentity() || $this->pageObject->isTeamMember()) {
            $this->view->mine = true;
            $this->view->can_edit = true; // @todo
        }

        //view count
        if (!$this->album->getOwner()->isSelf($viewer)) {
            $this->album->getTable()->update(array(
                    'view_count' => new Zend_Db_Expr('view_count + 1'),
                ),
                array(
                    'pagealbum_id = ?' => $this->album->getIdentity(),
                ));
        }

        if ($paginator->getTotalItemCount() > 0) {
            if ($this->view->photo) {
                $this->view->subject = $subject = $this->view->photo;
                $counter = 0;
                $inAlbum = false;
                foreach ($paginator as $photo) {
                    if ($subject->isSelf($photo)) {
                        $inAlbum = true;
                        break;
                    }
                    $counter++;
                }
                if (!$inAlbum) {
                    $this->view->subject = $subject = $paginator->getItem(0);
                    $counter = 0;
                }
                $this->view->startIndex = $counter;
            } else {
                $this->view->subject = $subject = $paginator->getItem(0);
                $this->view->startIndex = 0;
            }
            $check_photoviewer = Engine_Api::_()->getDbTable('modules', 'core');
            $select = $check_photoviewer->select()
                ->where('name = ?', 'photoviewer')
                ->where('enabled = ?', 1);
            $viewer_photo = $check_photoviewer->fetchRow($select);
            if ($viewer_photo->enabled == 1) {
                $this->view->photoviewer = 1;
            } else {
                $this->view->photoviewer = 0;
            }

            $this->view->page = $page = $this->_getParam('page');
            $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
            $this->view->likes = $likes = $subject->likes()->getLikePaginator();
            $this->view->comments = $this->getApi()->getComments($page, $subject);

            if ($viewer->getIdentity() && $this->view->isAllowedComment) {
                $this->view->form = $form = new Core_Form_Comment_Create();
                $form->addElement('Hidden', 'form_id', array('value' => 'album-comment-form'));
                $form->populate(array(
                    'identity' => $subject->getIdentity(),
                    'type' => $subject->getType(),
                ));

            }

            if (!Engine_Api::_()->core()->hasSubject()) {
                Engine_Api::_()->core()->setSubject($subject);
            }

            $this->view->subject = $subject;
            $this->view->photo_id = $subject->getIdentity();
            $this->view->likeHtml = $this->view->render('comment/list.tpl');
            $this->view->likeUrl = $this->view->url(array('action' => 'like'), 'like_comment');
            $this->view->unlikeUrl = $this->view->url(array('action' => 'unlike'), 'like_comment');
            $this->view->hintUrl = $this->view->url(array('action' => 'hint'), 'like_comment');
            $this->view->showLikesUrl = $this->view->url(array('action' => 'list'), 'like_comment');
            $this->view->postCommentUrl = $this->view->url(array('action' => 'create'), 'like_comment');
        }

        if ($this->_getParam('format') == 'json')
            $this->view->html = $this->view->render('index/view.tpl');
    }

    public function loadCommentsAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        $this->view->comment_form_id = "album-comment-form";

        $this->view->page = $page = $this->_getParam('page');
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        $this->view->likes = $likes = $subject->likes()->getLikePaginator();
        $this->view->comments = $this->getApi()->getComments($page, $subject);

        if ($viewer->getIdentity() && $this->view->isAllowedComment) {
            $this->view->form = $form = new Core_Form_Comment_Create();
            $form->addElement('Hidden', 'form_id', array('value' => 'album-comment-form'));
            $form->populate(array(
                'identity' => $subject->getIdentity(),
                'type' => $subject->getType(),
            ));
        }

        $this->view->likeHtml = $this->view->render('comment/list.tpl');
        $this->view->likeUrl = $this->view->url(array('action' => 'like'), 'like_comment');
        $this->view->unlikeUrl = $this->view->url(array('action' => 'unlike'), 'like_comment');
        $this->view->hintUrl = $this->view->url(array('action' => 'hint'), 'like_comment');
        $this->view->showLikesUrl = $this->view->url(array('action' => 'list'), 'like_comment');
        $this->view->postCommentUrl = $this->view->url(array('action' => 'create'), 'like_comment');
//    $this->view->html = $this->view->render('comment/list.tpl');
    }

    public function managePhotoAction()
    {
        if (!$this->view->isAllowedPost) {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view photos.');
            $this->view->html = $this->view->render('error.tpl');
            return;
        }

        $this->view->album = $album = $this->album;

        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        if ($paginator->getTotalItemCount() > 0) {

            $paginator->setCurrentPageNumber($this->_getParam('page'));
            $paginator->setItemCountPerPage($paginator->getTotalItemCount());

            $this->view->form = $form = new Pagealbum_Form_Photos();

            foreach ($paginator as $photo) {
                $subform = new Pagealbum_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
                $subform->populate($photo->toArray());
                $form->addSubForm($subform, $photo->getGuid());
                $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
            }
        }

        $this->view->html = $this->view->render('manage_photos.tpl');
    }

    public function editPhotoAction()
    {
        if (!$this->view->isAllowedPost) {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view photos.');
            $this->view->html = $this->view->render('error.tpl');
            return;
        }

        $this->view->album = $album = $this->album;

        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($paginator->getTotalItemCount());

        $this->view->form = $form = new Pagealbum_Form_Photos();

        foreach ($paginator as $photo) {
            $subform = new Pagealbum_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
            $subform->populate($photo->toArray());
            $form->addSubForm($subform, $photo->getGuid());
            $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
        }

        $this->view->html = $this->view->render('manage_photos.tpl');

        $form->populate($this->_getAllParams());

        $table = $album->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $values = $form->getValues();
            if (!empty($values['cover'])) {
                $album->photo_id = $values['cover'];
                $album->save();
            }

            $search_api = Engine_Api::_()->getDbTable('search', 'page');
            // Process
            foreach ($paginator as $photo) {
                $subform = $form->getSubForm($photo->getGuid());
                $values = $subform->getValues();
                $version = Engine_Api::_()->getDbtable('modules', 'core')->select()
                    ->from('engine4_core_modules', 'version')
                    ->where('name = ?', 'core')
                    ->query()
                    ->fetchColumn();
                if (version_compare($version, '4.7.0', '<=')) {
                    $values = $values[$photo->getGuid()];
                }

                unset($values['photo_id']);

                if (isset($values['delete']) && $values['delete'] == '1') {
                    $photo->delete();
                } else {
                    $values['title'] = urldecode($values['title']);
                    $values['description'] = urldecode($values['description']);
                    $photo->setFromArray($values);
                    $photo->save();


                    $search_api->saveData($photo);
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->eval = "self.view({$album->getIdentity()});";
        $this->view->message = "Changes saved";
        $this->view->html = $this->view->render('success.tpl');
    }

    public function saveAction()
    {
        if (!$this->view->isAllowedPost) {
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You can not view photos.');
            $this->view->html = $this->view->render('error.tpl');
            return;
        }

        $user = Engine_Api::_()->user()->getViewer();
        $values = $this->getValues();
        unset($values['file']);

        $table = $this->getTable();

        $db = $table->getAdapter();
        $db->beginTransaction();

        $this->view->eval = "self.view(" . $values['album'] . ")";
        try {
            $album = $table->getAlbum($values);
            $album->setFromArray($values);
            $album->save();

            $tags = preg_split('/[,]+/', $values['tags']);
            if ($tags) {
                $album->tags()->setTagMaps($user, $tags);
            }

            $search_api = Engine_Api::_()->getDbTable('search', 'page');
            $search_api->saveData($album);

            $db->commit();
            $this->view->error = 0;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("Changes were successfully saved.");
            $this->view->html = $this->view->render('success.tpl');
        } catch (Exception $e) {
            $db->rollBack();
            $this->view->error = 1;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_("Changes were not saved.");
            $this->view->html = $this->view->render('error.tpl');
        }
    }

    protected function getValues()
    {
        return array(
            'description' => trim($this->_getParam('description')),
            'title' => trim($this->_getParam('title')),
            'page_id' => (int)$this->_getParam('page_id'),
            'album' => (int)($this->_getParam('album', $this->_getParam('album_id'))),
            'file' => $this->_getParam('file'),
            'tags' => $this->_getParam('tags')
        );
    }

    protected function getApi()
    {
        return Engine_Api::_()->getApi('core', 'pagealbum');
    }

    protected function getPageApi()
    {
        return Engine_Api::_()->getApi('core', 'page');
    }

    protected function getPhotoTable()
    {
        return Engine_Api::_()->getDbTable('pagealbumphotos', 'pagealbum');
    }

    protected function getTable()
    {
        return Engine_Api::_()->getDbTable('pagealbums', 'pagealbum');
    }
}