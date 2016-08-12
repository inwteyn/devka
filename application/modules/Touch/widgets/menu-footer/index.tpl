<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<?php echo $this->translate('Copyright &copy;%s', date('Y')) ?>
<?php foreach( $this->navigation as $item ):
  $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
    'reset_params', 'route', 'module', 'controller', 'action', 'type',
    'visible', 'label', 'href'
  )));

  if ($attribs['class'] != 'menu_core_footer core_mini_auth'):
    
    //Ulan
//    $attribs['class'] .= ' ' . 'touchajax';
  endif;
  ?>
  &nbsp;-&nbsp; <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
<?php endforeach; ?>

<?php if( 1 !== count($this->languageNameList) ): ?>
  &nbsp;-&nbsp;
  <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline">
    <?php $selectedLanguage = $this->translate()->getLocale() ?>
    <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->languageNameList) ?>
    <?php echo $this->formHidden('return', $this->url()) ?>
  </form>
<?php endif; ?>
