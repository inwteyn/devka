<?php

set_time_limit(999);

  class he_file {
    static function getFiles($dir, $file_types = null)
    {
      $files = array();
      $d = dir($dir);
      while (false !== ($entry = $d->read())) {
        if ($entry == '.' || $entry == '..'){
          continue ;
        }
        $file_path = $d->path.DIRECTORY_SEPARATOR.$entry;
        if (is_dir($file_path)){
          $files = array_merge($files, he_file::getFiles($file_path, $file_types));
        } else {
          $ext = substr($entry, -4, 4);
          $js_ext = substr($entry, -3, 3); // :)
          if ($file_types && !in_array($ext, $file_types) && !in_array($js_ext, $file_types)){
            continue ;
          }
          $files[] = $file_path;
        }
      }
      $d->close();
      return $files;
    }
    static function read($file)
    {
      $fp = fopen ($file, "r");
      if (!filesize($file)){
        return '';
      }
      $buffer = fread($fp, filesize($file));
      fclose ($fp);
      return $buffer;
    }
    static function write($file, $content)
    {
      $fp = fopen ($file, "w");
      fwrite ($fp, $content);
      fclose ($fp);
    }
  }

  function heReplaceContent($content, $file, $name, $module)
  {
    $module = ucfirst($module);
    $new_content = '';

    $lowername = strtolower($name);
    $simple = ucfirst($name);


    $ext = substr($file, -4, 4);
    $js_ext = substr($file, -3, 3);
    $info = pathinfo($file);

    $date = date("d.m.y H:i");

    if ($ext == '.php'){

      $fragment = '';
      $matches = array();

      preg_match_all('/\<\?php[^\*\/]*\/\*\*[^\*\/].*?\@copyright.*?\*\/[^\*\/].*?\/\*\*.*?\@copyright.*?\*\/(.*)/imsS', $content, $matches);
      if (!empty($matches[1]) && !empty($matches[1][0])){
        $fragment = $matches[1][0];
      }

      if (empty($fragment)){
        preg_match_all('/\<\?php[^\*\/]*\/\*\*[^\*\/].*?\@copyright.*?\*\/(.*)/imsS', $content, $matches);
        if (!empty($matches[1]) && !empty($matches[1][0])){
          $fragment = $matches[1][0];
        }
      }
      if (empty($fragment)){
        preg_match_all('/\<\?php(.*)/imsS', $content, $matches);
        if (!empty($matches[1]) && !empty($matches[1][0])){
          $fragment = $matches[1][0];
        }
      }



      $new_content .= <<<CONTENT
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    $module
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    \$Id: {$info['basename']} $date $lowername $
 * @author     $simple
 */

/**
 * @category   Application_Extensions
 * @package    $module
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

CONTENT;

      $new_content .= $fragment;

    } else if ($ext == '.tpl'){

    $fragment = '';
    $matches = array();

      $phpTabAfter = false;

      preg_match_all('/\<\?php[^\*\/]*\/\*\*[^\*\/].*?\@copyright.*?\*\/(.*)/imsS', $content, $matches);
    if (!empty($matches[1]) && !empty($matches[1][0])){
      $fragment = $matches[1][0];
    }
    if (empty($fragment)){
      preg_match_all('/(.*)/imsS', $content, $matches); // '/\<\?php(.*)/imsS'
      if (!empty($matches[1]) && !empty($matches[1][0])){
        $fragment = $matches[1][0];
        $phpTabAfter = true;
      }
    }

    $new_content .= <<<CONTENT
<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    $module
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    \$Id: {$info['basename']} $date $lowername $
 * @author     $simple
 */
CONTENT;

      if ($phpTabAfter){
        $new_content .= PHP_EOL. "?>" . PHP_EOL;
      }

    $new_content .= $fragment;

  } else if ($js_ext == '.js' || $ext == '.css'){

      $fragment = '';
      $matches = array();

      preg_match_all('/.*\/\*.*\$Id: [^\*\/]+?\*\/(.*)/imsS', $content, $matches);

      if (!empty($matches[1]) && !empty($matches[1][0])){
        $fragment = $matches[1][0];
      }
      if (empty($fragment)){
        preg_match_all('/(.*)/imsS', $content, $matches);
        if (!empty($matches[1]) && !empty($matches[1][0])){
          $fragment = $matches[1][0];
        }
      }

      $new_content .= <<<CONTENT
/* \$Id: {$info['basename']} $date $lowername $ */

CONTENT;

      $new_content .= $fragment;




    } else {
      $new_content = $content;
    }




    return $new_content;

  }





echo '

