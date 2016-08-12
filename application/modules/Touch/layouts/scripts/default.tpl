<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: default.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */
?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-touch10.dtd">
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
  <meta content="<?php echo APPLICATION_ENV ?>" name="app_env">
  <meta content='<?php echo $this->touchCacheSettings(true); ?>' name="cache_settings">
  <meta content="<?php echo $this->touchActiveTheme(); ?>" name="active_theme">
  <meta content="<?php echo $this->isMaintenanceMode(); ?>" name="is_maintenance">

  <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />

  <?php // ALLOW HOOKS INTO META ?>
  <?php echo $this->touchHeadTranslate(); ?>

	<?php // TITLE/META ?>
	<?php
		$counter = (int) $this->layout()->counter;
    $staticBaseUrl = $this->layout()->staticBaseUrl;

		$request = Zend_Controller_Front::getInstance()->getRequest();
		$this->headTitle()
			->setSeparator(' - ');
		$pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
				. '-' . $request->getControllerName();
		$pageTitle = $this->translate($pageTitleKey);
		if( $pageTitle && $pageTitle != $pageTitleKey ) {
			$this
				->headTitle($pageTitle, Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
		}
		$this->headTitle($this->translate($this->layout()->siteinfo['title']), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
		$this->headMeta()
			->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
      ->appendHttpEquiv('Content-Language', $this->locale()->getLocale()->__toString());

		// Make description and keywords
		$description = '';
		$keywords = '';

		$description .= ' ' .$this->layout()->siteinfo['description'];
		$keywords = $this->layout()->siteinfo['keywords'];

		if( $this->subject() && $this->subject()->getIdentity() ) {
			$this->headTitle($this->subject()->getTitle());

			$description .= ' ' .$this->subject()->getDescription();
			if (!empty($keywords)) $keywords .= ',';
			$keywords .= $this->subject()->getKeywords(',');
		}

		$this->headMeta()->appendName('description', trim($description));
		$this->headMeta()->appendName('keywords', trim($keywords));

		// Get body identity
		if( isset($this->layout()->siteinfo['identity']) ) {
			$identity = $this->layout()->siteinfo['identity'];
		} else {
			$identity = $request->getModuleName() . '-' .
					$request->getControllerName() . '-' .
					$request->getActionName();
		}
	?>
	<?php echo $this->headTitle()->toString()."\n" ?>
	<?php echo $this->headMeta()->toString()."\n" ?>
	
  <?php // LINK/STYLES ?>
  <?php
    $this->headLink(array(
      'rel' => 'favicon',
      'href' => ( isset($this->layout()->favicon)
        ? $staticBaseUrl . $this->layout()->favicon
        : '/favicon.ico' ),
      'type' => 'image/x-icon'),
      'PREPEND');
    $themes = array();
    if( null !== ($theme = $this->touchActiveTheme()) ) {
      $themes = array($theme);
    } else {
      $themes = array('default');
    }
  ?>
  <?php echo $this->headLink()->toString()."\n" ?>
  <?php echo $this->headStyle()->toString()."\n" ?>

	<style type="text/css">
		<?php
    if(!isset($_COOKIE['css_cached']) || $_COOKIE['css_cached']=='false' || APPLICATION_ENV == 'development' || $_COOKIE['cached_theme']!= $this->touchActiveTheme() || Engine_Api::_()->getApi('settings', 'core')->getSetting('touch.admin.cache.enable')=='0')
      echo $this->activeTouchThemeStyles($counter);
    ?>
	</style>

  <?php

  $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
  $coreItem = $modulesTbl->getModule('core')->toArray();
  if (version_compare($coreItem['version'], '4.1.7') < 0) {
      echo $this->touchScript($this->baseUrl() . '/externals/mootools/mootools-core-1.2.4-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c='.$counter);
      echo $this->touchScript($this->baseUrl() . '/externals/mootools/mootools-more-1.2.4.4-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c='.$counter);
  } else if (version_compare($coreItem['version'], '4.2.2') < 0) {
      echo $this->touchScript($this->baseUrl() . '/externals/mootools/mootools-core-1.3.2-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c='.$counter);
      echo $this->touchScript($this->baseUrl() . '/externals/mootools/mootools-more-1.3.2.1-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c='.$counter);
  } else {
      echo $this->touchScript($this->baseUrl() . '/externals/mootools/mootools-core-1.4.5-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c='.$counter);
      echo $this->touchScript($this->baseUrl() . '/externals/mootools/mootools-more-1.4.0.1-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js?c='.$counter);
  }

  echo $this->touchScript($this->baseUrl().'/externals/chootools/chootools.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Core/externals/scripts/core.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/User/externals/scripts/core.js?c='.$counter);
  if (Engine_Api::_()->touch()->isModuleEnabled('suggest'))
  echo $this->touchScript($this->baseUrl().'/application/modules/Suggest/externals/scripts/core.js?c='.$counter);


  //Touch Scripts
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/hecore.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/ajax.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/StorageAPI.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/Cache.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/form.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/confirm.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/multiselect.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/picup.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/smoothbox.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/photobox.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/externals/soundmanager/script/soundmanager2.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/core.js?c='.$counter);

  //MooTouch Lib
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/libs/moo-touch/MTCore.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/libs/moo-touch/MTPoint.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/libs/moo-touch/MTSwipeEvent.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/libs/moo-touch/MTScrollView.js?c='.$counter);

  // Google APIs
  echo $this->touchScript('http://maps.googleapis.com/maps/api/js?sensor=true&libraries=places');
  echo $this->touchScript('http://maps.googleapis.com/maps/api/js?sensor=true');
  echo $this->touchScript('http://www.google.com/jsapi');
  //Touch Modules
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/modules/Activity/externals/scripts/core.js?c='.$counter);
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/calendar/calendar.compat.js?c='.$counter);


  if(Engine_Api::_()->touch()->isModuleEnabled('like'))
    echo $this->touchScript($this->baseUrl().'/application/modules/Touch/modules/Like/externals/scripts/core.js?c='.$counter);

  if(Engine_Api::_()->touch()->isModuleEnabled('rate')){
    echo $this->touchScript($this->baseUrl().'/application/modules/Touch/modules/Rate/externals/scripts/Rate.js?c='.$counter);
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $front_router = Zend_Controller_Front::getInstance()->getRouter();
    $plugins_settings = Zend_Json::encode( array(
      'blog' => array(
        'enabled' => $settings->getSetting('touch.rate.blog.enabled', true),
        'url_rate' => $front_router->assemble(array('module' => 'touch'), 'getRateContainer')
      ),
      'album' => array(
        'enabled' => $settings->getSetting('touch.rate.album_photo.enabled', true),
        'url_rate' => $front_router->assemble(array('module' => 'touch'), 'getRateContainer')
      ),
      'article' => array(
        'enabled' => $settings->getSetting('touch.rate.article.enabled', true),
        'url_rate' => $front_router->assemble(array('module' => 'touch'), 'getRateContainer')
      )
    ));
    $this->headScript()->appendScript('getRateContainer(' . $plugins_settings . ');');
  }

  if(Engine_Api::_()->touch()->isModuleEnabled('chat'))
    echo $this->touchScript($this->baseUrl().'/application/modules/Touch/modules/Chat/externals/scripts/core.js?c='.$counter);

  if(Engine_Api::_()->touch()->isModuleEnabled('store')){
    echo $this->touchScript($this->baseUrl().'/application/modules/Touch/modules/Store/externals/scripts/core.js?c='.$counter);
    echo $this->touchScript($this->baseUrl().'/application/modules/Touch/modules/Store/externals/scripts/manager.js?c='.$counter);
  }
  echo $this->touchScript($this->baseUrl().'/application/modules/Touch/externals/scripts/Html5AudioEngine.js?c='.$counter);

  ?>
<?php // SCRIPTS ?>

  <?php // facebook return hack ?>
  <script type="text/javascript">if (window.location.hash == '#_=_')window.location.hash = '';</script>

<script type="text/javascript">
	Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()) ?>');

	en4.core.siteTitle = "<?php echo Engine_Api::_()->getApi('settings', 'core')->__get('core_general_site_title', $this->translate('_SITE_TITLE')) ?>";
  Touch.referrerFavicon = "<?php echo (file_exists(APPLICATION_PATH .'/favicon.ico') ?  'http://' .$_SERVER['HTTP_HOST'] . $this->baseUrl() .'/'. 'favicon.ico'  : '');?>";
	en4.orientation = '<?php echo $orientation ?>';
	en4.core.environment = '<?php echo APPLICATION_ENV ?>';
	en4.core.language.setLocale('<?php echo $this->locale()->getLocale()->__toString() ?>');
	en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
  en4.core.staticBaseUrl = '<?php echo $this->escape($this->layout()->staticBaseUrl) ?>';
	en4.core.loader = new Element('img', {src:en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif'});

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

	if( <?php echo ( Zend_Controller_Front::getInstance()->getRequest()->getParam('ajax', false) ? 'true' : 'false' ) ?> ) {
		en4.core.dloader.attach();
	}

  <?php echo $this->headTranslate()->render()?>
</script>
<?php
// Process
  $except_scripts = Engine_Api::_()->touch()->deniedScripts();
  $hsContainer = $this->headScript()->setAllowArbitraryAttributes(true)->getContainer();
  $exc_indexes = array();
  $exc = false;
  foreach( $hsContainer as $key => $dat ) {
    if( !empty($dat->attributes['src']) ) {
      foreach($except_scripts as $src){
        if(false !== strpos($dat->attributes['src'], $src)){ $exc  = true;}
        if($exc) break;
      }
      if($exc){ array_push($exc_indexes, $key); $exc = false; continue; }
      $dat->attributes['dynamic'] = '1';
      if( false === strpos($dat->attributes['src'], '?') ) {
        $dat->attributes['src'] .= '?c=' . $counter;
      } else {
        $dat->attributes['src'] .= '&c=' . $counter;
      }
    }
  }
// Removing denied scripts
  foreach($exc_indexes as $i){
    $this->headScript()->__unset($i);
  }
?>
<?php echo $this->headScript()->toString()."\n" ?>

<?php echo $this->layout()->headIncludes; ?>
<meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" name="viewport">

<?php if($this->homeScreen())echo $this->homeScreen()->render(); ?>
</head>
<body id="global_page_<?php echo $request->getModuleName() . '-' . $request->getControllerName() . '-' . $request->getActionName() ?>" class="touch-body">
<div style="display: none !important;">
  <audio id = 'html5_audio'>
  </audio>
</div>

<div id="global_bind">

	<!-- Start of Boxes -->

	<div id="global_photo_box">
		<div id="photo_box_header">
				<a href="javascript://" id="photo_box_close"><?php echo $this->translate("TOUCH_Back to Album"); ?></a>
		</div>

		<div id="photo_box_loading" class="photo-loading">
			<img src="application/modules/Touch/externals/images/line_loading.gif" border="0" alt="<?php echo $this->translate('Loading') . '...'; ?>"/>
		</div>

		<div id="photo_box_photo" class="photo-body" ></div>

		<div class="photo-navigators" id="photo_box_navigators">
			<div id="photo_box_prev" class="photo-prev"></div>
			<div id="photo_box_middle" class="photo-middle"></div>
			<div id="photo_box_next" class="photo-next"></div>
		</div>
		
	</div>

  <div id="global_smooth_box">
    <button class="smooth-close" onclick="Smoothbox.close()"><?php echo $this->translate('close'); ?></button>
    <div id="smooth-body" class="smooth-body"></div>
		<div class="loader" id="smooth-loading"><?php echo $this->translate('Loading ...'); ?></div>
  </div>

	<!-- End of Boxes -->

  <div id="global_header">
    <?php echo $this->touchContent('header');?>

  </div>

  <div id='global_wrapper'>
		<div id="global_content_loading" style="display: none"><a class="loader"><?php echo $this->translate("Loading ..."); ?></a></div>
    <form method="post" action="" class="global_form touch_not_support" enctype="multipart/form-data" id = "touch_requirements" style="display: none;">
       <div>
         <div>
           <h3><?php echo $this->translate('TOUCH_This browser does not meet some requirements');?></h3>
           <p class="form-description"><?php echo $this->translate('This is version of the site allows you to access using modern mobile devices(iPhone, Android, BlackBerry, iPad and many more). It requires mobile devices to support at least Javascript and Ajax');?></p>
           <ul class="form-errors">
             <li class="js_not_support">
               <?php
               echo $this->translate('TOUCH_The browser on your mobile device does not support');
               ?>
               &nbsp;
               <?php
               echo $this->translate('TOUCH_Java Script Language');
               ?>
             </li>
             <li class="ajax_not_support">
               <?php
               echo $this->translate('TOUCH_The browser on your mobile device does not support');
               ?>
               &nbsp;
               <?php echo $this->translate('TOUCH_AJAX technology'); ?>
             </li>
             <li class="storage_api_not_support">
               <?php
               echo $this->translate('TOUCH_The browser on your mobile device does not support');
               ?>
               &nbsp;
               <?php echo $this->translate('TOUCH_HTML5 Storage API'); ?>
             </li>
           </ul>
           <div class="form-elements">
           </div>
         </div>
       </div>
    </form>
		<div id='global_content' class='content_global' >
      <?php if(Zend_Controller_Front::getInstance()->getRequest()->getParam('not_touch_integrated') && isset($this->viewer()->level_id) && $this->viewer()->level_id < 4){ ?>
      <div>
        <span class="not_touch_integrated">
          <?php echo $this->translate('TOUCH_NOT_INTEGRATED_PAGE') ?>
          <?php echo $this->translate('TOUCH_ADMINS_VIEW_ONLY_MESS') ?>
        </span>
      </div>
      <?php } ?>
      <?php echo $this->layout()->content ?>
    </div>
  </div>

  <div id="global_footer">
    <?php echo $this->touchContent('footer') ?>
  </div>

</div>
</body>
</html>