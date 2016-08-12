<?php


class Hetools_Db_Export_Mysql extends Engine_Db_Export_Mysql
{


  protected function _fetchTableData($table)
  {
    $adapter = $this->getAdapter();
    $quotedTable = $this->getAdapter()->quoteIdentifier($table);

    $output = '';

    // Get data
    $sql = 'SELECT * FROM ' . $quotedTable ;

    $table_where = $this->getParam('selectWhere');
    if (!empty($table_where[$table])){
      $sql .= " WHERE " . $table_where[$table];
    }



    $stmt = $adapter->query($sql);
    $first = true;
    $columns = null;
    $written = 0;

    $autoincrement = $this->getParam('autoincrement');

    $sql_query = "";

    while( false != ($row = $stmt->fetch()) ) {

      // Add insert
      if( !$this->getParam('insertExtended', true) || $first ) {
        $sql_query .= 'INSERT ';
        if( $this->getParam('insertIgnore', false) ) {
          $sql_query .= 'IGNORE ';
        }
        $sql_query .= 'INTO ' . $quotedTable . ' ';
        // Complete
        if( $this->getParam('insertComplete', true) ) {
          if( empty($columns) ) {
            $columns = array_map(array($adapter, 'quoteIdentifier'), array_keys($row));

            foreach ($columns as $key => $item){
              if (!empty($autoincrement[$table]) && $autoincrement[$table] == str_replace("`","", $item)){
                unset($columns[$key]);
              }
            }

            $columns = implode(', ', $columns);
          }
          $sql_query .= '(' . $columns . ') ';
          $sql_query .= 'VALUES ';
        }
        $sql_query .= PHP_EOL;
      }
      // Other wise we are continuing a previous query
      else {
        $sql_query .= ',';
        $sql_query .= PHP_EOL;
      }

      // Add data
      $data = array();
      foreach( $row as $key => $value ) {

        if (!empty($autoincrement[$table]) && $autoincrement[$table] == $key){
          continue ;
        }

        if( null === $value ) {
          $data[$key] = 'NULL';
        } else {
          $data[$key] = '\'' . addcslashes($value, '\'') . '\'';
        }
      }


      $sql_query .= '(' . implode(', ', $data) . ')';


      // Save to file
      if( !empty($output) ) {
        $written++;
        $this->_write($output);
        $output = '';
      }

      $first = false;
    }

    if (!empty($sql_query)){
      $output .= $this->escapePhp($sql_query) . PHP_EOL;
    }

    // Finish up
    if( $written ) {
      $output .= PHP_EOL . PHP_EOL;
    }
    $output .= PHP_EOL;

    if( !empty($output) ) {
      $this->_write($output);
      $output = '';
    }
  }

  protected function _fetchHeader()
  {
    return '';
  }
  protected function _fetchFooter()
  {
    return '';
  }
  protected function _fetchComment($comment = '')
  {
    return '';
  }

  protected function _fetchTableDataHeader($table)
  {
    return '';
  }

  protected function _fetchTableSchemaHeader($table)
  {
    return '';
  }
  protected function _fetchTableSchema($table)
  {
    $adapter = $this->getAdapter();

    $quotedTable = $this->getAdapter()->quoteIdentifier($table);
    $result = $this->_queryRaw('SHOW CREATE TABLE ' . $quotedTable);
    $result = $result[0]['Create Table'];

    $output = '';

    if( $this->getParam('dropTable', true) ) {
      $output .= 'DROP TABLE IF EXISTS ' . $quotedTable . self::EOQ . PHP_EOL;
    }
    if( $this->getParam('tableIfExists', true) ) {
      $result = str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $result);
    }

    $output .= $this->escapePhp($result);
    $output .= PHP_EOL . PHP_EOL;

    return $output;
  }


  public function escapePhp($str)
  {
    if ($this->getParam('escapePhp')){
      return escapePhp($str);
    } else {
      return $str;
    }
  }


}


function escapePhp($str)
{
  return '$db->query("'.addcslashes($str, '"$').'");';

}
function escapeWithoutVarPhp($str)
{
  return '$db->query("'.addcslashes($str, '"').'");';

}

$db = Engine_Db_Table::getDefaultAdapter();

