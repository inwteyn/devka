<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 26.11.10
 * Time: 16:53
 * To change this template use File | Settings | File Templates.
 */
 
class Updates_Model_DbTable_Widgets extends Engine_Db_Table
{
	protected $_name = 'updates_widgets';
	protected $_serializedColumns = array('params');

	public function getWidgets()
  {
		$moduleTb = Engine_Api::_()->getDbtable('modules', 'core');

		$widgets = $this->fetchAll($this->select()->order('id ASC'));
    $contentWidgets = array();
  	foreach ($widgets as $widget)
  	{
			if ($moduleTb->isModuleEnabled($widget->module))
			{
				$module = $moduleTb->getModule($widget->module);

				$contentWidgets[$module->title][] = array(
							'widget_id' => $widget->id,
							'title' => $widget->title,
							'description' => $widget->description,
							'type' => 'widget',
							'name' => $widget->name,
              'module'=> $widget->module,
              'params'=> $widget->params,
              'structure'=>$widget->structure,
              'last_sent_id'=>$widget->last_sent_id,
              'blacklist'=>$widget->blacklist,
							);
			}
  	}

    return $contentWidgets;
  }

	public function getWidget($params = array('id'=>0, 'name'=>'', 'module'=>''))
	{
		if (!is_array($params)){
			return false;
		}

		if (isset($params['id']) && is_numeric($params['id']) && $params['id']>0){
			$select = $this->select()->where('id=?', $params['id'])->limit(1);
		} elseif(isset($params['name']) && $params['name'] != '') {
			$select = $this->select()->where('name=?', $params['name'])->limit(1);
		} elseif(isset($params['module'])){
      $select = $this->select()->where('module=?', $params['module'])->limit(1);
    }

		if(isset($select)){
			return $this->fetchRow($select);
		} else {
			return false;
		}


	}
}
