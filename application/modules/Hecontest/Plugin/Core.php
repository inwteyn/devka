<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Hecontest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Hecontest_Plugin_Core
{
    public function onRenderLayoutDefault($event)
    {
        $view = $event->getPayload();
        if ($view instanceof Zend_View) {
            $view->headScript()->appendFile('application/modules/Hecontest/externals/scripts/core.js');
        }

        $view->hecontestJoinForm = new Hecontest_Form_Join();


        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
            if($subject->getType() == 'hecontest'){
                $paided = Engine_Api::_()->getDbTable('purchaseds', 'hecontest')->getPaidedContest($subject->getIdentity());
                if($subject->price_credit>0 && !$paided){
                    $view->hecontest  = $subject;
                    $view->paided = 1;
                }else{
                    $view->paided = 0;
                }

            }
            $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
            if (strpos($subject->getPhotoUrl(), 'http://') === 0 || strpos($subject->getPhotoUrl(), 'https://') === 0) {
                $host_url = '';
            }
            try {
                $og_image = $subject->getPhotoUrl() ? $host_url . $subject->getPhotoUrl() : false;
            } catch (Exception $e) {
            }
            echo ($og_image) ? '<meta property="og:image" content="' . $og_image . '"/>' . "\n" : '';
        }
        $view->layout()->content .= $view->render('application/modules/Hecontest/views/scripts/_joinForm.tpl');
        $view->layout()->content .= $view->render('application/modules/Hecontest/views/scripts/_voteForm.tpl');
    }
}
