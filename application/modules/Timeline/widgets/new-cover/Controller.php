<?php
/**
 * user, page - взять готовое
 * groups, events, offers - хранить cover photo в фотках самих плагинов
 * rest - создавать альбом type_cover_photos
 *
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Timeline_Widget_NewCoverController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {

        if (!Engine_Api::_()->core()->hasSubject()) {
            $this->view->subject = $subject = Engine_Api::_()->user()->getViewer();
        }else{
            $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        }
        $element = $this->getElement();


     //   print_die($element);

        $element->clearDecorators()
            //->addDecorator('Children', array('placement' => 'APPEND'))
            ->addDecorator('Container');

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();




        if (!$subject) {
            return $this->setNoRender();
        }

        if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')) {
            $this->view->likeEnabled = $likeEnabled = (bool)(Engine_Api::_()->like()->isAllowed($subject));
            $this->view->liked = Engine_Api::_()->like()->isLike($subject);
        }

        // Set up element
        $element = $this->getElement();
        $element->clearDecorators();

        $action_id = (int)Zend_Controller_Front::getInstance()->getRequest()->getParam('action_id');
        $activeTab = $action_id ? 'activity.feed' : Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');



        if (empty($activeTab)) {
            $activeTab = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
        }


        if($subject->getType() == 'page') {
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

        $select = $permissionsTable->select()->where('name = ?','auth_features')->where('level_id = ?','4');

        $tmp = $permissionsTable->fetchAll($select);
        try{
            $res = $tmp[0];
        }catch ( Exception $e){
            $res = array();
        }

        @$str = str_replace('"','',$res->params);
        @$str = str_replace('[','',$str);
        @$str = str_replace(']','',$str);

        $str_array = explode(",", $str);
        $str_array['type'] = 'user';
        $this->view->single_widget = false;

                $widgets_pseudonyms = Engine_Api::_()->page()->getWidgetsPseudonyms();

                $activeTab = $widgets_pseudonyms[$activeTab];

                $this->view->widgets_pseudonyms = array_flip($widgets_pseudonyms);
                $this->view->single_widget = true;
            $str_array['type'] = 'page';

        }

        $tabs = Engine_Api::_()->timeline()->getWidgetTabs($str_array,$element, $activeTab);

        $this->view->activeTab = $activeTab;
        $this->view->tabs = $tabs;
        $this->view->max = $this->_getParam('max');


        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $this->view->item_id = $id = $subject->getIdentity();
        $this->view->item_type = $type = $subject->getType();




        try {

            $this->view->profilePhoto = Engine_Api::_()->timeline()->getProfilePhoto($subject, $type, $id);
        } catch(Exception $e) {

        }
        try {

            $this->view->coverPhoto = Engine_Api::_()->timeline()->getTimelinePhoto($id, $type, 'cover');
        } catch(Exception $e) {

        }



        $this->view->isAlbumEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album');

        if($viewer->getIdentity()) {
            if ($type == 'page') {
                $this->view->canEdit = ($subject->user_id == $viewer->getIdentity() || $viewer->level_id < 4);
            } else {
                $this->view->canEdit = $subject->authorization()->isAllowed($viewer, 'edit');
            }
        }


        $this->view->profile_options = Engine_Api::_()->timeline()->getProfileOptions($type);

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

        $this->view->allowFromAlbums = array('user', 'page');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->menuitems = $settings->__get('timeline.menuitems', 20);
        switch ($type) {
            case 'user':
                if ($subject->status) {
                    $this->view->subjectAdditionalInfo = $this->view->viewMore($subject->status, 60);
                    if ($subject->getIdentity() == $viewer->getidentity()) {
                        $this->view->subjectAdditionalInfo .= '<a class="profile_status_clear" href="javascript:void(0);" onclick="document.tl_cover.clearStatus();">(' . $this->view->translate('clear') . ')</a>';
                    }
                }
                break;
            case
            'page':
                $this->view->subjectAdditionalInfo = false;
                $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
                foreach($fieldStructure as $s) {
                    if($s->option_id) {
                        $opt = $s->getOption();
                        $this->view->subjectAdditionalInfo = $opt->label;
                        break;
                    }
                }
                break;
            default:
                $this->view->subjectAdditionalInfo = false;
        }

    }
}
