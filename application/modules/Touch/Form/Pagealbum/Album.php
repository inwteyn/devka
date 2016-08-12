<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Album.php 2010-09-06 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pagealbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Touch_Form_Pagealbum_Album extends Touch_Form_Standard
{
    private $page_id;

    public function __construct($page_id)
    {
        $this->page_id = $page_id;
        parent::__construct();
    }

    private $_subject;

    public function init()
    {
        // Init form
        $this
                ->setTitle('Add New Photos')
                ->setDescription('Choose photos on your computer to add to this album.')
                ->setAttrib('id', 'form-upload-page-album')
                ->setAttrib('class', 'global_form hidden')
                ->setAttrib('name', 'pagealbums_create')
                ->setAttrib('enctype', 'multipart/form-data');

        $this->addElement('Select', 'album', array(
                                                  'label' => 'Choose Album',
                                                  'multiOptions' => array('0' => 'Create A New Album'),
                                                  'onchange' => "updateTextFields()",
                                             ));

        $my_albums = Engine_Api::_()->pagealbum()->getUserAlbums(Engine_Api::_()->user()->getViewer());
        $album_options = Array();


        foreach ($my_albums as $my_album)
        {
            $album_options[$my_album->pagealbum_id] = htmlspecialchars_decode($my_album->getTitle());
        }

        
        $this->album->addMultiOptions($album_options);


        // Init name
        $this->addElement('Text', 'title', array(
                                                'label' => 'Album Title',
                                                'maxlength' => '40',
                                                'filters' => array(
                                                    'StripTags',
                                                    new Engine_Filter_Censor(),
                                                    new Engine_Filter_StringLength(array('max' => '63')),
                                                )
                                           ));

        $this->addElement('Text', 'tags', array(
                                               'label' => 'Tags (Keywords)',
                                               'autocomplete' => 'off',
                                               'description' => 'Separate tags with commas.',
                                               'filters' => array(
                                                   new Engine_Filter_Censor(),
                                               ),
                                          ));

        $this->tags->getDecorator("Description")->setOption("placement", "append");

        // Init descriptions
        $this->addElement('Textarea', 'description', array(
                                                          'label' => 'Album Description',
                                                          'filters' => array(
                                                              'StripTags',
                                                              new Engine_Filter_Censor(),
                                                              new Engine_Filter_EnableLinks(),
                                                          ),
                                                     ));

        if (!isset($_FILES['file'])) {
            // ignore Zend_Validate_File_Upload::INI_SIZE
            $_FILES['file'] = array(
                'name' => '',
                'type' => '',
                'tmp_name' => '',
                'error' => 4,
                'size' => 0
            );
        }

        // Init file
        $this->addElement('File', 'file', array(
                                               'label' => 'Photo',
                                          ));
        $this->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->addElement('hidden', 'photos');

        // Init submit
        $this->addElement('Button', 'submit', array(
                                                   'label' => 'Save Photos',
                                                   'type' => 'submit',
                                              ));

    }

    public function clearAlbum()
    {
        $this->getElement('album')->setValue(0);
    }

    protected function _getParam($paramName, $default = null)
    {
        $value = $this->getRequest()->getParam($paramName);
        if ((null === $value) && (null !== $default)) {
            $value = $default;
        }

        return $value;
    }

    public function saveValues()
    {
        $set_cover = false;

        $values = $this->getValues();

        $user = Engine_Api::_()->user()->getViewer();

        $params = Array();
        $params['user_id'] = $user->getIdentity();
        $params['page_id'] = $this->page_id;

        if (($values['album'] == 0)) {
            $params['title'] = $values['title'];
            if (empty($params['title'])) {
                $params['title'] = "Untitled Album";
            }
            $params['description'] = $values['description'];
            $album = Engine_Api::_()->getDbtable('pagealbums', 'pagealbum')->createRow();
            $album->setFromArray($params);

            $album->save();

            $set_cover = True;
        }
        else
        {
            $album = Engine_Api::_()->getItem('pagealbum', $values['album']);
        }

        // Add action and attachments
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $api->addActivity($user, $album->getPage(), 'pagealbum_photo_new', null,
                                    array(
                                         'count' => count($values['file']),
                                         'link' => $album->getLink(),
                                         'is_mobile' => true
                                    )
        );

        // Do other stuff
        $count = 0;
        foreach ($values['photos'] as $photo_id)
        {
            $photo = Engine_Api::_()->getItem("pagealbumphoto", $photo_id);

            if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) continue;
            if ($set_cover) {
                $album->photo_id = $photo_id;
                $album->save();
                $set_cover = false;
            }

            $photo->collection_id = $album->getIdentity();
            $photo->save();

            if ($action instanceof Activity_Model_Action && $count < 8) {
                $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
            }
            $count++;
        }

        $tags = preg_split('/[,]+/', $values['tags']);
        if ($tags) {
            $album->tags()->setTagMaps($user, $tags);
        }

        $search_api = Engine_Api::_()->getDbTable('search', 'page');
        $search_api->saveData($album);

        return $album;
    }

}