<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PageVideos.php 2010-09-20 17:46 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagevideo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Pagevideo_Model_DbTable_Pagevideos extends Engine_Db_Table
{
	protected $_name = 'page_videos';

	protected $_rowClass = 'Pagevideo_Model_Pagevideo';

	public function getVideos($params = array(), $details = false)
	{
		if (!empty($params['count']) && $params['count']){
			return $this->getAdapter()->fetchOne($this->getSelect($params));
		}

		$paginator = $this->getPaginator($params);

		if (!$details){
		  return $paginator;
		}

		$data = array();
		if ($paginator->getTotalItemCount()){
  		foreach ($paginator as $video){
  		  $video_id = $video->getIdentity();
  		  $data[$video_id] = $this->getVideoFileInfo($video);
  		}
		}

		return array('files' => $data, 'paginator' => $paginator);
	}

  public function getVideoFileInfo($video)
  {
    $protocol = (_ENGINE_SSL ? 'https' : 'http'); //We receive the protocol of the site for avoidance of an error "with the disabled contents"
    $core = Engine_Api::_()->getApi('core', 'hecore');
    $data = array();

    $video_id = $video->getIdentity();
    if ($video->type != 3){
      $data['embedded'] = $video->getRichContent(true);
    }

    if ($video->type == 1){
      $data['width'] = 560;
      $data['height'] = 340;
      $data['url'] = $protocol . "://www.youtube.com/v/".$video->code."&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1";
      $data['player'] = $protocol . "://www.youtube.com/v/".$video->code."&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1";
    }

    if ($video->type == 2){
      $data['width'] = 640;
      $data['height'] = 360;
      $data['url'] = $protocol . "://vimeo.com/moogaloop.swf?clip_id=".$video->code."&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1";
      $data['player'] = $protocol . "://vimeo.com/moogaloop.swf?clip_id=".$video->code."&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1";
    }

    if( $video->type == 3 && $video->status != 0 ){
      if( !empty($video->file_id) ){
        $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
        if( $storage_file ){
          $data['url'] = $storage_file->map();
          $data['width'] = 560;
          $data['height'] = 340;
					$modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
					$coreItem = $modulesTbl->getModule('core')->toArray();

					if(version_compare($coreItem['version'], '4.8.10')>=0){
						$data['player'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'home') . "externals/flowplayer/flowplayer-3.2.18.swf";
					}else{
						$data['player'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'home') . "externals/flowplayer/flowplayer-3.1.5.swf";
					}

        }
      }
    }

    $data['type'] = $video->type;
    $data['duration'] = $video->duration;
    $data['comment_count'] = $video->comment_count;
    $data['title'] = $video->getTitle();
    $data['description'] =  $core->truncate($video->getDescription(), 300, "... <a class='pagevideo_more' href='javascript:page_video.view_comments({$video_id})'>more</a>");

    return $data;
  }

	public function getVideo($params = array())
	{
		$select = $this->getSelect($params);
		return $this->fetchRow($select);
	}

	public function getSelect($params = array())
	{
		$select = $this->select();
		$vtn = $this->info('name');
//		$utn = Engine_Api::_()->getItemTable('user')->info('name');

		$select
			->setIntegrityCheck(false);

		if (!empty($params['status'])) {
		  $params['status'] = (int)$params['status'];
		  $select->where($vtn.".status = {$params['status']}");
		}

		if (!empty($params['count']) && $params['count']){
			$select
				->from($vtn, array('count' => 'COUNT(*)'))
				->group($vtn.'.page_id');
		} else {
			$select
				->from($vtn);
		}

//		$select
//			->joinLeft($utn, $utn.'.user_id = '.$vtn.'.user_id', array());

		if (!empty($params['page_id'])) {
			$select
				->where($vtn.".page_id = {$params['page_id']}");
		}

		if (!empty($params['user_id'])) {
			$select
				->where($vtn.".user_id = {$params['user_id']}");
		}

		if (!empty($params['video_id'])) {
			$select
				->where($vtn.".pagevideo_id = {$params['video_id']}");
		}

		return $select;
	}

  public function getPaginator($params = array())
  {
    $select = $this->getSelect($params);
    $paginator = Zend_Paginator::factory($select);

    if (!empty($params['ipp'])){
      $paginator->setItemCountPerPage($params['ipp']);
    }

    if (!empty($params['p'])){
      $paginator->setCurrentPageNumber($params['p']);
    }

    return $paginator;
  }
}