<style type="text/css">
.he_hetools {
overflow: hidden;
}
.he_hetools_left {
float: left;
margin-right: 20px;
width:450px;
}
.he_hetools_right {
float: left;
width:450px;
}
.he_hetools textarea {
  background-color: #EEEEEE;
  border: 1px solid #DDDDDD;
  color: #333333;
  font-size: 8pt;
  height: 500px;
  padding: 5px;
  width: 450px;
}
.he_hetools > ul > li {
  margin:45px 0;
  overflow: hidden;
}
.he_hetools_title {
    font-size: 22pt;
    margin: 10px 0;
}

</style>

';


  $form = new Engine_Form();
  $form->addElement('text', 'name', array(
    'label' => 'Developer Name',
    'value' => 'Michael'
  ));
  $form->addElement('text', 'm', array(
    'label' => 'Module',
    'value' => 'hebadge'
  ));
  $form->addElement('text', 'path', array(
    'label' => 'Path',
    'value' => './application/modules/Hebadge'
  ));
  $form->addElement('hidden', 'task', array('value' => 'prepare'));
  $form->addElement('button', 'submit', array(
    'label' => 'Submit',
    'type' => 'submit'
  ));

  $request = Zend_Controller_Front::getInstance()->getRequest();

  if ($request->getParam('task') == 'save'){

    $values = $request->getParams();

    require_once './application/modules/Hetools/pclzip.lib.php';


    function recurse_zip($src,&$zip,$path_length) {
      $dir = opendir($src);
      while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
          if ( is_dir($src . '/' . $file) ) {
            recurse_zip($src . '/' . $file,$zip,$path_length);
          }
          else {
            $zip->addFile($src . '/' . $file,substr($src . '/' . $file,$path_length));
          }
        }
      }
      closedir($dir);
    }

    function compress($src, $name)
    {
      if (substr($src, -1) === '/'){
        $src = substr($src, 0, -1);
      }
      $arr_src = explode('/', $src);
      $filename = end($src);
      unset($arr_src[count($arr_src) - 1]);
      $path_length = strlen(implode('/', $arr_src) . '/');
      $f = explode('.', $filename);
      $filename = $f[0];
      $filename = (($filename == '') ? 'backup.zip' : $filename . '.zip');

    $zip = new ZipArchive;
    $res = $zip->open($name, ZipArchive::CREATE);
    if ($res !== TRUE){
      echo 'Error: Unable to create zip file';
      exit;
    }
    if (is_file($src)){
      $zip->addFile($src, substr($src, $path_length));
    }
    else {
      if (!is_dir($src)){
        $zip->close();
        @unlink($filename);
        echo 'Error: File not found';
        exit;
      }
      recurse_zip($src, $zip, $path_length);
    }
    $zip->close();
    return $zip;
  }



if (empty($values['m'])){
      die('Backup file is missing');
    }



    $zip = new ZipArchive($values['m'] . '_' . time());
    $path = './application/modules/' . ucfirst($values['m']);

    $name = "hetools_" . strtolower($values['m']) . "_" . time();
    $zip = compress($path, $name);


    echo "<h2>Backup file: " . $name . "<br /></h2>";
    echo "<h2>Total files: " . count($request->getParam('files')) . "</h2>";

    foreach ($request->getParam('files') as $file => $content){
      he_file::write($file, $content);
      echo "<div>Write: ".$file." | Filesize: ".filesize($file)." </div>";
    }

    echo "Success!";

  } else if ($request->isPost() && $request->getParam('task') == 'prepare' && $form->isValid($request->getPost())){

    $values = $form->getValues();
    $files = he_file::getFiles($values['path'], array('.php', '.css', '.tpl', '.js'));


    echo '<form action="" method="post">';

    echo '<div class="he_hetools"><ul>';
    foreach ($files as $file){
      $content = he_file::read($file);
      if (empty($content)){
        continue ;
      }
      echo '<li>

      <div class="he_hetools_title">'.$file.'</div>
      <div class="he_hetools_left"><textarea>';
      echo htmlspecialchars($content);
      echo '</textarea></div><div class="he_hetools_right"><textarea name="files['.$file.']">';
      $result = heReplaceContent($content, $file, $values['name'], $values['m']);
      echo htmlspecialchars($result);
      //echo highlight_string($result, true);
      echo '</textarea></div></li>';

    }
    echo '</ul></div';

    echo '

      <br />
      <button type="submit">Save Changes</button>
      <input type="hidden" name="task" value="save" />
      <input type="hidden" name="m" value="'.$values['m'].'" />
      </form>
    ';

  } else {
    echo $form->render();
  }



