<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: FAQs.php 2011-08-03 10:34 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Pagefaq_Model_DbTable_Faqs extends Engine_Db_Table
{
  public function saveFAQ($params)
    {
    if (!$params['faq_id']) {
      $data = array(
        'page_id' => $params['page_id'],
        'question' => $params['question'],
        'answer' => $params['answer'],
        'new_faq' => 1
      );
			return $this->insert($data);
    }
    
    $faqWhere = array(
      'faq_id = ?' => $params['faq_id'],
      'page_id = ?' => $params['page_id']
    );
		return $this->update(array('question' => $params['question'], 'answer' => $params['answer']), $faqWhere);
  }

  public function getFAQ($faq_id)
  {
    if ($faq_id) {
      return $this->fetchRow($this->select()->where('faq_id = ?', $faq_id))->toArray();
    }
    $result = $this->fetchRow($this->select()->where('new_faq = ?', 1))->toArray();

      $faqWhere = array('new_faq = 1');
			$this->update(array('new_faq' => 0), $faqWhere);

      return $result;
    }

  public function getAllFAQ($page_id)
  {
    $select = $this->select()
      ->where('page_id = ?', $page_id )
      ->order('faq_id ASC');

    return $this->fetchAll($select);
  }

  public function deleteFaq($faq_id)
  {
    $this->delete(array('faq_id = ?' => $faq_id));
  }
}