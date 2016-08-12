<?php
/**
 * @category   Application_Extensions
 * @package    Heemoticon
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Heemoticon_Model_DbTable_Useds extends Engine_Db_Table
{
    public function changeUsedStickerStatus($sticker_id, $status)
    {
        $this->update(array('status' => $status),
            array('sticker_id = ?' => $sticker_id));
    }
}
