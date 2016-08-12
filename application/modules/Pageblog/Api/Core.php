<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Pageblog
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Pageblog_Api_Core extends Page_Api_Core
{
    public function getTable()
    {
        return Engine_Api::_()->getDbTable('pageblogs', 'pageblog');
    }

    public function getInitJs($content_info, $subject = null)
    {
        if (empty($content_info))
            return false;

        $content = $content_info['content'];
        $content_id = $content_info['content_id'];

        $res = "page_blog.init_blog();";
        if ($subject->isTimeline()) {
            $tbl = Engine_Api::_()->getDbTable('content', 'page');
            $id = $tbl->select()->from($tbl->info('name'), array('content_id'))
                ->where('page_id = ?', $subject->getIdentity())
                ->where("name = 'pageblog.profile-blog'")
                ->where('is_timeline = 1')
                ->query()
                ->fetch();
            $res = "tl_manager.fireTab('{$id['content_id']}');";
        }
        if ($content == 'blog') {
            $blog = Engine_Api::_()->getItem('pageblog', $content_id);
            if (!$blog) {
                return false;
            }
            return "page_blog.blog_id = {$content_id}; page_blog.view({$content_id}); " . $res;
        } elseif ($content == 'pageblogs') { /// for SEO by Kirill
            return $res; /// for SEO by Kirill
        } elseif ($content == 'blog_page') {
            return $res;
        }
        return false;
    }

    public function getBlogs($pageObject)
    {
        $pageObject = $this->getPage($pageObject);
        $table = $this->getTable();
        $params = array('page_id' => $pageObject->getIdentity());

        return $table->getBlogs($params);
    }

    public function postBlog(array $values)
    {
        return $this->getTable()->postBlog($values);
    }

    public function getComments($page = null)
    {
        $subject = Engine_Api::_()->core()->getSubject();

        if (null !== $page) {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id ASC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber($page);
            $comments->setItemCountPerPage(10);
        } else {
            $commentSelect = $subject->comments()->getCommentSelect();
            $commentSelect->order('comment_id DESC');
            $comments = Zend_Paginator::factory($commentSelect);
            $comments->setCurrentPageNumber(1);
            $comments->setItemCountPerPage(4);
        }

        return $comments;
    }

    public function getBlogsSelect($params = array())
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!empty($params['show']) && $params['show'] == 2) {
            // Get an array of friend ids
            $table = Engine_Api::_()->getItemTable('user');
            $select = $viewer->membership()->getMembersSelect('user_id');
            $friends = $table->fetchAll($select);
            // Get stuff
            $ids = array();
            foreach ($friends as $friend) {
                $ids[] = $friend->user_id;
            }
            $str = "'" . join("', '", $ids) . "'";
        }

        $module = Engine_Api::_()->getDbTable('modules', 'core')->getModule('blog');
        $ynblog = Engine_Api::_()->getDbTable('modules', 'core')->getModule('ynblog');

        //Get tables
        $pageblogTbl = Engine_Api::_()->getItemTable('pageblog');
        $authallowTbl = Engine_Api::_()->getDbTable('allow', 'authorization');
        $listitemTbl = Engine_Api::_()->getItemTable('page_list_item');

        // Check if blog plugin installed and enabled
        if (($module && $module->enabled) || ($ynblog && $ynblog->enabled)) {
            $blogTbl = Engine_Api::_()->getItemTable('blog');
            //Blog select
            $blogselect = $blogTbl->select()
                ->from($blogTbl->info('name'), array('blog_id', 'creation_date', 'view_count', new Zend_Db_Expr("'blog' as type")));
            //Searching

            if (!empty($params['search'])) {
                $blogselect->where('title LIKE ? OR body LIKE ?', '%' . $params['search'] . '%');
            }

            if (!empty($params['show']) && $params['show'] == 2) {
                $blogselect->where('owner_id in (?)', new Zend_Db_Expr($str));
            } elseif (!empty($params['show']) && $params['show'] == 3) {
                $blogselect->where('owner_id = ?', $params['owner']->getIdentity());
            }

            if (!empty($params['category']) && $params['category'] != '0') {
                $blogselect->where('category_id = ?', $params['category']);
            }

            $unionselect = $blogselect;
        }
        $pageblogselect = false;

        //Pageblog select
        if (empty($params['category']) || $params['category'] == '0') {
            $pageblogselect = $pageblogTbl->select()
                ->from(array('pb' => $pageblogTbl->info('name')), array('blog_id' => 'pageblog_id', 'creation_date', 'view_count', new Zend_Db_Expr("'page' as type")))
                ->joinLeft(array('a' => $authallowTbl->info('name')), "a.resource_type = 'page' AND a.resource_id = pb.page_id AND a.action = 'view'", array())
                ->joinLeft(array('li' => $listitemTbl->info('name')), "a.role_id = li.list_id", array())
                ->where("a.role = 'everyone' OR a.role = 'registered' OR li.child_id = ?", $viewer->getIdentity())
                ->group('pb.pageblog_id');

            if (!empty($params['search'])) {
                $pageblogselect->where('title LIKE ? OR body LIKE ?', '%' . $params['search'] . '%');
            }

            if (!empty($params['show']) && $params['show'] == 2) {
                $pageblogselect->where("user_id in (?)", new Zend_Db_Expr($str));
            } elseif (!empty($params['show']) && $params['show'] == 3) {
                $pageblogselect->where('user_id = ?', $params['owner']->getIdentity());
            }

            $unionselect = $pageblogselect;
        }

        if (($module && $module->enabled) || ($ynblog && $ynblog->enabled) && $pageblogselect) {
            $unionselect = Engine_Db_Table::getDefaultAdapter()->select()->union(array($blogselect, $pageblogselect));
        }

        //Order
        if (empty($params['orderby'])) {
            $params['orderby'] = 'creation_date';
        }

        if ($unionselect) $unionselect->order($params['orderby'] . ' DESC');
        return $unionselect;
    }

    public function getBlogsPaginator($params = array())
    {

        $paginator = Zend_Paginator::factory($this->getBlogsSelect($params));

        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }

        if (!empty($params['ipp'])) {
            $paginator->setItemCountPerPage($params['ipp']);
        }

        return $paginator;
    }

    public function isAllowedPost($page)
    {
        if (!$page)
            return false;
        $auth = Engine_Api::_()->authorization()->context;
        return $auth->isAllowed($page, Engine_Api::_()->user()->getViewer(), 'blog_posting');
    }

    public function uploadPhoto($photo, $params = array())
    {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        $extension = ltrim(strrchr($fileName, '.'), '.');
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');

        if (!$extension) {
            $extension = 'jpg';
        }

        $params = array_merge(array(
            'name' => $name,
            'parent_type' => 'pageblogphoto',
            'parent_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
            'extension' => $extension,
        ), $params);

        $storage = Engine_Api::_()->storage();

        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(174, 174)
            ->write($path . '/m_' . $name . '.' . $extension)
            ->destroy();

        $image = Engine_Image::factory();
        $image->open($file)
            ->resize(200, 150)
            ->write($path . '/p_' . $name . '.' . $extension)
            ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
            ->write($path . '/ti_' . $name . '.' . $extension)
            ->destroy();

        $iMain = $storage->create($path . '/m_' . $name . '.' . $extension, $params);
        $iProfile = $storage->create($path . '/p_' . $name . '.' . $extension, $params);
        $iIcon = $storage->create($path . '/ti_' . $name . '.' . $extension, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIcon, 'thumb.icon');

        @unlink($path . '/p_' . $name . '.' . $extension);
        @unlink($path . '/m_' . $name . '.' . $extension);
        @unlink($path . '/ti_' . $name . '.' . $extension);
        @unlink($file);

        return $iMain;
    }

    public function deletePhoto($photo_id)
    {
        $storage = Engine_Api::_()->storage();

        $thumb = $storage->get($photo_id, 'thumb.profile');
        if ($thumb) {
            $thumb->delete();
        }

        $thumb = $storage->get($photo_id, 'thumb.icon');
        if ($thumb) {
            $thumb->delete();
        }

        $photo = $storage->get($photo_id);
        if ($photo) {
            $photo->delete();
        }

    }
}
