<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Wall_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();
    $view = Zend_Registry::get('Zend_View');
    $headScript = new Zend_View_Helper_HeadScript();
    $smiles = $view->wallSmiles()->getJson();
        $content = <<<CONTENT

 window.Wall_smiles = {$smiles};

  function getSmile(links)
  {
    if($('wall_comment_smile')){
      var elem = document.getElementById("wall_comment_smile");
      elem.parentNode.removeChild(elem);
    }
    $$('body')[0].addEvent('click', function(e){
      if(!e.target.getParent('#wall_comment_smile') && !e.target.getParent('#smile_composer_comment-element')
      && !e.target.hasClass('wall-compose-smile-o-activator')  && !e.target.hasClass('wall-compose-smile-o-activator') && !e.target.getParent('#smile_composer_comment-element') && e.target.get('id') != 'addEmoticonContanerBackground' && !e.target.getParent('#addEmoticonContaner') && !e.target.getParent('.header-smiles-contaner') && !e.target.getParent('.tabs_smiles')){
        hideSmile();
      }
    });
    var  smiles = {$smiles};
    var link = links;
    var container = new Element('div', {'class': 'wall-smile-container','id':'wall_comment_smile', 'html': '<div class="wall_data_comment"></div>'});
    container = injectAbsoluteCommentSmile(link, container, true);

    var arrow = new Element('div', {'class': 'wall_arrow_container', 'html': '<div class="wall_arrow"></div>'});
    arrow.inject(container, 'top');

    var ul = new Element('ul');

    for (var i=0;i<smiles.length; i++){
      var item = smiles[i];
      var a = new Element('a', {'title': item.title, 'href': 'javascript:void(0)', 'html': item.html, 'rev': item.index_tag});
      var li = new Element('li', {});
      a.inject(li);
      li.inject(ul);

      a.addEvent('click', function (){
        var body_in = link.getParent('form').getChildren('#body')[0].value;
        link.getParent('form').getChildren('#body')[0].value = body_in+' '+$(this).get('rev')+' ';
        link.getParent('form').getChildren('#submit')[0].setStyle('display', 'block');
        hideSmile();
      });
    }

    ul.inject(container.getElement('.wall_data_comment'));
  };


  function hideSmile(){
    if($('wall_comment_smile')){
      var elem = document.getElementById("wall_comment_smile");
      elem.parentNode.removeChild(elem);

    }
  }
  function injectAbsoluteCommentSmile(element, container) {
    element = $(element);
    container = $(container);

    if (\$type(element) != 'element' || \$type(container) != 'element') {
      return;
    }

    var build = function () {
      var pos = element.getCoordinates();

      container
        .setStyle('position', 'absolute')
        .setStyle('top', pos.top + pos.height)
        .setStyle('right', ($$('body')[0].getCoordinates().width - pos.left - pos.width) - 15);

    };

    container.inject(Wall.externalDiv(), 'bottom');
    build();

    return container;

  };
  function select_file_comment(id){
    if(id && id.toInt()>0){

      var file_button = $('photo_comment_'+id);
      if(file_button){
        file_button.click();
      }
    }
  }
  function deleteImage(id){
    var photo_id = 0;
    var container = $('comment_attach_preview_image_wall'+id);
    if(container) {
      photo_id = container.getChildren('a').get('href')[0].split('/').pop();
    }
    if(!photo_id){
      return;
    }
    if (window.load_image_deletes_comment == 1) {
      return;
    }
    var loading = $('comment_attach_loading_wall'+id);
    loading.setStyle('display','block');
    container.setStyle('display', 'none');
    window.load_image_deletes_comment = 1;
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl +'wall/index/album',
      data: {
        'do': '1',
        'photos_id_del': photo_id
      },
      onComplete: function (response) {
        container.set('html','');
        loading.setStyle('display','none');
        $('select_photo_'+id).setStyle('display','block');
        if($('photo_comment_'+id)) $('photo_comment_'+id).value = '';
        window.load_image_deletes_comment = 0;
      }
    }).send();
  }

CONTENT;

    $view->headScript()->appendScript($content);
    $view->headTranslate(array(
      'Create Album',
      'Album name',
      'Album description',
      'Add photos',
      'Create Album',
      'select file'
    ));
    /*$front =  Zend_Controller_Front::getInstance();
    $plugin =  new Wall_Controller_Helper_Protector();
    $front->registerPlugin($plugin);*/

  }
}