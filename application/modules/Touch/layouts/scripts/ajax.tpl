<?php
//Init Vars
$counter = (int) $this->layout()->counter;
$request = Zend_Controller_Front::getInstance()->getRequest();
$this->headTitle()
  ->setSeparator(' - ');

$pageTitleKey =   'pagetitle-' .
                  $request->getModuleName() .
                  '-' . $request->getActionName(). '-' .
                  $request->getControllerName();

$pageTitle = $this->translate($pageTitleKey);

if( $pageTitle && $pageTitle != $pageTitleKey ) {
  $this->headTitle($pageTitle, Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
}
$this->headTitle($this->translate($this->layout()->siteinfo['title']), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);

if( $this->subject() && $this->subject()->getIdentity() ) {
  $this->headTitle($this->subject()->getTitle());
}


// Init headScript
$scripts = array();
$scripts_indexes = array();
$inner_scripts = "";
$except_scripts = Engine_Api::_()->touch()->deniedScripts();
$this->headScript()->prependScript($this->headTranslate()->toString());
$hsContainer = $this->headScript()->setAllowArbitraryAttributes(true)->getContainer();
$exc = false;
foreach( $hsContainer as $key => $dat ) {
  if( !empty($dat->attributes['src']) ) {
    foreach($except_scripts as $src){
      if(false !== strpos($dat->attributes['src'], $src)){ $exc  = true;}
      if($exc) break;

    }
    if($exc){ $exc = false; continue; }
    if( false === strpos($dat->attributes['src'], '?') ) { $dat->attributes['src'] .= '?c=' . $counter;} else { $dat->attributes['src'] .= '&c=' . $counter; }
    array_push($scripts, $dat->attributes['src']);

  } else{ $inner_scripts .= $dat->source.';'; }

}
// Get body identity
if( isset($this->layout()->siteinfo['identity']) ) {
  $identity = $this->layout()->siteinfo['identity'];
} else {
  $identity = $request->getModuleName() . '-' .
      $request->getControllerName() . '-' .
      $request->getActionName();
}

?>
<script type="text/javascript">
  Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()) ?>');
  <?php if( $this->subject() ): ?>
    en4.core.subject = {
      type : '<?php echo $this->subject()->getType(); ?>',
      id : <?php echo $this->subject()->getIdentity(); ?>,
      guid : '<?php echo $this->subject()->getGuid(); ?>'
    };
  <?php endif; ?>
  <?php if( $this->viewer()->getIdentity() ): ?>
    en4.user.viewer = {
      type : '<?php echo $this->viewer()->getType(); ?>',
      id : <?php echo $this->viewer()->getIdentity(); ?>,
      guid : '<?php echo $this->viewer()->getGuid(); ?>'
    };
  <?php endif; ?>

  Touch.DPage = new DynPage(
    <?php echo Zend_Json::encode(strip_tags($this->headTitle()->toString())) ?>,
    <?php echo Zend_Json::encode('global_page_'.$identity) ?>,
    <?php echo Zend_Json::encode($scripts) ?>,
    <?php echo Zend_Json::encode($inner_scripts) ?>,
    <?php echo Zend_Json::encode($this->headStyle()->toString()) ?>,
    <?php echo $json_content = @Zend_Json::encode($this->layout()->content) ?>
  )
</script>
  <?php
  if($json_content === 'null')
    echo $this->layout()->content;