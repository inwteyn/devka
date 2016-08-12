<?php
/**
 * Created by PhpStorm.
 * User: Nursultan
 * Date: 03.07.14
 * Time: 17:18
 */
class Suggest_Widget_InviteFriendsPageController extends Engine_Content_Widget_Abstract{

    public function indexAction()
    {

        $engine = Engine_Api::_();
        $this->view->subject = $subject = $engine->core()->getSubject();
        $this->view->viewer =  $viewer = $engine->user()->getViewer();
        $suggest = $engine->suggest();

        $this->view->likes_count=$page_likes = $engine->like()->getLikeCount($subject);

        $this->view->friends = $friends = $suggest->getInviteFriends(array(
            'user_id' => $viewer->getIdentity(),
            'resource_id'=>$subject->getIdentity(),
            'resource_type'=>'page',
            'liked'=>true
        ));

        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }
        return true;
    }
}