  <?php
  /**
   * Created by JetBrains PhpStorm.
   * User: Admin
   * Date: 17.05.12
   * Time: 14:05
   * To change this template use File | Settings | File Templates.
   */
  abstract class Touch_View_Helper_UiKit_Abstract
  {

    protected $children = array();
    protected $tag = 'div';
    protected $body = '';
    protected $title = '';
    protected $attribs = array();

    protected abstract function getSelfBody();

    protected function getOpenTag(){
      $opentag = '<'.$this->tag;
      foreach($this->attribs as $name=>$value){
        if(!is_numeric($name) && !is_null($value))
          $opentag .= ' '. $name . '=' . '"' . $value . '"';
      }
      $opentag .= '>';
      return $opentag;
    }

    protected function getCloseTag(){
      return '</' . $this->tag . '>';
    }

    public function add(Touch_View_Helper_UiKit_Abstract $component){
      array_push($this->children, $component);
    }

    public function multiAdd(array $components){
      array_merge($this->children, $components);
    }

    //  public function addBefore(Touch_View_Helper_UiKit_Abstract $component){
    //    array_push($this->children, $component);
    //  }
    //
    //  public function multiAddBefore(array $components){
    //    array_merge($this->children, $components);
    //  }
//
    public function remove($component){
      if($key = array_search($component, $this->children))
      unset($this->children[$key]);
  }

  public function render(){
    return $this->getOpenTag() .
             $this->getSelfBody() .
             $this->renderChildren() .
           $this->getCloseTag();
  }
  protected function renderChildren(){
    $childrenBody = '';
    foreach($this->children as $child){
      if($child instanceof Touch_View_Helper_UiKit_Abstract){
        $childrenBody .= $child->render();
      }
    }
  }
  public function getChildren(){
    return $this->children;
  }

  public function setTitle($title){
    $this->title = $title;
  }

  public function getTitle(){
    return $this->title;
  }
  public function __toString(){
    return $this->render();
  }
}