$pages = array();
foreach ($db->query("SELECT * FROM engine4_core_pages WHERE fragment = 0 AND name NOT LIKE '%user_%' AND name NOT LIKE '%core_%' AND name NOT LIKE '%classified_%' AND name NOT LIKE '%music_%' AND name NOT LIKE '%video_%' AND name NOT LIKE '%forum_%' AND name NOT LIKE '%group_%' AND name NOT LIKE '%blog_%' AND name NOT LIKE '%mobi_%' AND name NOT LIKE '%chat_%' AND name NOT LIKE '%invite_%' AND name NOT LIKE '%event_%' AND name NOT LIKE '%album_%' AND name NOT LIKE '%poll_%' AND name NOT LIKE '%message_%'")->fetchAll() as $item){
  $pages[$item['page_id']] = $item['title'];
}


$form = new Engine_Form();
$form->setTitle('Generator SQL queries');
$form->addElement('text', 'm', array('label' => 'Module Key', 'value' => 'module'));

$form->addElement('checkbox', 'module_tables', array(
  'label' => 'Export tables, where name have prefix engine4_{module}',
  'value' => 1
));

$form->addElement('multiCheckbox', 'tables', array(
  'label' => 'Export module rows from Tables',
  'multiOptions' => array(
    'engine4_activity_actiontypes'  =>  'engine4_activity_actiontypes',
    'engine4_activity_notificationtypes'  =>  'engine4_activity_notificationtypes',
    'engine4_core_mailtemplates'  =>  'engine4_core_mailtemplates',
    'engine4_core_menuitems'  =>  'engine4_core_menuitems',
    'engine4_core_jobtypes' => 'engine4_core_jobtypes'
  ),
  'value' => array(
    'engine4_activity_actiontypes'  =>  'engine4_activity_actiontypes',
    'engine4_activity_notificationtypes'  =>  'engine4_activity_notificationtypes',
    'engine4_core_mailtemplates'  =>  'engine4_core_mailtemplates',
    'engine4_core_menuitems'  =>  'engine4_core_menuitems',
    'engine4_core_jobtypes' => 'engine4_core_jobtypes'
  )
));


$form->addElement('multiCheckbox', 'pages', array(
  'multiOptions' => $pages,
  'label' => 'Export Structure pages'
));

$form->addElement('button', 'submit', array(
  'type' => 'submit',
  'value' => 'Submit'
));

$request = Zend_Controller_Front::getInstance()->getRequest();

