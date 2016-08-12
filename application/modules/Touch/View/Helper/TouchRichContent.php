<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TouchRichContent.php 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Touch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Engine_View_Helper_TouchRichContent extends Zend_View_Helper_Abstract
{

  public function touchRichContent($item, $params = array())
  {
    if (!($item instanceof Core_Model_Item_Abstract)){
      return ;
    }
    if ($item instanceof Video_Model_Video){
      $call_params = array($item);
      $call_params = array_merge($call_params, $params);
      return call_user_func_array(array($this, 'getVideoRichContent'), $call_params);
    }
    if ($item instanceof Music_Model_Playlist){
      $call_params = array($item);
      $call_params = array_merge($call_params, $params);
      return call_user_func_array(array($this, 'getMusicRichContent'), $call_params);
    }
    if (!is_callable(array($item, 'getRichContent'))){
      return ;
    }
    return call_user_func_array(array($item, 'getRichContent'), $params);

  }

  public function getVideoRichContent(Video_Model_Video $item, $view = false, $params = array())
  {

      // prepare the duration
      //
      $video_duration = "";
      if( $item->duration ) {
        if( $item->duration >= 3600 ) {
          $duration = gmdate("H:i:s", $item->duration);
        } else {
          $duration = gmdate("i:s", $item->duration);
        }
        $duration = ltrim($duration, '0:');

        $video_duration = "<span class='video_length'>".$duration."</span>";
      }

      // prepare the thumbnail
      $thumb = Zend_Registry::get('Zend_View')->itemPhoto($item, 'thumb.video.activity');

      if ($item->photo_id){
        $thumb = Zend_Registry::get('Zend_View')->itemPhoto($item, 'thumb.video.activity');
      }
      else {
        $thumb = '<img alt="" src="application/modules/Video/externals/images/video.png">';
      }

		$videoEmbedded = '';
    // if video type is youtube
    if ($item->type == 1){
			$thumb = '<a id="video_thumb_'.$item->video_id.'" class="video_thumb" href="http://www.youtube.com/v/' . $item->code .' " target="_blank">
					<div class="video_thumb_wrapper">'.$video_duration.$thumb.'</div>
					</a>';
    }
    // if video type is vimeo
    if ($item->type == 2){
      $thumb = '<a id="video_thumb_'.$item->video_id.'" class="video_thumb" href="http://vimeo.com/' . $item->code .' " target="_blank">
					<div class="video_thumb_wrapper">'.$video_duration.$thumb.'</div>
					</a>';
    }

    // if video type is uploaded
    if ($item->type ==3){
      $video_location = Engine_Api::_()->storage()->get($item->file_id, $item->getType())->getHref();
      $videoEmbedded = $this->compileFlowPlayer($item, $video_location, $view);

      $thumb = '<a id="video_thumb_'.$item->video_id.'" class="video_thumb" href="javascript:void(0);" onclick="javascript: $(\'videoFrame'.$item->video_id.'\').style.display=\'block\'; $(\'videoFrame'.$item->video_id.'\').src = $(\'videoFrame'.$item->video_id.'\').src; var myElement = $(this); myElement.style.display=\'none\'; var next = myElement.getNext(); next.style.display=\'block\';">
                <div class="video_thumb_wrapper">'.$video_duration.$thumb.'</div>
                </a>';
    }

		if($view==false){
      // prepare title and description
      $title = "<a href='".$item->getHref($params)."'>$item->title</a>";
      $tmpBody = Engine_String::strip_tags($item->description);
      $description = "<div class='video_desc'>".(Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody)."</div>";

      $videoEmbedded = $thumb.'<div id="video_object_'.$item->video_id.'" class="video_object">'.$videoEmbedded.'</div><div class="video_info">'.$title.$description.'</div>';
    } else {
			 $videoEmbedded = $thumb.'<div id="video_object_'.$item->video_id.'" class="video_object">'.$videoEmbedded.'</div>';
		}

    return $videoEmbedded;
  }

  public function compileFlowPlayer(Video_Model_Video $item, $location, $view)
  {
    //    php echo $item->baseUrl() /externals/flowplayer/flowplayer-3.1.5.swf"
    $embedded = "
    <div id='videoFrame".$item->video_id."' style='height:".($view ? '360px;' : '230px;')."'></div>
    <script type='text/javascript'>
    en4.core.runonce.add(function(){ if (Touch.isFlash()){ \$('video_thumb_".$item->video_id."').removeEvents('click').addEvent('click', function(){flashembed('videoFrame$item->video_id',{src: '".Zend_Registry::get('Zend_View')->baseUrl()."/externals/flowplayer/flowplayer-3.1.5.swf', wmode: 'opaque'},{config: {clip: {url: '$location',autoPlay: ".($view?"false":"true").", duration: '$item->duration', autoBuffering: true},plugins: {controls: {background: '#000000',bufferColor: '#333333',progressColor: '#444444',buttonColor: '#444444',buttonOverColor: '#666666'}},canvas: {backgroundColor:'#000000'}}});}) } else { \$('video_thumb_".$item->video_id."').removeEvents('click').set('onclick', '').addEvent('click', function(){ alert('".$this->view->translate('TOUCH_NOT_SUPPORTED_FLASH')."');}); }});
    </script>";

    return $embedded;
  }
  public function getMusicRichContent(Music_Model_Playlist $item, $view = false, $params = array())
  {
    $playerEmbed = '';

    // $view == false means that this rich content is requested from the activity feed
    if( !$view ) {
      $desc   = strip_tags($item->description);
      $desc   = "<div class=''>".(Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc)."</div>";
      $zview  = Zend_Registry::get('Zend_View');
      $zview->playlist     = $item;
      $zview->songs        = $item->getSongs();
      $playerEmbed       = $desc . $zview->render('application/modules/Touch/modules/Music/views/scripts/_EmptyFile.tpl');
    }

    // hide playlist if in production mode
    if (!count($zview->songs) && 'production' == APPLICATION_ENV) {
      throw new Exception('Empty playlists show not be shown');
    }

    return $playerEmbed;
  }

}