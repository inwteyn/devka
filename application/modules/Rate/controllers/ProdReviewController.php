<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ReviewController.php 2010-07-02 19:27 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Rate_ProdReviewController extends Core_Controller_Action_Standard
{
  public function init()
  {
      
    $this->_helper->contextSwitch->initContext();

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('rate', 'json')->initContext('json');

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';

    $this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/product-review-ajax';

    $this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/product-review';

    $this->view->addScriptPath($path);
  }

  public function createAction()
  {
    $result = 0;
    $product_id = (int)$this->_getParam('product_id');

    $tbl_product = Engine_Api::_()->getDbTable('products', 'store');
    $product = $tbl_product->fetchRow('product_id=' . $product_id);
    $getC = $product->getCategory();
      $categories = Engine_Api::_()->getApi('core', 'rate')->getStoreCategories();
      foreach ($categories as $category) {
          if($getC['category']== $category->label){
              $option = $category->option_id;
          }

      }

    $viewer = Engine_Api::_()->user()->getViewer();


      $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');
      $select = $tbl_type->select()
          ->where('category_id = ?', $option)
          ->where('type = ?', 'store')
          ->order('order');
      $types = $tbl_type->fetchAll($select);


    // if product and viewer exists
    if ($product && $viewer->getIdentity()) {

      $form = new Rate_Form_Review_Create;
      // Add vote types
      $form->addVotes($types);

      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

        $tbl = Engine_Api::_()->getDbTable('productreviews', 'rate');


        if ($tbl->isAllowedPost($product->getIdentity(), $viewer)) {

          $values = $form->getValues();
            $output = array_slice($values, 3,count($values));

          $values['user_id'] = $viewer->getIdentity();
          $values['product_id'] = $product_id;
          $values['creation_date'] = date('Y-m-d H:i:s');
          $values['modified_date'] = date('Y-m-d H:i:s');

          $row = $tbl->createRow($values);
          $result = (bool)$row->save();

          if ($result) {

            // Add Votes
            $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
            $type_keys = array();
            foreach ($types as $type) {
              $type_keys[$type->type_id] = 'rate_' . $type->type_id;
            }

            foreach ($values as $key => $value) {

              if ($type_id = array_search($key, $type_keys)) {

                $tbl_vote->createRow(array(
                  'type_id' => $type_id,
                  'review_id' => $row->getIdentity(),
                  'product_id' => $product->getIdentity(),
                  'rating' => ($value <= 5 || $value >= 0) ? (int)$value : 0,
                  'creation_date' => date('Y-m-d H:i:s')
                ))->save();

              }
            }

            // Add Search
            //Engine_Api::_()->getDbTable('search', 'product')->saveData($row);

            // Add Action
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            $link = $row->getLink();

            $product = $row->getProduct();

            $action = $api->addActivity($viewer, $product, 'productreview_new', null, array('link' => $link, 'object' => $product));

            if($action) {
              $api->attachActivity($action, $row);
            }

            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            $notifyApi->addNotification($product->getOwner(), $viewer, $row, 'post_productreview', array(
              'label' => $row->getShortType()
            ));

            $this->view->id = $row->getIdentity();
          }
        }
      }
    }

    $this->view->result = $result;
    $this->view->msg = $this->view->translate(($result) ? 'RATE_REVIEW_CREATE_SUCCESS'
      : 'PRODUCT_RATE_REVIEW_CREATE_ERROR');
  }

  public function viewAction()
  {
    $result = false;
    $row = Engine_Api::_()->getDbTable('productreviews', 'rate')->fetchRow('productreview_id =' . (int)$this->_getParam('review_id'));

    if ($row) {
      if (!Engine_Api::_()->core()->hasSubject())
        Engine_Api::_()->core()->setSubject($row);

      $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
      $select = $tbl_vote->select()
        ->where('review_id = ?', $row->getIdentity());
      $votes = $tbl_vote->fetchAll($select);


      $vote_list = array();
      foreach ($votes as $vote) {
        $vote_list[$vote->type_id] = $vote->rating;
      }
        $product = Engine_Api::_()->getDbTable('products', 'store')->fetchRow('product_id =' . (int)$this->_getParam('product_id'));
        $productCat = $product->getCategory();

        $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');
        $categories = Engine_Api::_()->getApi('core', 'rate')->getStoreCategories();
        foreach ($categories as $category) {
            if($productCat['category']== $category->label){
                $option = $category->option_id;
            }

        }

        $select = $tbl_type->select()
            ->where('category_id = ?',$option )
            ->where('type = ?', 'store')
            ->order('order');

        $types = $tbl_type->fetchAll($select);

        $typesA = $types->toArray();
        $votesA = $votes->toArray();
      for ($i=0;$i<count($typesA);$i++) {
          foreach($votesA as $vote){
            if($typesA[$i]['type_id' ]== $vote['type_id' ]){
                $typesA[$i]['rating'] = $vote['rating'];
            }
        }

      }


      $this->view->types = $typesA;

      $this->view->owner = $row->getOwner();
      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
      $this->view->row = $row;

      $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
      $this->view->likes = $row->likes()->getLikePaginator();

      $this->view->comment_form_id = "productreview-comment-form";
      $this->view->product = $product = $this->_getParam('product');
      $this->view->comments = Engine_Api::_()->getApi('core', 'rate')->getComments($product);
      $this->view->isAllowedComment = $row->getProduct()->authorization()->isAllowed($viewer, 'comment');

      $this->view->isLikeEnabled = $isLikeEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('like');

      if ($this->view->isAllowedComment && $isLikeEnabled) {
        $this->view->form = $form = new Core_Form_Comment_Create();
        $form->addElement('Hidden', 'form_id', array('value' => 'productreview-comment-form'));
        $form->populate(array(
          'identity' => $row->getIdentity(),
          'type' => $row->getType(),
        ));

        $this->view->subject = $row;
        $this->view->likeHtml = $this->view->render('comment/list.tpl');
        $this->view->likeUrl = $this->view->url(array('action' => 'like'), 'like_comment');
        $this->view->unlikeUrl = $this->view->url(array('action' => 'unlike'), 'like_comment');
        $this->view->hintUrl = $this->view->url(array('action' => 'hint'), 'like_comment');
        $this->view->showLikesUrl = $this->view->url(array('action' => 'list'), 'like_comment');
        $this->view->postCommentUrl = $this->view->url(array('action' => 'create'), 'like_comment');

      }
      $result = true;
      $this->view->html = $this->view->render('view.tpl');
    }

    $this->view->result = $result;
  }

  public function editAction()
  {
      $result = 0;
      $product_id = (int)$this->_getParam('product_id');

      $tbl_product = Engine_Api::_()->getDbTable('products', 'store');
      $product = $tbl_product->fetchRow('product_id=' . $product_id);
      $getC = $product->getCategory();
      $categories = Engine_Api::_()->getApi('core', 'rate')->getStoreCategories();
      foreach ($categories as $category) {
          if($getC['category']== $category->label){
              $option = $category->option_id;
          }

      }

      $viewer = Engine_Api::_()->user()->getViewer();


      $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');
      $select = $tbl_type->select()
          ->where('category_id = ?', $option)
          ->where('type = ?', 'store')
          ->order('order');
      $types = $tbl_type->fetchAll($select);





    $review_id = (int)$this->_getParam('productreview_id');

    $viewer = Engine_Api::_()->user()->getViewer();

    $tbl = Engine_Api::_()->getDbTable('productreviews', 'rate');
    $row = $tbl->fetchRow('productreview_id=' . $review_id);

    if ($row && $viewer->isOwner($row->getOwner())) {

      $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
      $select = $tbl_vote->select()
        ->where('review_id = ?', $row->getIdentity());
      $votes = $tbl_vote->fetchAll($select);

      $vote_list = array();
      foreach ($votes as $vote) {
        $vote_list[$vote->type_id] = $vote->rating;
      }


      foreach ($types as $key => $type) {
        if (isset($vote_list[$type->type_id])) {
          $types[$key]->value = $vote_list[$type->type_id];
        }
      }

      $form = new Rate_Form_ProductReview_Edit;
      $this->view->js = implode(" ", $form->addVotes($types));

      if ($this->_getParam('task') == 'dosave') {

        $result = false;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

          $values = $form->getValues();
          $values['modified_date'] = date('Y-m-d H:i:s');
          $row->setFromArray($values);
          $result = (bool)$row->save();

          if ($result) {

            // Delete Old Votes
            $tbl_vote->delete(array(
              'review_id = ?' => $row->getIdentity()
            ));

            $type_keys = array();
            foreach ($types as $type) {
              $type_keys[$type->type_id] = 'rate_' . $type->type_id;
            }
            foreach ($values as $key => $value) {
              if ($type_id = array_search($key, $type_keys)) {
                $tbl_vote->createRow(array(
                  'type_id' => $type_id,
                  'review_id' => $row->getIdentity(),
                  'product_id' => $product_id,
                  'rating' => ($value <= 5 || $value >= 0) ? (int)$value : 0,
                  'creation_date' => date('Y-m-d H:i:s')
                ))->save();
              }
            }

            // Delete and Create Search
            /*$tbl_search = Engine_Api::_()->getDbTable('search', 'product');
            $tbl_search->saveData($row);*/
          }
        }

        $this->view->result = $result;
        $this->view->id = $row->getIdentity();
        $this->view->msg = $this->view->translate(($result) ? 'RATE_REVIEW_EDIT_SUCCESS'
          : 'RATE_REVIEW_EDIT_ERROR');

      } else {

        // Set Form Values
        $form->productreview_id->setValue($row->getIdentity());
        $form->title->setValue($row->title);
        $form->body->setValue($row->body);

        $this->view->form = $form;
        $this->view->html = $this->view->render('edit.tpl');
      }
    }
  }

  public function removeAction()
  {
    $result = false;
    $review_id = $this->_getParam('review_id');

    $viewer = Engine_Api::_()->user()->getViewer();

    $tbl = Engine_Api::_()->getDbTable('productreviews', 'rate');
    $row = $tbl->fetchRow('productreview_id=' . $review_id);

    if ($row) {

      $product_id = $row->product_id;

      if ($viewer->isOwner($row->getOwner()) || Engine_Api::_()->getApi('core', 'rate')
          ->isAllowRemoveReview($product_id, $viewer)
      ) {
        $result = (bool)$row->delete();
      }
    }
    $this->view->result = $result;
    $this->view->msg = $this->view->translate(($result) ? 'RATE_REVIEW_DELETE_SUCCESS'
      : 'RATE_REVIEW_DELETE_ERROR');

    $this->view->isAllowedPost = $tbl->isAllowedPost($product_id, $viewer);
  }

  public function listAction()
  {
    $this->view->result = true;
    $this->view->product_id = $product_id = $this->_getParam('product_id');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->getItem('store_product', $product_id));
    }

    $types = Engine_Api::_()->getApi('core', 'rate')->getProductTypes($product_id);

    // get paginator
    $tbl = Engine_Api::_()->getDbTable('productreviews', 'rate');
    $this->view->paginator = $tbl->getPaginator($product_id, $viewer->getIdentity(), $this->_getParam('page'));
    $this->view->isAllowedPost = $tbl->isAllowedPost($product_id, $viewer);

    // is allowed remove
    $this->view->isAllowedRemove = Engine_Api::_()->getApi('core', 'rate')->isAllowRemoveProductReview($product_id, $viewer);
    $this->view->countOptions = count($types);
    $this->view->html = $this->view->render('list.tpl');
    $this->view->count = $this->view->paginator->getCurrentItemCount();
  }
}
