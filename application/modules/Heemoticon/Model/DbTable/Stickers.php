<?php

/**
 * Created by PhpStorm.
 * User: Медербек
 * Date: 03.03.2015
 * Time: 13:57
 */
class Heemoticon_Model_DbTable_Stickers extends Engine_Db_Table
{

    protected $_rowClass = 'Heemoticon_Model_Sticker';

    public function getSickersByCollectionID($collection_id)
    {
        $select_stickers = $this->select()
            ->where('collection_id = ?', $collection_id)
            ->order('order');

        $stickers = $this->fetchAll($select_stickers);

        return $stickers;
    }

    public function deleteSticker($ids)
    {
        $filesDB = Engine_Api::_()->getDbtable('files', 'storage');

        if (is_array($ids)) {
            foreach ($ids as $id) {

                Engine_Api::_()->getDbTable('useds', 'heemoticon')->changeUsedStickerStatus($id, 0);

                // delete files from server
                $filesDB = Engine_Api::_()->getDbtable('files', 'storage');

                $filePath = $filesDB->fetchRow($filesDB->select()->where('file_id = ?', $id))->storage_path;
                unlink($filePath);

                $thumbPath = $filesDB->fetchRow($filesDB->select()->where('parent_file_id = ?', $id))->storage_path;
                unlink($thumbPath);

                // Delete image and thumbnail
                $filesDB->delete(array('file_id = ?' => $id));
                $filesDB->delete(array('parent_file_id = ?' => $id));

                // Delete sticker
                $this->delete(array('sticker_id = ?' => $id));
            }

        } else {

            Engine_Api::_()->getDbTable('useds', 'heemoticon')->changeUsedStickerStatus($ids, 0);

            // delete files from server
            $filesDB = Engine_Api::_()->getDbtable('files', 'storage');

            $filePath = $filesDB->fetchRow($filesDB->select()->where('file_id = ?', $ids))->storage_path;
            unlink($filePath);

            $thumbPath = $filesDB->fetchRow($filesDB->select()->where('parent_file_id = ?', $ids))->storage_path;
            unlink($thumbPath);

            // Delete image and thumbnail
            $filesDB->delete(array('file_id = ?' => $ids));
            $filesDB->delete(array('parent_file_id = ?' => $ids));

            // Delete sticker
            $this->delete(array('sticker_id = ?' => $ids));
        }

    }

    public function deleteCollectionStickers($collection_id)
    {
        $stickers = $this->getSickersByCollectionID($collection_id);

        // delete files from server
        $filesDB = Engine_Api::_()->getDbtable('files', 'storage');

        foreach ($stickers as $sticker) {

            Engine_Api::_()->getDbTable('useds', 'heemoticon')->changeUsedStickerStatus($sticker->getIdentity(), 0);

            $filePath = $filesDB->fetchRow($filesDB->select()->where('file_id = ?', $sticker['photo_id']));
            if($filePath) {
                @unlink($filePath->storage_path);
                $filePath->delete();
            }

            $thumbPath = $filesDB->fetchRow($filesDB->select()->where('parent_file_id = ?', $sticker['photo_id']));
            if($thumbPath){
                @unlink($thumbPath->storage_path);
                $thumbPath->delete();
            }
        }

        $this->delete(array('collection_id = ?' => $collection_id));

    }
}