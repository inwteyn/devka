<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Gift.php 03.02.12 16:19 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Heemoticon
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Heemoticon_Model_Collection extends Core_Model_Item_Abstract
{
    public function getStickersCount()
    {
        $stick_db = Engine_Api::_()->getDbTable('stickers', 'heemoticon');
        $coll_db = Engine_Api::_()->getDbTable('collections', 'heemoticon');

        $coll_db_name = $coll_db->info('name');
        $stick_db_name = $stick_db->info('name');

        $stickers_count = $coll_db->select()
            ->setIntegrityCheck(false)
            ->from($coll_db_name, array('count' => 'COUNT(*)'))
            ->joinInner($stick_db_name, "$stick_db_name.collection_id = $coll_db_name.collection_id", '')
            ->where("$coll_db_name.collection_id = ?", $this->collection_id)
            ->query()
            ->fetchColumn();

        return $stickers_count;
    }

    public function setStickers($photos_ids, $order)
    {
        $sticker_table = Engine_Api::_()->getItemTable('sticker');
        $stick_db = $sticker_table->getAdapter();
        $stick_db->beginTransaction();
        $sticker_values = array();

        try {

            for ($i = 0; $i < sizeof($order); $i++) {
                if (in_array($order[$i], $photos_ids)) {

                    $sticker = $sticker_table->createRow();
                    $sticker_values['type'] = 1;
                    $sticker_values['name'] = ' ';
                    $sticker_values['photo_id'] = $order[$i];
                    $sticker_values['url'] = Engine_Api::_()->getItem('storage_file', $order[$i])->storage_path;
                    $sticker_values['collection_id'] = $this->getIdentity();
                    $sticker_values['order'] = $i;
                    $sticker->setFromArray($sticker_values);
                    $sticker->save();

                } else {

                    $sticker_table->update(array('order' => $i), array(
                        'collection_id = ?' => $this->getIdentity(),
                        'photo_id = ?' => $order[$i]
                    ));
                }
            }

            $stick_db->commit();

        } catch (Exception $e) {
            $stick_db->rollBack();
            throw $e;
        }
    }

    public function getCollectionIconUrl()
    {
        if ($this->cover) {
            if ($this->getStickersCount()) {
                $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->cover, 'thumb.icon')->map();
            } else {
                $file = null;
            }
        } else {
            $stick_db = Engine_Api::_()->getDbTable('stickers', 'heemoticon');

            $select = $stick_db->select()
                ->setIntegrityCheck(false)
                ->where("collection_id = ?", $this->collection_id)
                ->limit(1);

            $sticker = $stick_db->fetchRow($select);

            if ($sticker && $sticker['photo_id']) {
                $file = Engine_Api::_()->getItemTable('storage_file')->getFile($sticker['photo_id'], 'thumb.icon')->map();
            } else {
                $file = null;
            }
        }
        return $file;
    }
}