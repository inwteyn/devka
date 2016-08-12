<?php

class Hebadge_Installer extends Engine_Package_Installer_Module
{

  protected function _addHebadgeContentPage(){

        $db = $this->getDb();

        $db->query("INSERT  IGNORE  INTO  `engine4_core_pages`  (`name`,  `displayname`,  `url`,  `title`,  `description`,  `keywords`,  `custom`,  `fragment`,  `layout`,  `levels`,  `provides`,  `view_count`)  VALUES  ('hebadge_index_index',  'Badges  Home',  NULL,  'Badges  Home',  'Badges  Home',  NULL,  NULL,  NULL,  NULL,  NULL,  'no-subject',  NULL)");

        $page_id  =  $db->lastInsertId();

        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'top',  'NULL',  '1',  '[\"[]\"]',  NULL)");
        $parent_content_id  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'middle',  '$parent_content_id',  '6',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.browse-menu',  '$parent_content_id_0',  '3',  '[\"[]\"]',  NULL)");





        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'main',  'NULL',  '2',  '[\"[]\"]',  NULL)");
        $parent_content_id  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'middle',  '$parent_content_id',  '6',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.badges-next',  '$parent_content_id_0',  '9',  '{\"title\":\"HEBADGE_WIDGET_TITLE_BADGES_NEXT\",\"paginator_type\":\"all\",\"nomobile\":\"0\",\"itemCountPerPage\":\"4\",\"name\":\"hebadge.badges-next\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.browse-search',  '$parent_content_id_0',  '10',  '[\"[]\"]',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'core.container-tabs',  '$parent_content_id_0',  '11',  '{\"max\":6}',  NULL)");
        $parent_content_id_0_1  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.badges',  '$parent_content_id_0_1',  '12',  '{\"title\":\"HEBADGE_WIDGET_TITLE_BADGES\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.badges-friend',  '$parent_content_id_0_1',  '13',  '{\"title\":\"HEBADGE_WIDGET_TITLE_BADGES_FRIEND\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.badges-recent',  '$parent_content_id_0_1',  '14',  '{\"title\":\"HEBADGE_WIDGET_TITLE_BADGES_RECENT\"}',  NULL)");

        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'right',  '$parent_content_id',  '5',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.last-members',  '$parent_content_id_0',  '16',  '{\"title\":\"HEBADGE_WIDGET_TITLE_BADGES_LAST_MEMBERS\",\"paginator_type\":\"hide\",\"nomobile\":\"0\",\"itemCountPerPage\":\"\",\"name\":\"hebadge.last-members\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'left',  '$parent_content_id',  '4',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.best-members',  '$parent_content_id_0',  '6',  '{\"title\":\"HEBADGE_WIDGET_TITLE_BEST_MEMBERS\",\"paginator_type\":\"hide\",\"nomobile\":\"0\",\"itemCountPerPage\":\"\",\"name\":\"hebadge.best-members\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.info',  '$parent_content_id_0',  '7',  '{\"title\":\"HEBADGE_WIDGET_TITLE_INFO\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_pages`  (`name`,  `displayname`,  `url`,  `title`,  `description`,  `keywords`,  `custom`,  `fragment`,  `layout`,  `levels`,  `provides`,  `view_count`)  VALUES  ('hebadge_index_view',  'Badges  Profile',  NULL,  'Badges  Profile',  'Badges  Profile',  NULL,  NULL,  NULL,  NULL,  NULL,  'no-subject',  NULL)");

        $page_id  =  $db->lastInsertId();

        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'main',  'NULL',  '2',  '[\"[]\"]',  NULL)");
        $parent_content_id  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'middle',  '$parent_content_id',  '6',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'core.container-tabs',  '$parent_content_id_0',  '8',  '{\"max\":6}',  NULL)");
        $parent_content_id_0_1  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.profile-require',  '$parent_content_id_0_1',  '9',  '{\"title\":\"HEBADGE_WIDGET_TITLE_PROFILE_REQUIRE\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.profile-info',  '$parent_content_id_0_1',  '10',  '{\"title\":\"HEBADGE_WIDGET_TITLE_PROFILE_INFO\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.profile-members',  '$parent_content_id_0_1',  '11',  '{\"title\":\"HEBADGE_WIDGET_TITLE_PROFILE_MEMBERS\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'left',  '$parent_content_id',  '4',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.profile-status',  '$parent_content_id_0',  '3',  '[]',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.profile-approved',  '$parent_content_id_0',  '4',  '[]',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.profile-body',  '$parent_content_id_0',  '5',  '{\"title\":\"HEBADGE_WIDGET_TITLE_PROFILE_BODY\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.profile-loader',  '$parent_content_id_0',  '6',  '{\"title\":\"HEBADGE_WIDGET_TITLE_PROFILE_LOADER\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_pages`  (`name`,  `displayname`,  `url`,  `title`,  `description`,  `keywords`,  `custom`,  `fragment`,  `layout`,  `levels`,  `provides`,  `view_count`)  VALUES  ('hebadge_index_manage',  'Badges  Manage',  NULL,  'Badges  Manage',  'Badges  Manage',  NULL,  NULL,  NULL,  NULL,  NULL,  'no-subject',  NULL)");

        $page_id  =  $db->lastInsertId();

        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'top',  'NULL',  '1',  '[\"[]\"]',  NULL)");
        $parent_content_id  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'middle',  '$parent_content_id',  '6',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.browse-menu',  '$parent_content_id_0',  '3',  '[]',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'main',  'NULL',  '2',  '[\"[]\"]',  NULL)");
        $parent_content_id  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'middle',  '$parent_content_id',  '6',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.badges-manage',  '$parent_content_id_0',  '8',  '{\"title\":\"HEBADGE_WIDGET_TITLE_BADGES_MANAGE\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.badges-next',  '$parent_content_id_0',  '9',  '{\"title\":\"HEBADGE_WIDGET_TITLE_BADGES_NEXT\"}',  NULL)");

        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'left',  '$parent_content_id',  '4',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.info',  '$parent_content_id_0',  '6',  '{\"title\":\"HEBADGE_WIDGET_TITLE_INFO\"}',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_pages`  (`name`,  `displayname`,  `url`,  `title`,  `description`,  `keywords`,  `custom`,  `fragment`,  `layout`,  `levels`,  `provides`,  `view_count`)  VALUES  ('hebadge_credit_index',  'Credit  Ranks',  NULL,  'Credit  Ranks',  'Credit  Ranks',  NULL,  NULL,  NULL,  NULL,  NULL,  'no-subject',  NULL)");

        $page_id  =  $db->lastInsertId();

        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'top',  'NULL',  '1',  '[\"[]\"]',  NULL)");
        $parent_content_id  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'middle',  '$parent_content_id',  '6',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.browse-menu',  '$parent_content_id_0',  '3',  '[\"[]\"]',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'main',  'NULL',  '2',  '[\"[]\"]',  NULL)");
        $parent_content_id  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'middle',  '$parent_content_id',  '6',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.credit-badges',  '$parent_content_id_0',  '8',  '[\"[]\"]',  NULL)");


        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'container',  'left',  '$parent_content_id',  '4',  '[\"[]\"]',  NULL)");
        $parent_content_id_0  =  $db->lastInsertId();
        $db->query("INSERT  IGNORE  INTO  `engine4_core_content`  (`page_id`,  `type`,  `name`,  `parent_content_id`,  `order`,  `params`,  `attribs`)  VALUES  ('$page_id',  'widget',  'hebadge.credit-loader',  '$parent_content_id_0',  '6',  '{\"title\":\"HEBADGE_WIDGET_TITLE_CREDIT_LOADER\"}',  NULL)");


        /*  Member  Home  Widget    */
        $he_content  =  $db->query("
    SELECT
    NULL  AS  content_id,  p.page_id  AS  page_id,  'widget'  AS  type,  'hebadge.profile-badgeicons'  AS  name,  c.parent_content_id  AS  parent_content_id,  c.`order`+1  AS  `order`,  '[]'  AS  params,  NULL  AS  attribs
    FROM  engine4_core_pages  AS  p
    JOIN  engine4_core_content  AS  c_main  ON  c_main.page_id  =  p.page_id  AND  c_main.name  =  'main'
    JOIN  engine4_core_content  AS  c_block  ON  c_block.page_id  =  p.page_id  AND  c_block.parent_content_id  =  c_main.content_id  AND  c_block.name  =  'left'
    JOIN  engine4_core_content  AS  c  ON  c.page_id  =  p.page_id  AND  c.parent_content_id  =  c_block.content_id  AND  c.name  =  'user.home-photo'
    WHERE  p.name  =  'user_index_home'
    ")->fetch();

            if  ($he_content){
                $he_table  =  new  Zend_Db_Table(array('name'  =>  'engine4_core_content'));
                $he_table->insert($he_content);
                $he_content_id  =  $db->lastInsertId();
                $db->query("UPDATE  engine4_core_content  SET  `order`=`order`+1  WHERE  parent_content_id  =  {$he_content['parent_content_id']}  AND  `order`  >=  {$he_content['order']}  AND  content_id  !=  {$he_content_id}");
            }


            /*  Member  Profile  Widget  */
            $he_content  =  $db->query("
    SELECT
    NULL  AS  content_id,  p.page_id  AS  page_id,  'widget'  AS  type,  'hebadge.profile-badgeicons'  AS  name,  c.parent_content_id  AS  parent_content_id,  c.`order`+1  AS  `order`,  '[]'  AS  params,  NULL  AS  attribs
    FROM  engine4_core_pages  AS  p
    JOIN  engine4_core_content  AS  c_main  ON  c_main.page_id  =  p.page_id  AND  c_main.name  =  'main'
    JOIN  engine4_core_content  AS  c_block  ON  c_block.page_id  =  p.page_id  AND  c_block.parent_content_id  =  c_main.content_id  AND  c_block.name  =  'left'
    JOIN  engine4_core_content  AS  c  ON  c.page_id  =  p.page_id  AND  c.parent_content_id  =  c_block.content_id  AND  (c.name  =  'user.profile-photo'  OR  c.name  =  'hegift.profile-photo')
    WHERE  p.name  =  'user_profile_index'
    ")->fetch();

            if  ($he_content){
                $he_table  =  new  Zend_Db_Table(array('name'  =>  'engine4_core_content'));
                $he_table->insert($he_content);
                $he_content_id  =  $db->lastInsertId();
                $db->query("UPDATE  engine4_core_content  SET  `order`=`order`+1  WHERE  parent_content_id  =  {$he_content['parent_content_id']}  AND  `order`  >=  {$he_content['order']}  AND  content_id  !=  {$he_content_id}");
            }


            /*  Member  Profile  Widget  */
            $he_content  =  $db->query("
    SELECT
    NULL  AS  content_id,  p.page_id  AS  page_id,  'widget'  AS  type,  'hebadge.profile-badges'  AS  name,  c_tabs.content_id  AS  parent_content_id,  999  AS  `order`,  '{\"title\":\"HEBADGE_WIDGET_TITLE_PROFILE_BADGES\",\"titleCount\":true}'  AS  params,  NULL  AS  attribs
    FROM  engine4_core_pages  AS  p
    JOIN  engine4_core_content  AS  c_main  ON  c_main.page_id  =  p.page_id  AND  c_main.name  =  'main'
    JOIN  engine4_core_content  AS  c_block  ON  c_block.page_id  =  p.page_id  AND  c_block.parent_content_id  =  c_main.content_id  AND  c_block.name  =  'middle'
    JOIN  engine4_core_content  AS  c_tabs  ON  c_tabs.page_id  =  p.page_id  AND  c_tabs.parent_content_id  =  c_block.content_id  AND  c_tabs.name  =  'core.container-tabs'
    WHERE  p.name  =  'user_profile_index'
    ")->fetch();

            if  ($he_content){
                $he_table  =  new  Zend_Db_Table(array('name'  =>  'engine4_core_content'));
                $he_table->insert($he_content);
                $he_content_id  =  $db->lastInsertId();
                $db->query("UPDATE  engine4_core_content  SET  `order`=`order`+1  WHERE  parent_content_id  =  {$he_content['parent_content_id']}  AND  `order`  >=  {$he_content['order']}  AND  content_id  !=  {$he_content_id}");
            }


            $he_module  =  $db->query("SELECT  *  FROM  engine4_core_modules  WHERE  name  =  'page'  AND  enabled  =  1")->fetch();


            if  ($he_module){

                $db->query("INSERT  IGNORE  INTO  `engine4_page_modules`  (`name`,  `widget`,  `order`,  `params`,  `informed`)  VALUES
    ('hebadge',  'hebadge.page-badgeicons',  6,  '{\"title\":\"\",  \"titleCount\":true}',  1);");

                /*  Browse  Pages  Widget    */
                $he_content  =  $db->query("
            SELECT
    NULL  AS  content_id,  c.page_id  AS  page_id,  'widget'  AS  type,  'hebadge.pages-badges'  AS  name,  c.parent_content_id  AS  parent_content_id,  c.`order`+1  AS  `order`,  '[]'  AS  params,  ''  AS  attribs
    FROM  engine4_core_pages  AS  p
    JOIN  engine4_core_content  AS  c_main  ON  c_main.page_id  =  p.page_id  AND  c_main.name  =  'main'
    JOIN  engine4_core_content  AS  c_block  ON  c_block.page_id  =  p.page_id  AND  c_block.parent_content_id  =  c_main.content_id  AND  c_block.name  =  'middle'
    JOIN  engine4_core_content  AS  c  ON  c.page_id  =  p.page_id  AND  c.parent_content_id  =  c_block.content_id  AND  c.name  =  'page.page-abc'
    WHERE  p.name  =  'page_index_index'
    ")->fetch();

                if  ($he_content){
                    $he_table  =  new  Zend_Db_Table(array('name'  =>  'engine4_core_content'));
                    $he_table->insert($he_content);
                    $he_content_id  =  $db->lastInsertId();
                    $db->query("UPDATE  engine4_core_content  SET  `order`=`order`+1  WHERE  parent_content_id  =  {$he_content['parent_content_id']}  AND  `order`  >=  {$he_content['order']}  AND  content_id  !=  {$he_content_id}");
                }

                /*  Default  Badges  Icons    */
                $he_content  =  $db->query("
            SELECT
    NULL  AS  content_id,  c.page_id  AS  page_id,  'hebadge.page-badgeicons'  AS  name,  'widget'  AS  type,  c_block.content_id  AS  parent_content_id,  c.`order`+1  AS  `order`,  '[]'  AS  params,  ''  AS  attribs
    FROM  engine4_page_pages  AS  p
    JOIN  engine4_page_content  AS  c_main  ON  c_main.page_id  =  p.page_id  AND  c_main.name  =  'main'
    JOIN  engine4_page_content  AS  c_block  ON  c_block.page_id  =  p.page_id  AND  c_block.parent_content_id  =  c_main.content_id  AND  c_block.name  =  'left'
    JOIN  engine4_page_content  AS  c  ON  c.page_id  =  p.page_id  AND  c.parent_content_id  =  c_block.content_id  AND  c.name  =  'page.profile-photo'
    WHERE  p.name  =  'default'
    ")->fetch();

                if  ($he_content){
                    $he_table  =  new  Zend_Db_Table(array('name'  =>  'engine4_page_content'));
                    $he_table->insert($he_content);
                    $he_content_id  =  $db->lastInsertId();
                    $db->query("UPDATE  engine4_page_content  SET  `order`=`order`+1  WHERE  parent_content_id  =  {$he_content['parent_content_id']}  AND  `order`  >=  {$he_content['order']}  AND  content_id  !=  {$he_content_id}");
                }


            }


            $he_module  =  $db->query("SELECT  *  FROM  engine4_core_modules  WHERE  name  =  'credit'  AND  enabled  =  1")->fetch();

            if  ($he_module){

                /*  Member  Home  Widget    */
                $he_content  =  $db->query("
            SELECT
    NULL  AS  content_id,  p.page_id  AS  page_id,  'widget'  AS  type,  'hebadge.credit-loader'  AS  name,  c_block.content_id  AS  parent_content_id,  1  AS  `order`,  '{\"title\":\"HEBADGE_WIDGET_TITLE_CREDIT_LOADER\"}'  AS  params,  NULL  AS  attribs
    FROM  engine4_core_pages  AS  p
    JOIN  engine4_core_content  AS  c_main  ON  c_main.page_id  =  p.page_id  AND  c_main.name  =  'main'
    JOIN  engine4_core_content  AS  c_block  ON  c_block.page_id  =  p.page_id  AND  c_block.parent_content_id  =  c_main.content_id  AND  c_block.name  =  'right'
    WHERE  p.name  =  'user_index_home'
    ")->fetch();

                if  ($he_content){
                    $he_table  =  new  Zend_Db_Table(array('name'  =>  'engine4_core_content'));
                    $he_table->insert($he_content);
                    $he_content_id  =  $db->lastInsertId();
                    $db->query("UPDATE  engine4_core_content  SET  `order`=`order`+1  WHERE  parent_content_id  =  {$he_content['parent_content_id']}  AND  `order`  >=  {$he_content['order']}  AND  content_id  !=  {$he_content_id}");
                }

                $db->query("INSERT  IGNORE  INTO  `engine4_core_menuitems`  (`name`,  `module`,  `label`,  `plugin`,  `params`,  `menu`,  `submenu`,  `enabled`,  `custom`,  `order`)  VALUES
    ('hebadge_main_credit',  'hebadge',  'HEBADGE_MAIN_CREDIT',  'Hebadge_Plugin_Menus',  '{\"route\":\"hebadge_general\",  \"module\":  \"hebadge\",  \"controller\":  \"credit\",  \"action\":  \"index\"}',  'credit_main',  NULL,  '1',  '0',  '2')");


        }

      $he_module = $db->query("SELECT * FROM engine4_core_modules WHERE name = 'credit' AND enabled = 1")->fetch();

        if ($he_module){

            /* Member Home Widget  */
            $he_content = $db->query("
                SELECT
                NULL AS content_id, p.page_id AS page_id, 'widget' AS type, 'hebadge.credit-loader' AS name, c_block.content_id AS parent_content_id, 1 AS `order`, '{\"title\":\"HEBADGE_WIDGET_TITLE_CREDIT_LOADER\"}' AS params, NULL AS attribs
                FROM engine4_core_pages AS p
                JOIN engine4_core_content AS c_main ON c_main.page_id = p.page_id AND c_main.name = 'main'
                JOIN engine4_core_content AS c_block ON c_block.page_id = p.page_id AND c_block.parent_content_id = c_main.content_id AND c_block.name = 'right'
                WHERE p.name = 'user_index_home'
            ")->fetch();

            if ($he_content){
                $he_table = new Zend_Db_Table(array('name' => 'engine4_core_content'));
                $he_table->insert($he_content);
              $he_content_id = $db->lastInsertId();
              $db->query("UPDATE engine4_core_content SET `order`=`order`+1 WHERE parent_content_id = {$he_content['parent_content_id']} AND `order` >= {$he_content['order']} AND content_id != {$he_content_id}");
            }

            $db->query("INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
            ('hebadge_main_credit', 'hebadge', 'HEBADGE_MAIN_CREDIT', 'Hebadge_Plugin_Menus', '{\"route\":\"hebadge_general\", \"module\": \"hebadge\", \"controller\": \"credit\", \"action\": \"index\"}', 'credit_main', NULL, '1', '0', '2')");

        }


    }

  public function onInstall()
  {
        $operation = $this->_databaseOperationType;
        if ($operation == 'install') {
          $this->_addHebadgeContentPage();
        }
        parent::onInstall();
  }

  public function onPreInstall()
  {
    parent::onPreInstall();

    $db = $this->getDb();
    $translate = Zend_Registry::get('Zend_Translate');

    $select = $db->select()
        ->from('engine4_core_modules')
        ->where('name = ?', 'hecore')
        ->where('enabled = ?', 1);

    $hecore = $db->fetchRow($select);

    if (!$hecore) {
      $error_message = $translate->_('Error! This plugin requires Hire-Experts Core module. It is free module and can be downloaded from Hire-Experts.com');
      return $this->_error($error_message);
    }

    if (version_compare($hecore['version'], '4.2.0p1') < 0) {
      $error_message = $translate->_('This plugin requires Hire-Experts Core Module. We found that you has old version of Core module, please download latest version of Hire-Experts Core Module and install. Note: Core module is free.');
      return $this->_error($error_message);
    }

    $operation = $this->_databaseOperationType;
    $module_name = "badges";

    $select = $db->select()
        ->from('engine4_hecore_modules')
        ->where('name = ?', $module_name);

    $module = $db->fetchRow($select);

    if ($module && isset($module['installed']) && $module['installed']
        && isset($module['version']) && $module['version'] == $this->_targetVersion
        && isset($module['modified_stamp']) && ($module['modified_stamp'] + 1000) > time()
    ) {
      return;
    }

    if ($operation == 'install') {

      if ($module && $module['installed']) {
        return;
      }

      $url_params = array(
        'module' => 'hecore',
        'controller' => 'module',
        'action' => 'license',
        'name' => $module_name,
        'version' => $this->_targetVersion,
        'format' => 'smoothbox'
      );

      $route = Zend_Controller_Front::getInstance()->getRouter();
      $register_url = $route->assemble($url_params, 'default', true);
      $register_url = str_replace('/install', '', $register_url);

      $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to install this module - <a class="smoothbox" href="%s">Click Here</a>');
      $error_message = sprintf($error_message, $register_url);

      return $this->_error($error_message);
    }
    else { //$operation = upgrade|refresh

      $url_params = array(
        'module' => 'hecore',
        'controller' => 'module',
        'action' => 'upgrade',
        'name' => $module_name,
        'version' => $this->_currentVersion,
        'target_version' => $this->_targetVersion,
        'operation' => $operation,
        'format' => 'smoothbox'
      );

      $route = Zend_Controller_Front::getInstance()->getRouter();
      $register_url = $route->assemble($url_params, 'default', true);
      $register_url = str_replace('/install', '', $register_url);

      $error_message = $translate->_('It is paid plugin from Hire-Experts LLC. You need to type License Key to install this module - <a class="smoothbox" href="%s">Click Here</a>');
      $error_message = sprintf($error_message, $register_url);

      return $this->_error($error_message);
    }
  }

}
