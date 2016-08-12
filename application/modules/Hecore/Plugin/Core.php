<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-08-31 16:05 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Plugin_Core
{
    public function _onRenderLayoutDefault($event)
    {

        // Arg should be an instance of Zend_View
        $view = $event->getPayload();

        if (!($view instanceof Zend_View)) {
            return;
        }

        $view->headTranslate(array('More'));


        $theme_name = $view->activeTheme();
        $script = <<<JS
    en4.core.runonce.add(function() {
      $$('body').addClass('layout_active_theme_{$theme_name}');
    });
JS;

        $view->headScript()
            ->appendFile($view->hecoreBaseUrl()
                . 'application/modules/Hecore/externals/scripts/core.js')
            ->appendFile($view->hecoreBaseUrl()
                . 'application/modules/Hecore/externals/scripts/imagezoom/core.js')
            ->appendFile($view->hecoreBaseUrl()
                . 'application/modules/Hecore/externals/scripts/hestrap/Hestrap.js')
            ->appendFile($view->hecoreBaseUrl()
                . 'application/modules/Hecore/externals/scripts/hestrap/Hestrap.Dropdown.js')
            ->appendFile($view->hecoreBaseUrl()
                . 'application/modules/Hecore/externals/scripts/hestrap/Hestrap.Tab.js')
            ->appendScript($script);

        $view->headLink()
            ->appendStylesheet($view->hecoreBaseUrl()
                . 'application/css.php?request=application/modules/Hecore/externals/styles/imagezoom/core.css')
            ->appendStylesheet($view->hecoreBaseUrl()
                . 'application/modules/Hecore/externals/styles/hestrap.css');

        $view->headTranslate(array('Confirm', 'Cancel', 'or', 'close'));

        /* Font Awesome Install by Jungar*/
        $this->_installFontAwesome($view);
    }

    public function onRenderLayoutDefault($event)
    {
        $this->_onRenderLayoutDefault($event);
        $view = $event->getPayload();

        if (!($view instanceof Zend_View)) {
            return;
        }
        $this->_initTheme($view);
    }

    public function onRenderLayoutAdmin($event)
    {
        $this->_onRenderLayoutDefault($event);
    }

    public function onRenderLayoutAdminSimple($event)
    {
        $this->_onRenderLayoutDefault($event);
    }

    public function onRenderLayoutDefaultSimple($event)
    {
        $this->_onRenderLayoutDefault($event);
    }

    private function _initTheme(Zend_View $view)
    {
        $themeName = Engine_Api::_()->getDbtable('themes', 'core')->fetchAll()->getRowMatching('active', 1)->name;
        $dir = APPLICATION_PATH . '/application/themes/' . $themeName;
        $meta = include("$dir/manifest.php");
        if (isset($meta['he']) && isset($meta['he']['params'])) {
            $scripts = $meta['he']['params']['scripts'];

            foreach ($scripts as $script) {
                $view->headScript()
                    ->appendFile($view->hecoreBaseUrl()
                        . "application/themes/$themeName/$script");
            }
        }
    }

    private function _installFontAwesome(Zend_View $view)
    {
        $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Hecore/externals/css/font-awesome.min.css');
        $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Hecore/externals/css/font-awesome-ie7.min.css', null, 'IE 7');

        $baseUrl = $view->baseUrl();
        // avoiding CDN server quirk
        $content = <<<CONTENT
          @font-face {
            font-family: 'FontAwesome';
            src: url('{$baseUrl}/application/modules/Hecore/externals/font/fontawesome-webfont.eot?v=3.2.1');
            src: url('{$baseUrl}/application/modules/Hecore/externals/font/fontawesome-webfont.eot?#iefix&v=3.2.1') format('embedded-opentype'),
              url('{$baseUrl}/application/modules/Hecore/externals/font/fontawesome-webfont.woff?v=3.2.1') format('woff'),
              url('{$baseUrl}/application/modules/Hecore/externals/font/fontawesome-webfont.ttf?v=3.2.1') format('truetype');
            font-weight: normal;
            font-style: normal;
          }
CONTENT;
        $view->headStyle()->appendStyle($content);
    }
}