<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: UtilityController.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Core_UtilityController extends Touch_Controller_Action_Standard
{

  public function localeAction()
  {
    $locale = $this->_getParam('locale');
    $language = $this->_getParam('language');
    $return = $this->_getParam('return', $this->_helper->url->url(array(), 'default', true));
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !empty($locale) ) {
      try {
        $locale = Zend_Locale::findLocale($locale);
      } catch( Exception $e ) {
        $locale = null;
      }
    }
    if( !empty($language) ) {
      try {
        $language = Zend_Locale::findLocale($language);
      } catch( Exception $e ) {
        $language = null;
      }
    }

    if(  $language && !$locale ) $locale = $language;
    if( !$language &&  $locale ) $language = $locale;
    
    if( $language && $locale ) {
      // Set as cookie
      setcookie('en4_language', $language, time() + (86400*365), '/');
      setcookie('en4_locale',   $locale,   time() + (86400*365), '/');
      // Set as database
      if( $viewer && $viewer->getIdentity() ) {
        $viewer->locale = $locale;
        $viewer->language = $language;
        $viewer->save();
      }
    }

    return $this->_helper->touchRedirector->gotoUrl($return, array('prependBase' => false));
  }

}