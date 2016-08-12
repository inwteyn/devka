<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DefaultProviders.php 2010-07-02 19:54 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Inviter
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Engine_Form_Decorator_DefaultProviders extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;

  public function render($content)
  {
    $img_width = '64px';

    $style=<<<STYLE
<style>
  #provider_facebook:hover > div {
    transition: opacity 0.5s ease 0s;
    -webkit-transition: opacity 0.5s ease 0s;
    -ms-transition: opacity 0.5s ease 0s;
    opacity: 1;
  }

  #provider_facebook > div {
    position: absolute;
    top: -65px;
    left: 0;
    padding-bottom: 5px;

    transition: opacity 0.5s ease 0s;
    -webkit-transition: opacity 0.5s ease 0s;
    -ms-transition: opacity 0.5s ease 0s;
    opacity: 0;

    background-image: url('application/modules/Inviter/externals/images/arrows.png');
    background-repeat: no-repeat;
    background-position: 10px 50px;
  }

  #provider_facebook > div > div {
    background: #fff;
    border: 1px solid;
    padding: 5px;
    width: 200px;
    border-radius: 3px;
    text-align: left;
  }
</style>
STYLE;




    $html = $style . "<table cellpadding='0' cellspacing='0' style='margin-top: 5px;' ><tr>";

    $providers_html = "<div style='padding: 10px; padding-top: 0px;' id='default_providers'>";

    $providers = Engine_Api::_()->inviter()->getIntegratedProviders(false);

    foreach ($providers as $provider) {

      /*if($provider->provider_title == 'LinkedIn' || $provider->provider_logo == 'linkedin_logo.png') {
        continue;
      }*/


      $id = '';
      $div = '';
      $style='';
      if($provider->provider_id == 1) {
        $style='position: relative;';
        $id = 'provider_facebook';
        $lang = Zend_Registry::get('Zend_Translate')->_('INVITER_Facebook popup');
        $div = <<<DIV
<div>
 <div>
  <span>
    {$lang}
  </span>
 </div>
</div>
DIV;

      }


      $providers_html .= <<<PROVIDER
<div id="{$id}" onclick='provider.open("{$provider->provider_title}");' class="default_provider" style="{$style}">
  {$div}
  <a href="javascript://" style="float: left">
    <img
      src="application/modules/Inviter/externals/images/providers_big/{$provider->provider_logo}"
      width="{$img_width}"
    />
  </a>
</div>
PROVIDER;
    }

    $providers_html .= "</div>";

    $html .= '<td valign="top">' . $providers_html . '</td></tr>' . "<tr><td style='padding-top: 20px;' width='100%'>" . $content . "</td></tr>" . '</table>';

    return $html;
  }
}