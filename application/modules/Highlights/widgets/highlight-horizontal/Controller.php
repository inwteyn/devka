<?php
/***/
class Highlights_Widget_HighlightHorizontalController extends Engine_Content_Widget_Abstract {
  public function indexAction()
  {
    $userTable = Engine_Api::_()->getItemTable('user');
    $highlightTable = Engine_Api::_()->getDbtable('highlights', 'highlights');
    $lastHighlights = $highlightTable->select()
      ->setIntegrityCheck(false)
      ->from(array('h' => $highlightTable->info('name')))
      ->join(array('u' => $userTable->info('name')), 'u.user_id = h.owner_id', array())
      ->where('h.date_finish > NOW()')
      ->order('RAND()')
      ->limit(5);

    $this->view->lastHighlights = $highlightTable->fetchAll($lastHighlights);
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->canHighlight = Engine_Api::_()->getDbTable('permissions', 'authorization')->isAllowed('highlight', $viewer, 'buy_highlight');
    if (count($highlightTable->fetchAll($lastHighlights)->toArray()) == 0) {
      $this->setNoRender(true);
    }
  }
}