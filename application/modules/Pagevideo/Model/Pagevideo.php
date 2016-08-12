<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageVideo.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Model_Pagevideo extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'page';
  
  protected $_type = 'pagevideo';

  protected $_owner_type = 'user';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'page_view',      
      'page_id' => $this->getParentPage()->url,
      'tab' => 'video',
      'content_id' => $this->getIdentity(),
    ), $params);
    
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }
  
  public function getAuthorizationItem()
  {
    return $this->getParent('page');
  }
  
  public function getLink()
  {
    return sprintf("<a href='%s'>%s</a>", $this->getHref(), $this->getTitle());
  }
  
  public function getPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }
  
  public function getParentPage()
  {
    return Engine_Api::_()->getItem('page', $this->page_id);
  }

	public function removeTags()
	{
		$tagTable = Engine_Api::_()->getDbTable('tagMaps', 'page');
		$tagTable->delete(array('resource_id = ?' => $this->getIdentity(), 'resource_type = ?' => $this->getType()));
	}

	public function removePhotos()
  {
    $storage = Engine_Api::_()->storage();
    $file_id = $this->photo_id;
		
		if ($file_id){
			$file = $storage->get($file_id);
			if ($file){
				$file->delete();
			}
			$file = $storage->get($file_id, 'thumb.mini');
			if ($file){
				$file->delete();
			}
      $file = $storage->get($file_id, 'thumb.icon');
			if ($file){
				$file->delete();
			}
		}
  }
  
  public function delete()
  {
    $table = $this->getTable();
    $db = $table->getAdapter();
    $prefix = $table->getTablePrefix();
    
    $where = "resource_type = '{$this->getType()}' AND resource_id = {$this->getIdentity()}";
    
    $db->delete($prefix.'core_comments', $where);
    $db->delete($prefix.'core_likes', $where);
    $db->delete($prefix.'core_tagmaps', $where);
    
    $where = "object_type = '{$this->getType()}' AND object_id = {$this->getIdentity()}";
    $db->delete($prefix.'activity_notifications', $where);

		$this->removeTags();
		$this->removePhotos();

		$search_api = Engine_Api::_()->getDbTable('search', 'page');
		$search_api->deleteData($this);
    
    parent::delete();
  }
  
  public function getRichContent($view = false, $params = array())
  {
    // if video type is youtube
    if ($this->type == 1){
      $videoEmbedded = $this->compileYouTube($this->code, $view);
    }
    // if video type is vimeo
    if ($this->type == 2){
      $videoEmbedded = $this->compileVimeo($this->code, $view);
    }

    // if video type is uploaded
    if ($this->type == 3){
      $video_location = Engine_Api::_()->storage()->get($this->file_id, $this->getType())->getHref();
      $videoEmbedded = $this->compileFlowPlayer($video_location, $view);
    }

    // $view == false means that this rich content is requested from the activity feed
    if($view == false){
      // prepare the duration
      //
      $video_duration = "";
      if($this->duration){
        if($this->duration>360) $duration = gmdate("H:i:s", $this->duration);
        else $duration = gmdate("i:s", $this->duration);
        if ($duration[0] =='0') $duration= substr($duration,1);
        $video_duration = "<span class='video_length'>".$duration."</span>";
      }
      
      // prepare the thumbnaile
      $thumb = Zend_Registry::get('Zend_View')->itemPhoto($this, 'thumb.video.activity');
  
      if ($this->photo_id){
        $thumb = Zend_Registry::get('Zend_View')->itemPhoto($this, 'thumb.video.activity');
      }
      else {
        $thumb = '<img alt="" src="application/modules/Video/externals/images/video.png">';
      }
  
      $thumb = '<a id="video_thumb_'.$this->getIdentity().'" style="" href="javascript:void(0);" onclick="javascript:var myElement = $(this);myElement.style.display=\'none\';var next = myElement.getNext(); next.style.display=\'block\';">
                <div class="video_thumb_wrapper">'.$video_duration.$thumb.'</div>
                </a>';
  
      // prepare title and description
      $title = "<a href='".$this->getHref($params)."'>$this->title</a>";
      $tmpBody = Engine_String::strip_tags($this->description);
      $description = "<div class='video_desc'>".(Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody)."</div>";
  
      $videoEmbedded = $thumb.'<div id="video_object_'.$this->getIdentity().'" style="display:none;">'.$videoEmbedded.'</div><div class="video_info">'.$title.$description.'</div>';
    }
    
    return $videoEmbedded;
  }

  public function compileYouTube($code, $view)
  {
    //560 x 340
    $protocol = (_ENGINE_SSL ? 'https' : 'http'); //We receive the protocol of the site for avoidance of an error "with the disabled contents"
    $embedded = '
    <object width="'.($view?"560":"425").'" height="'.($view?"340":"344").'">
    <param name="movie" value="' . $protocol . '://www.youtube.com/v/'.$code.'&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1"/>
    <param name="allowFullScreen" value="true"/>
    <param name="allowScriptAccess" value="always"/>
    <param name="wmode" value="transparent" />
    <embed wmode="transparent" src="' . $protocol . '://www.youtube.com/v/'.$code.'&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1'.($view?"":"&autoplay=1").'" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="'.($view?"560":"425").'" height="'.($view?"340":"344").'"/>
    </object>';

    return $embedded;
  }
  
  public function compileVimeo($code, $view)
  {
    //640 x 360
    $protocol = (_ENGINE_SSL ? 'https' : 'http'); //We receive the protocol of the site for avoidance of an error "with the disabled contents"
    $embedded = '
    <object width="'.($view?"640":"400").'" height="'.($view?"360":"230").'">
    <param name="allowFullScreen" value="true" />
    <param name="allowScriptAccess" value="always" />
    <param name="wmode" value="transparent" />
    <param name="movie" value="' . $protocol . '://vimeo.com/moogaloop.swf?clip_id='.$code.'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1" />
    <embed wmode="transparent" src="'. $protocol . '://vimeo.com/moogaloop.swf?clip_id='.$code.'&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1'.($view?"":"&autoplay=1").'" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'.($view?"640":"400").'" height="'.($view?"360":"230").'"/>
    </object>';

    return $embedded;
  }

  public function compileFlowPlayer($location, $view)
  {
    //    php echo $this->baseUrl() /externals/flowplayer/flowplayer-3.1.5.swf"
    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $coreItem = $modulesTbl->getModule('core')->toArray();

    if(version_compare($coreItem['version'], '4.8.10')>=0){
      $embedded = "
    <script type='text/javascript'>
    en4.core.runonce.add(function(){\$('video_thumb_".$this->getIdentity()."').removeEvents('click').addEvent('click', function(){flashembed('video_object_{$this->getIdentity()}',{src: '".Zend_Registry::get('Zend_View')->baseUrl()."/externals/flowplayer/flowplayer-3.2.18.swf', width: ".($view?"480":"420").", height: ".($view?"386":"326").", wmode: 'transparent'},{config: {clip: {url: '$location',autoPlay: ".($view?"false":"true").", duration: '$this->duration', autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});});});
    </script>";
    }else{
      $embedded = "
    <script type='text/javascript'>
    en4.core.runonce.add(function(){\$('video_thumb_".$this->getIdentity()."').removeEvents('click').addEvent('click', function(){flashembed('video_object_{$this->getIdentity()}',{src: '".Zend_Registry::get('Zend_View')->baseUrl()."/externals/flowplayer/flowplayer-3.1.5.swf', width: ".($view?"480":"420").", height: ".($view?"386":"326").", wmode: 'transparent'},{config: {clip: {url: '$location',autoPlay: ".($view?"false":"true").", duration: '$this->duration', autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});});});
    </script>";
    }



    return $embedded;
  }
  
  public function getKeywords($separator = ' ')
  {
    $keywords = array();
    foreach( $this->tags()->getTagMaps() as $tagmap ) {
      $tag = $tagmap->getTag();
      $keywords[] = $tag->getTitle();
    }

    if( null === $separator ) {
      return $keywords;
    }

    return join($separator, $keywords);
  }
  
  // Interfaces

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
  
  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'page'));
  }
  
  public function getParent($type = null)
  {
    return $this->getPage();
  }
}