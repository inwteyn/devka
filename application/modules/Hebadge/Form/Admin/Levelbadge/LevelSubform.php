<?php
/**
 * Created by Hire-Experts LLC.
 * Author: Mirlan;
 * Date: 20.08.2015
 * Time: 14:02
 */
 
 class Hebadge_Form_Admin_Levelbadge_LevelSubform extends Hebadge_Form_Subform
 {
   public function init()
   {
     $this->setTitle('Requirense');
//     $this->setDescription('WALL_ADMIN_PRIVACY_DESCRIPTION');

     $levelsTable = Engine_Api::_()->getDbTable('levels','authorization');
     $select = $levelsTable->select();
     $levels = $levelsTable->fetchAll($select);

     foreach ($levels as $level) {
       $this->addElement('checkbox', 'level_'.$level->level_id,
         array(
           'label' => $level->title,
         )
       );
     }

   }
 }