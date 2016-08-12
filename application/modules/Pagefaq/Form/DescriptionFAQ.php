<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DescriptionFAQ.php 2011-09-28 15:18 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Pagefaq
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
class Pagefaq_Form_DescriptionFAQ extends Engine_Form
{
  
	public function init()
	{
		$this
			->setAttrib('id', 'description_form_faq')
			->setAttrib('class', 'description_form_faq');

    $this->addElement('TinyMce', 'descriptionFAQ', array());

    $this->getView()->getHelper('TinyMce')->setOptions(array('mode' => 'exact',
			'elements' => 'descriptionFAQ'));

    $this->addElement('Hidden', 'description_id', array());
  }
}