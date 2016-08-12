<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 02.04.12 09:12 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hebadge
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hebadge_Form_Admin_Badge_Edit extends Hebadge_Form_Admin_Badge_Create
{
  public function init()
  {
    parent::init();

    $this->setTitle('HEBADGE_FORM_ADMIN_BADGE_EDIT_TITLE');
    $this->setDescription('HEBADGE_FORM_ADMIN_BADGE_EDIT_DESCRIPTION');


  }


}