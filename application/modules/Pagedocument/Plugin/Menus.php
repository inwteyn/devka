<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2011-09-01 13:17:53 kirill $
 * @author     Kirill
 */

/**
 * @category   Application_Extensions
 * @package    Page Documents
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagedocument_Plugin_Menus
{
  public function onMenuInitialize_PageDocumentAll($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();

    return array(
      'label' => 'pagedocument_Browse Documents',
      'href' => $subject->getHref().'/content/pagedocuments/',
      'onClick' => 'javascript:page_document.list(); return false;',// for SEO by Kirill
      'route' => 'page_document'
    );
  }

  public function onMenuInitialize_PageDocumentMine($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'doc_posting');
    
    if ($isAllowedPost) {
      return array(
        'label' => 'pagedocument_Manage Documents',
        'href' => 'javascript:void(0);',
        'onClick' => 'javascript:page_document.my_documents();',// for SEO by Kirill
        'route' => 'page_document'
      );
    }

    return false;
  }

  public function onMenuInitialize_PageDocumentCreate($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->pagedocument_api_key;
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->pagedocument_secret_key;
    $viewer = Engine_Api::_()->user()->getViewer();
    $auth = Engine_Api::_()->authorization()->context;
    $isAllowedPost = $auth->isAllowed($subject, $viewer, 'doc_posting');

    if (!empty($scribd_api_key) && !empty($scribd_secret)) {
      if ($isAllowedPost) {
        return array(
          'label' => 'pagedocument_Create Document',
          'href' => 'javascript:void(0);',
          'onClick' => 'page_document.create();',
          'route' => 'page_document',
        );
      }
    }

    return false;
  }
}