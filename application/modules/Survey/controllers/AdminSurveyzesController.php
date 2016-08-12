<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSurveyzesController.php 2010-07-02 19:25 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Survey
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Survey_AdminSurveyzesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('survey_admin_main', array(), 'survey_admin_main_surveyzes');
    
    $this->view->page = $page = $this->_getParam('page', 1);
    
    $this->view->paginator = Engine_Api::_()->survey()->getSurveyzesPaginator(array('orderby' => 'survey_id'));
  
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  public function approveAction()
  {
    if(_ENGINE_ADMIN_NEUTER)
    {
      return;
    }

    $survey_id = $this->_getParam('survey_id');

  	//GET USER
  	$survey = Engine_Api::_()->getItem('surveys', $survey_id);

   	$db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $survey->approved = 1 - $survey->approved;
      $survey->save();
 			$db->commit();
	 	}
   	catch( Exception $e ) {
    	$db->rollBack();
     	throw $e;
   	}
  	$this->_redirect("admin/survey/surveyzes/index/page/".$this->_getParam('page'));
  }

  public function deleteAction()
  {
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->survey_id = $id;
    
    // Check post
    if ($this->getRequest()->isPost()) {
      
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $survey = Engine_Api::_()->getItem('surveys', $id);
        // delete the survey into the database
        $survey->delete();

        $db->commit();
      }

      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh' => 10,
        'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-surveyzes/delete.tpl');
  }
}