if ($request->isPost() && $form->isValid($request->getPost())){

  $values = $form->getValues();



$sql_data = '
  /*
&nbsp;&nbsp;SQL file | '.$values['m'].' | '.date("d.m.y H:i.s").'
  */
' . PHP_EOL;

  $sql_data .= "\$db = Engine_Db_Table::getDefaultAdapter();" . PHP_EOL . PHP_EOL;


  $module_tables = array();
  if ($values['module_tables']){
    foreach ($db->query("SHOW TABLES")->fetchAll() as $item){
      $item = array_values($item);
      $item = $item[0];
      if (strpos($item, "engine4_" . $values['m']) === false){
        continue ;
      }
      $module_tables[] = $item;
    }
  }


  foreach ($module_tables as $item){
    $export = new Hetools_Db_Export_Mysql($db, array(
      'tables' => array($item),


      'dropTable' => false,
      'fetchStructure' => true,
      'fetchData' => false,
      'fullInserts' => false,
      'insertIgnore' => true,
      'insertComplete' => true,
      'insertExtended' => true,
      'comments' => false,
      'tableIfExists' => true,
      'escapePhp' => true
    ));

    $sql_data .= $export->toString();
  }


  $simple_tables = array(
    'engine4_activity_actiontypes' => array(
      'select_where' => 'module = "'.$values['m'].'"',
      'autoincrement' => 'actiontype_id'
    ),
    'engine4_activity_notificationtypes' => array(
      'select_where' => 'module = "'.$values['m'].'"',
      'autoincrement' => 'notificationtype_id'
    ),
    'engine4_core_mailtemplates' => array(
      'select_where' => 'module = "'.$values['m'].'"',
      'autoincrement' => 'mailtemplate_id'
    ),
    'engine4_core_menuitems' => array(
      'select_where' => 'module = "'.$values['m'].'"',
      'autoincrement' => 'id'
    ),
    'engine4_core_jobtypes' => array(
      'select_where' => 'module = "'.$values['m'].'"',
      'autoincrement' => 'jobtype_id'
    )
  );


  foreach ($simple_tables as $key => $item){
    if (!in_array($key, $values['tables'])){
      unset($simple_tables[$key]);
    }
  }



  $tables = array();
  foreach ($simple_tables as $key => $item){
    $tables[] = $key;
  }
  $selectWhere = array();
  foreach ($simple_tables as $key => $item){
    $selectWhere[$key] = $item['select_where'];
  }
  $autoincrement = array();
  foreach ($simple_tables as $key => $item){
    $autoincrement[$key] = $item['autoincrement'];
  }


  $export = new Hetools_Db_Export_Mysql($db, array(
    'tables' => $tables,
    'selectWhere' => $selectWhere,
    'autoincrement' => $autoincrement,

    'dropTable' => false,
    'fetchStructure' => false,
    'fetchData' => true,
    'fullInserts' => false,
    'insertIgnore' => true,
    'insertComplete' => true,
    'insertExtended' => true,
    'comments' => false,
    'escapePhp' => true

  ));

  $sql_data .= $export->toString();


  $columns = array(
    'name', 'displayname', 'url', 'title', 'description', 'keywords', 'custom', 'fragment', 'layout', 'levels', 'provides', 'view_count'
  );


  function sqlCreateFromItem($table, $keys, $item)
  {
    $sql = "INSERT IGNORE INTO `$table` (`".implode("`, `", $keys)."`) VALUES (";
    foreach ($keys as $c){
      if (empty($item[$c])){
        $sql .= "NULL, ";
      } else if (strpos("$", $item) >= 0 && false){
        $sql .= "$item[$c], ";
      } else {
        $sql .= "'".addcslashes($item[$c], "'")."', ";
      }
    }
    $sql = substr($sql, 0, -2);
    $sql .= ")";
    return $sql;
  }


  function recursiveContent($content, $page_str = "NULL", $parent_content_str = "NULL", $level=0)
  {
    $db = Engine_Db_Table::getDefaultAdapter();

    $columns = array(
      'page_id', 'type', 'name', 'parent_content_id', 'order', 'params', 'attribs'
    );

    $sql = "";
    $content['page_id'] = $page_str;
    $content['parent_content_id'] = $parent_content_str;

    $sql .= "&nbsp;&nbsp;";
    $sql .= escapeWithoutVarPhp(sqlCreateFromItem('engine4_core_content', $columns, $content)) . PHP_EOL;


    $data = $db->query("SELECT * FROM engine4_core_content WHERE parent_content_id = {$content['content_id']}")->fetchAll();


    if (empty($data)){
      return $sql . PHP_EOL;
    }

    $key_parent = "\$parent_content_id";
    if ($level){
      for ($i=0; $i<$level; $i++){
        $key_parent .= "_$i";
      }
    }
    $parent_content_str = $key_parent;
    $sql .= $key_parent . " = \$db->lastInsertId();" . PHP_EOL;

    foreach ($data as $sub_content){
      $sub_content['page_id'] = $page_str;
      $sub_content['parent_content_id'] = $parent_content_str;
      $sql .= recursiveContent($sub_content, $page_str, $parent_content_str, $level+1) . PHP_EOL;
    }

    return $sql;

  }


  $sql_data .= '/* Pages */' . PHP_EOL . PHP_EOL;

  if (!empty($values['pages'])){
    foreach ($db->query('SELECT * FROM engine4_core_pages WHERE page_id IN ('.implode(",", $values['pages']).')')->fetchAll() as $item){

      $sql_data .= escapePhp(sqlCreateFromItem('engine4_core_pages', $columns, $item)) . PHP_EOL . PHP_EOL;

      $sql_data .= '$page_id = $db->lastInsertId();' . PHP_EOL . PHP_EOL;

      $sql_data .= 'if ($page_id) {' . PHP_EOL . PHP_EOL;
      foreach ($db->query("SELECT * FROM engine4_core_content WHERE (parent_content_id = 0 OR ISNULL(parent_content_id)) AND page_id = {$item['page_id']}")->fetchAll() as $content){
        $sql_data .= recursiveContent($content, "\$page_id")  ;
      }

      $sql_data .= '} ' . PHP_EOL . PHP_EOL;

      $sql_data .= '/* -------------------------- */' . PHP_EOL . PHP_EOL;

    }
  }





  echo '<div style="border: 1px solid #222; font-family: Verdana; color: rgb(85, 85, 85); font-size: 8pt; padding: 20px; background-color: #fffee6; overflow: scroll;">';


  echo(nl2br($sql_data));


  echo '</div>';



} else {
  echo $form->render();
}



?>