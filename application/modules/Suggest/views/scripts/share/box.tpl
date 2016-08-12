<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: box.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>
<?php
$type = $this->subject->getType();
$id = $this->subject->getIdentity();
$html = '';
$onclick = "javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"
?>
<?php foreach ($this->socials as $key => $item): ?>
    <?php $html .= '<li class="' . $key . '_share_box_container"><a class="hei hei-' . $key . ' hei-lg>" href="' . $item['share_url'] . '" onclick="' . $onclick . '"><span>' . $item['count'] . '</span></a></li>'; ?>
<?php endforeach; ?>

<?php

if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
    $html .= '
    <li class="se_share_box_container"> '
        . $this->htmlLink($this->url(array(
                'module' => 'activity',
                'controller' => 'index',
                'action' => 'share',
                'type' => $type,
                'id' => $id,
                'format' => 'smoothbox',
            ), 'default'), '',
            array(
                'class' => 'smoothbox hei hei-share',
                'title' => $this->translate('share')
            ))
        . '</li>
    <li class="he_suggest_box_container">
      <a href="javascript:HESuggest.open()" class="hei hei-users" title="' . $this->translate('suggest') . '"></a>
    </li>
    ';
}
?>

window.addEvent('load', function() {
HESuggest.share(<?php echo Zend_Json_Encoder::encode($html); ?>);
});