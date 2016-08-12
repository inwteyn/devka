<?php

class Heemoticon_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->errorLicense = 0;


    $hecoreApi = Engine_Api::_()->getApi('core', 'hecore');
    $product_result = $hecoreApi->checkProduct('heemoticon');

    if (isset($product_result['result']) && !$product_result['result']) {
      $this->view->errorLicenseMessage = 'heemoticon';
      $this->view->errorLicense = 1;
      return;
    }
    $this->view->typeLoad = $this->_getParam('type', 0);
    $this->filterUsedCollections();
    $viewer = Engine_Api::_()->user()->getViewer();
    $db = Engine_Db_Table::getDefaultAdapter();
    $collections = Engine_Api::_()->getDbTable('collections', 'heemoticon');
    $stickers = Engine_Api::_()->getDbTable('stickers', 'heemoticon');
    $purchaseds = Engine_Api::_()->getDbTable('purchaseds', 'heemoticon');
    $select = $db->select()
      ->from(array('c' => $collections->info('name')), array('c.name as colection_name', 'c.cover as cover'))
      ->joinLeft(array('s' => $stickers->info('name')), "s.collection_id = c.collection_id", array('s.*'))
      ->joinLeft(array('p' => $purchaseds->info('name')), "p.collection_id = c.collection_id", array('s.*'))
      ->where('p.user_id = ?', $viewer->getIdentity())
      ->where('c.status = ?', 1);
    $dc = Engine_Db_Table::getDefaultAdapter();
    $selects = $dc->select()
      ->from(array('c' => $collections->info('name')), array('count(c.collection_id) as count'))
      ->joinLeft(array('p' => $purchaseds->info('name')), "p.collection_id = c.collection_id", array())
      ->where('p.user_id = ?', $viewer->getIdentity())
      ->where('c.status = ?', 1)
      ->limit(1);
    $count = $selects->query()->fetchAll();
    $this->view->count = $count[0]['count'];
    $collections_array = $select->query()->fetchAll();
    $base_url = $this->view->baseUrl() . '/';
    $data = array();
    $wallSmiles = Engine_Api::_()->getDbTable('smiles', 'wall')->getPaginator()->getCurrentItems();
    foreach ($wallSmiles as $item) {
      $src = '';
      if ($item->file_id) {
      } else {
        $src = $base_url . $item->file_src;
      }
      $html = '<img src="' . $src . '" class="wall_smile" alt="' . $item->title . '" />';
      $json_item = $item->toArray();
      $json_item['html'] = $html;
      $json_item['title'] = $this->view->translate('WALL_' . strtoupper(str_replace(" ", "_", $json_item['title'])));
      $list_tag = array();
      foreach (explode(',', $item->tag) as $tag) {
        $list_tag[] = trim($tag);
      }
      $index_tag = (empty($list_tag[0])) ? '' : $list_tag[0];
      $json_item['index_tag'] = trim($index_tag);
      $data[] = $json_item;
    }
    $titles = array();
    $smiles = array();
    $cover = array();
    $storage = Engine_Api::_()->getItemTable('storage_file');
    foreach ($collections_array as $key => $item) {

      if ($item['cover']) {
        $file = $storage->getFile($item['cover'], 'thumb.icon');
      } else {
        $file = $storage->getFile($item['photo_id'], 'thumb.icon');
      }
      if ($file && !$cover[$item['collection_id']]) {
        $cover[$item['collection_id']] = $file->map();
      }


      $titles[$item['collection_id']]['cover'] = $cover[$item['collection_id']];
      $titles[$item['collection_id']]['collection_id'] = $item['collection_id'];
      $titles[$item['collection_id']]['name'] = $item['colection_name'];

      $smiles[$item['collection_id']]['name'] = $item['colection_name'];
      $smiles[$item['collection_id']]['collection_id'] = $item['collection_id'];
      $smile_array = array();
      foreach ($collections_array as $k => $smile) {
        $an = $storage->getFile($smile['photo_id'], 'thumb.cover');
        if ($item['collection_id'] == $smile['collection_id']) {
          $smile_array[$k]['name'] = $smile['name'];
          $smile_array[$k]['url'] = $smile['url'];
          $smile_array[$k]['url_no_animaticon'] = $an->map();
          $smile_array[$k]['id'] = $smile['sticker_id'];
        }
      }
      $smiles[$item['collection_id']]['smiles'] = $smile_array;
    }
    $this->view->standart = $data;
    $this->view->titles = $titles;
    $this->view->emoticons = $smiles;


  }

  public function addsmilesAction()
  {
    $this->filterUsedCollections();
    $viewer = Engine_Api::_()->user()->getViewer();
    $collections = Engine_Api::_()->getDbTable('collections', 'heemoticon');
    $stickers = Engine_Api::_()->getDbTable('stickers', 'heemoticon');
    $purchaseds = Engine_Api::_()->getDbTable('purchaseds', 'heemoticon');
    $db = Engine_Db_Table::getDefaultAdapter();
    $select = $db->select()
      ->from(array('c' => $collections->info('name')), array('c.name as colection_name', 'c.privacy', 'c.author as author'))
      ->joinLeft(array('s' => $stickers->info('name')), "s.collection_id = c.collection_id", array('s.*'))
      ->where('c.status = ?', 1);

    $collections_array = $select->query()->fetchAll();
    foreach ($collections_array as $key => $item) {
      $titles[$item['collection_id']]['cover'] = $item['url'];
      $titles[$item['collection_id']]['collection_id'] = $item['collection_id'];
      $smiles[$item['collection_id']]['name'] = $item['colection_name'];
      $smiles[$item['collection_id']]['collection_id'] = $item['collection_id'];
      $smiles[$item['collection_id']]['privacy'] = explode(',', $item['privacy']);
      $smiles[$item['collection_id']]['author'] = $item['author'];
      $smile_array = array();
      foreach ($collections_array as $k => $smile) {
        if ($item['collection_id'] == $smile['collection_id']) {
          $smile_array[$k]['name'] = $smile['name'];
          $smile_array[$k]['url'] = $smile['url'];
        }
      }
      $smiles[$item['collection_id']]['smiles'] = $smile_array;
      $smiles[$item['collection_id']]['used'] = $purchaseds->getUsed($item['collection_id'], $viewer->getIdentity());
    }
    $this->view->creditModuleStatus = $this->getCreditModuleStatus();
    $this->view->buyeds = $buyeds = Engine_Api::_()->getDbTable('buyeds', 'heemoticon');
    $this->view->collections = $collections;
    $this->view->emoticons = $smiles;
    $this->view->viewer_user = $viewer;
    $this->view->purchased = $purchaseds;
  }

  public function setcollectionAction()
  {
    $id = $this->_getParam('id', 0);
    $viewer = Engine_Api::_()->user()->getViewer();
    $stickers = Engine_Api::_()->getDbTable('collections', 'heemoticon');
    $select = $stickers->select()->where('collection_id=?', $id);
    $fet = $stickers->fetchRow($select);
    if ($fet) {
      $privacy = $fet->privacy;
    }
    $privacy = explode(',', $privacy);

    $price = $stickers->getPrice($id);
    $creditModuleStatus = $this->getCreditModuleStatus();
    $collectionTypeStatus = (!$price == 0) && ($creditModuleStatus);
    if (in_array($viewer->level_id, $privacy) || in_array(0, $privacy)) {
      $status = $this->_getParam('status', 0);
      $viewer = Engine_Api::_()->user()->getViewer();
      $purchaseds = Engine_Api::_()->getDbTable('purchaseds', 'heemoticon');
      $buyeds = Engine_Api::_()->getDbTable('buyeds', 'heemoticon');

      if ($status == 1) {
        //By Collection------
        if (!$buyeds->getBuyed($id, $viewer->getIdentity()) && $collectionTypeStatus) {
          $balances = Engine_Api::_()->getItem('credit_balance', $viewer->getIdentity());
          if ((int)$balances->current_credit > (int)$price) {
            Engine_Api::_()->credit()->buyEmoticon($viewer, (-1) * $price);
            $buyeds->BuyCollection($id, $viewer->getIdentity());
            $status = $purchaseds->AddCollection($id, $viewer->getIdentity());
          } else {
            echo -1;
            die;
          }
        } //-------------------
        else {
          if (!$purchaseds->getUsed($id, $viewer->getIdentity())) {
            $status = $purchaseds->AddCollection($id, $viewer->getIdentity());
          }
        }
      }

      if ($status == 2) {
        if ($purchaseds->getUsed($id, $viewer->getIdentity())) {
          $status = $purchaseds->RemoveCollection($id, $viewer->getIdentity());
        }
      }

      die($status);
    } else {
      echo 0;
      die;
    }
  }

  public function viewAction()
  {
    $id = $this->_getParam('id', 0);
    $viewer = Engine_Api::_()->user()->getViewer();

    $db = Engine_Db_Table::getDefaultAdapter();
    $collections = Engine_Api::_()->getDbTable('collections', 'heemoticon');
    $stickers = Engine_Api::_()->getDbTable('stickers', 'heemoticon');
    $purchaseds = Engine_Api::_()->getDbTable('purchaseds', 'heemoticon');
    $select = $db->select()
      ->from(array('c' => $collections->info('name')), array('c.name as colection_name', 'c.privacy', 'c.stickers_view', 'c.description', 'c.author', 'c.cover'))
      ->joinLeft(array('s' => $stickers->info('name')), "s.collection_id = c.collection_id", array('s.*'))
      ->where('c.collection_id = ?', $id);

    $collections_array = $select->query()->fetchAll();
    $storage = Engine_Api::_()->getItemTable('storage_file');


    if ($collections_array[0]['cover']) {
      $file = $storage->getFile($collections_array[0]['cover'], 'thumb.cover');
    } else {
      $file = $storage->getFile($collections_array[0]['photo_id'], 'thumb.cover');
    }
    if ($file) {
      $this->view->mainphoto = $file->map();
    }
    if (!$collections_array[0]['stickers_view']) {
      $imgs = array();
      foreach ($collections_array as $i => $item) {
        $imgs[$i] = $item['url'];
      }
      $temp = $this->drawImage($imgs, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'emoticon_array.png');
      $photo_id = $this->setSmilesEmoticons($temp);
      Engine_Api::_()->getDbTable('collections', 'heemoticon')
        ->update(array('stickers_view' => $photo_id ? $photo_id : 0), array('collection_id = ?' => $id));;
    } else {
      $photo_id = $collections_array[0]['stickers_view'];
    }
    $emoticon = $storage->getFile($photo_id);
    if ($emoticon) {
      $image = $emoticon->map();
    } else {
      $image = false;
    }

    $this->view->privacy = explode(',', $collections_array[0]['privacy']);
    $this->view->purchased = $purchaseds;
    $this->view->photoSmiles = $image;
    $this->view->viewer = $viewer;
    $this->view->emoticons = $collections_array;

  }

  public function poststickerAction()
  {
    $sticker_id = $this->_getParam('sticker_id', 0);
    $comment_id = $this->_getParam('comment_id', 0);
    $viewer = Engine_Api::_()->user()->getViewer();
    $useds = Engine_Api::_()->getDbTable('useds', 'heemoticon');
    $used_select = $useds->select()
      ->where('sticker_id = ?', $sticker_id);
    $used = $useds->fetchRow($used_select);

    $row = $useds->createRow();
    if ($used) {
      $used = $used->toArray();
      $row->user_id = $viewer->getIdentity();
      $row->sticker_id = $sticker_id;
      $row->url = $used['url'];
      $row->photo_id = $used['photo_id'];
      $row->comment_id = $comment_id;
      $row->save();
      echo $used['url'] . '?row=' . $row->used_id;
      die();
    }
    $stickers = Engine_Api::_()->getDbTable('stickers', 'heemoticon');
    $select = $stickers->select()
      ->where('sticker_id = ?', $sticker_id);
    $sticker = $stickers->fetchRow($select);
    if ($sticker) {
      $sticker = $sticker->toArray();
      $origin = explode('?', APPLICATION_PATH . DIRECTORY_SEPARATOR . $sticker['url']);
      $dest = explode(basename($origin[0]), $origin[0]);
      $nu = explode(basename($origin[0]), $sticker['url']);
      $newurluse = $nu[0] . 'work_' . basename($origin[0]);
      $new_url = $dest[0] . 'work_' . basename($origin[0]);
      $st = $this->smileCopy($origin[0], $new_url);
      $row->user_id = $viewer->getIdentity();
      $row->sticker_id = $sticker_id;
      $row->url = $newurluse;
      $row->photo_id = $sticker['photo_id'];
      $row->comment_id = $comment_id;
      $row->save();
      echo $newurluse . '?row=' . $row->used_id;
      die();
    }

  }

  public function deletefromcommentAction()
  {
    $used_id = $this->_getParam('used_id', 0);
    $sticker_id = $this->_getParam('sticker_id', 0);
    if (is_array($sticker_id)) {
      $sticker_id = $sticker_id[0];
    }
    $useds = Engine_Api::_()->getDbTable('useds', 'heemoticon');
    $used_select = $useds->select()
      ->where('used_id = ?', $used_id);
    $used = $useds->fetchRow($used_select);
    if ($used) {
      $useds = Engine_Api::_()->getDbTable('useds', 'heemoticon');
      $count_select = $useds->select('count(*)')
        ->where('sticker_id = ?', $sticker_id);
      $count = $useds->fetchAll($count_select);
      if ($count && count($count) <= 1) {
        @unlink(APPLICATION_PATH . $used->url);
      }
      $used->delete();
      die('true');
    }
    die();
  }

  public function smileCopy($source, $dest, $options = array('folderPermission' => 0755, 'filePermission' => 0755))
  {
    $result = false;

    if (is_file($source)) {
      if ($dest[strlen($dest) - 1] == DIRECTORY_SEPARATOR) {
        if (!file_exists($dest)) {
          cmfcDirectory::makeAll($dest, $options['folderPermission'], true);
        }
        $__dest = $dest . DIRECTORY_SEPARATOR . basename($source);
      } else {
        $__dest = $dest;
      }
      $result = copy($source, $__dest);
      chmod($__dest, $options['filePermission']);

    } elseif (is_dir($source)) {
      if ($dest[strlen($dest) - 1] == DIRECTORY_SEPARATOR) {
        if ($source[strlen($source) - 1] == DIRECTORY_SEPARATOR) {
          //Copy only contents
        } else {
          //Change parent itself and its contents
          $dest = $dest . basename($source);
          @mkdir($dest);
          chmod($dest, $options['filePermission']);
        }
      } else {
        if ($source[strlen($source) - 1] == DIRECTORY_SEPARATOR) {
          //Copy parent directory with new name and all its content
          @mkdir($dest, $options['folderPermission']);
          chmod($dest, $options['filePermission']);
        } else {
          //Copy parent directory with new name and all its content
          @mkdir($dest, $options['folderPermission']);
          chmod($dest, $options['filePermission']);
        }
      }

      $dirHandle = opendir($source);
      while ($file = readdir($dirHandle)) {
        if ($file != "." && $file != "..") {
          if (!is_dir($source . DIRECTORY_SEPARATOR . $file)) {
            $__dest = $dest . DIRECTORY_SEPARATOR . $file;
          } else {
            $__dest = $dest . DIRECTORY_SEPARATOR . $file;
          }

          $result = smileCopy($source . DIRECTORY_SEPARATOR . $file, $__dest, $options);
        }
      }
      closedir($dirHandle);

    } else {
      $result = false;
    }
    return $result;
  }

  public function drawImage($imgs, $file = false)
  {
    $info = array();
    $i = 0;
    $line = 0;
    $d = 1;
    $width = 530;
    $height = 0;
    $line_match = 3;
    $line_match = (int)(530 / 88);
    $square_size = 80;
    foreach ($imgs as $img) {
      if (!preg_match('~\.(jpe?g|png|gif)$~i', $img)) {
        return false;
      }
      if (!($info[$i] = getimagesize($img))) {
        return false;
      }
      $info[$i]['type'] = substr($info[$i]['mime'], 6);
      $info[$i]['width'] = 88;
      $info[$i]['width_original'] = $info[$i][0];
      $info[$i]['height_original'] = $info[$i][1];
      $info[$i]['height'] = 88;

      $info[$i]['width_original_plus'] = 0;


      $info[$i]['height_original_plus'] = 0;

      ${'create' . $d} = 'imagecreatefrom' . $info[$i]['type'];
      if (!function_exists(${'create' . $d})) return false;
      if ($i == 0) {
        $height = $height + $info[$i]['height'];
      }
      if ($line == $line_match) {
        $height = $height + $info[$i]['height'];
        $line = 0;
      } else {
        $line++;
      }
      if ($info[$i]['width_original'] > $info[$i]['height_original']) {
        $width_t = $square_size;
        $height_t = round($info[$i]['height_original'] / $info[$i]['width_original'] * $square_size);
        $off_y = ceil(($width_t - $height_t) / 2) + 10;
        $off_x = 10;
      } elseif ($info[$i]['height_original'] > $info[$i]['width_original']) {
        $height_t = $square_size;
        $width_t = round($info[$i]['width_original'] / $info[$i]['height_original'] * $square_size);
        $off_x = ceil(($height_t - $width_t) / 2) + 10;
        $off_y = 10;
      } else {
        $width_t = $height_t = $square_size;
        $off_x = $off_y = 10;
      }
      ${'image' . $d} = imagecreatetruecolor(90, 90);
      $color = imagecolorallocatealpha(${'image' . $d}, 0, 0, 0, 127);
      imagefill(${'image' . $d}, 0, 0, $color);
      imagesavealpha(${'image' . $d}, TRUE);
      imagecopyresampled(${'image' . $d}, ${'create' . $d}($img), $off_x, $off_y, 0, 0, $width_t, $height_t, $info[$i]['width_original'], $info[$i]['height_original']);
      $i++;
      $d++;
    }
    if (empty($width) || empty($height))
      return false;
    $dst = imagecreatetruecolor($width, $height);
    $color = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefill($dst, 0, 0, $color);
    imagesavealpha($dst, TRUE);
    $type = 'png';
    $j = 0;
    $width = 0;
    $width2 = 0;
    $h = 1;
    $height = 0;
    $d = 1;
    $line = 0;
    foreach ($imgs as $img) {
      if ($j == 0) {
        imagecopyresampled($dst, ${'image' . $d}, 0 + $info[$j]['width_original_plus'], $height + $info[$j]['height_original_plus'], 0, 0, $info[$j]['width'], $info[$j]['height'], $info[$j]['width'], $info[$j]['height']);
        $j++;
        $d++;
        $h = 1;
        continue;
      }
      if ($h == $line_match) {
        $height = $height + $info[$j]['height'];
        $width = 0;
        imagecopyresampled($dst, ${'image' . $d}, 0 + $info[$j]['width_original_plus'], $height + $info[$j]['height_original_plus'], 0, 0, $info[$j]['width'], $info[$j]['height'], $info[$j]['width'], $info[$j]['height']);
        $h = 1;
      } else {
        imagecopyresampled($dst, ${'image' . $d}, ($width = $width += $info[$j]['width']) + $info[$j]['width_original_plus'], $height + $info[$j]['height_original_plus'], 0, 0, $info[$j]['width'], $info[$j]['height'], $info[$j]['width'], $info[$j]['height']);
        $h++;
      }
      $j++;
      $d++;
    }
    $save = 'image' . $type;
    // header('Content-type: image/' . $type);

    if (false !== $file) {
      $save($dst, $file);
    } else {
      $save($dst);
    }
    return $file;
  }

  public function setSmilesEmoticons($url)
  {
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

    $file = $url;
    if (!$file) {
      return false;
    }
    $name = basename($file);
    $name = 'smiles_' . rand(100000, 99999999) . $name;
    $params = array(
      'parent_type' => 'heemoticon_sticker'
    );

    // Save
    $storage = Engine_Api::_()->storage();
    try {

      $iMain = $storage->create($file, $params);

    } catch (Exception $e) {
      // Remove temp files
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    @unlink($file);

    // Update row
    $file_id = $iMain->file_id;

    return $file_id;
  }

  public function viewBuyAction()
  {
    $id = $this->_getParam('id', 0);
    $viewer = Engine_Api::_()->user()->getViewer();

    $db = Engine_Db_Table::getDefaultAdapter();
    $collections = Engine_Api::_()->getDbTable('collections', 'heemoticon');
    $stickers = Engine_Api::_()->getDbTable('stickers', 'heemoticon');
    $purchaseds = Engine_Api::_()->getDbTable('purchaseds', 'heemoticon');
    $select = $db->select()
      ->from(array('c' => $collections->info('name')), array('c.name as colection_name', 'c.privacy', 'c.stickers_view', 'c.description', 'c.author', 'c.cover'))
      ->joinLeft(array('s' => $stickers->info('name')), "s.collection_id = c.collection_id", array('s.*'))
      ->where('c.collection_id = ?', $id);

    $collections_array = $select->query()->fetchAll();
    $storage = Engine_Api::_()->getItemTable('storage_file');


    if ($collections_array[0]['cover']) {
      $file = $storage->getFile($collections_array[0]['cover'], 'thumb.cover');
    } else {
      $file = $storage->getFile($collections_array[0]['photo_id'], 'thumb.cover');
    }
    if ($file) {
      $this->view->mainphoto = $file->map();
    }
    if (!$collections_array[0]['stickers_view']) {
      $imgs = array();
      foreach ($collections_array as $i => $item) {
        $imgs[$i] = $item['url'];
      }
      $temp = $this->drawImage($imgs, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'emoticon_array.png');
      $photo_id = $this->setSmilesEmoticons($temp);
      Engine_Api::_()->getDbTable('collections', 'heemoticon')
        ->update(array('stickers_view' => $photo_id ? $photo_id : 0), array('collection_id = ?' => $id));;
    } else {
      $photo_id = $collections_array[0]['stickers_view'];
    }
    $emoticon = $storage->getFile($photo_id);
    if ($emoticon) {
      $image = $emoticon->map();
    } else {
      $image = false;
    }
    $price = $collections->getPrice($id);
    $creditModuleStatus = $this->getCreditModuleStatus();
    $balances = Engine_Api::_()->getItem('credit_balance', $viewer->getIdentity());
    $buyeds = Engine_Api::_()->getDbTable('buyeds', 'heemoticon');

    $this->view->privacy = explode(',', $collections_array[0]['privacy']);
    $this->view->purchased = $purchaseds;
    $this->view->photoSmiles = $image;
    $this->view->viewer = $viewer;
    $this->view->emoticons = $collections_array;

    $this->view->buyed = $buyeds->getBuyed($id, $viewer->getIdentity());
    $this->view->buyButtonLabel = $this->view->translate("Buy with ") . $price . $this->view->translate(" credits");
    $this->view->price = $price;
    $this->view->balances = $balances->current_credit;
    $this->view->collectionTypeStatus = (!$price == 0) && ($creditModuleStatus);
  }

  //Removal from the list of paid collections, added at the switched-off condition of a plug-in of Credits.
  private function filterUsedCollections()
  {
    $ids = array();
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $collections = Engine_Api::_()->getDbTable('collections', 'heemoticon');
    $purchaseds = Engine_Api::_()->getDbTable('purchaseds', 'heemoticon');
    $buyeds = Engine_Api::_()->getDbTable('buyeds', 'heemoticon');

//      Check and Filter where Privacy
    $selectP = $collections
      ->select()
      ->setIntegrityCheck(false)
      ->from(array('collections' => $collections->info('name')), array('collections.collection_id', 'collections.privacy'))
      ->join(array('purchaseds' => $purchaseds->info('name')), 'collections.collection_id = purchaseds.collection_id', array())
      ->where('collections.privacy != ?', 0);

    $rowsP = $collections->fetchAll($selectP);
    if ($rowsP->count() > 0) {
      foreach ($rowsP as $rowP) {
        $privacy = $rowP->privacy;
        $privacy = explode(',', $privacy);
        if (!in_array($viewer->level_id, $privacy)) {
          if (!in_array($rowP->collection_id, $ids)) {
            array_push($ids, $rowP->collection_id);
          }
        }
      }
    }
//    Search of the paid kolekshn added at switched off or in lack of a plug-in of Credits
    if ($this->getCreditModuleStatus()) {
      $selectB = $collections
        ->select()
        ->setIntegrityCheck(false)
        ->from(array('collections' => $collections->info('name')), 'collections.collection_id')
        ->join(array('purchaseds' => $purchaseds->info('name')), 'collections.collection_id = purchaseds.collection_id', array())
        ->joinLeft(array('buyeds' => $buyeds->info('name')), 'purchaseds.collection_id = buyeds.collection_id', array())
        ->where('buyeds.collection_id IS NULL')
        ->where('collections.price > ?', 0);
      $rowsB = $collections->fetchAll($selectB);
      if ($rowsB->count() > 0) {
        foreach ($rowsB as $rowB) {
          if (!in_array($rowB->collection_id, $ids)) {
            array_push($ids, $rowB->collection_id);
          }
        }
      }
    }
//    Collection IDs broadcast for removal
    if(count($ids) > 0) {
      $purchaseds->RemoveCollections($ids, $viewer_id);
    }
  }

  /**
   * @return bool
   */
  private function getCreditModuleStatus()
  {
    $table = Engine_Api::_()->getDbTable('modules', 'core');
    $enabled = $table->isModuleEnabled('credit');
    if ($enabled) {
      $select = $table->select()->where('name = ?', 'credit');
      $row = $table->fetchRow($select);
      $version = $row->version;
      if (version_compare($version, '4.3.1') < 0) {
       return false;
      } else return true;
    } else {
      return false;
    }
  }
}