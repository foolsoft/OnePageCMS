<?php
class posts_category extends fsDBTableExtension
{
    public function __destruct()
    {
        parent::__destruct();
    }
  
    public function GetFullInfo($categoryId) 
    {
        $resultQuery = $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `c`.*, `ci`.`title`, `ci`.`alt`, `ci`.`meta_keywords`, `ci`.`meta_description`, `ci`.`id_language` 
            FROM `{0}posts_category` c JOIN `{0}posts_category_info` ci ON `ci`.`id_category` = `c`.`id`
            WHERE `c`.`id` = "{1}"
        ', array(
            fsConfig::GetInstance('db_prefix'),
            $categoryId,
        )));
        if(count($resultQuery) == 0) {
            return null;
        }
        $result = array(
            'id' => $resultQuery[0]['id'],
            'id_parent' => $resultQuery[0]['id_parent'],
            'tpl_short' => $resultQuery[0]['tpl_short'],
            'tpl_full' => $resultQuery[0]['tpl_full'],
            'tpl' => $resultQuery[0]['tpl'],
            'auth' => $resultQuery[0]['auth'],
            'title' => array(),
            'alt' => array(),
            'meta_keywords' => array(),
            'meta_description' => array()
        );
        foreach($resultQuery as $row) {
            $result['title'][$row['id_language']] = $row['title'];
            $result['alt'][$row['id_language']] = $row['alt'];
            $result['meta_keywords'][$row['id_language']] = $row['meta_keywords'];
            $result['meta_description'][$row['id_language']] = $row['meta_description'];
        }
        return $result;
    }
  
    public function IsUniqueAlt($alts, $id = -1) 
    {
        foreach($alts as $languageId => $alt) {
            $categories = $this->ExecuteToArray(fsFunctions::StringFormat('SELECT `id_category` FROM `{0}posts_category_info` 
                WHERE `alt` = "{1}" AND `id_language` = "{2}"
            ', array(
                fsConfig::GetInstance('db_prefix'),
                $alt,
                $languageId
            )));
            foreach($categories as $category) {
                if($category['id_category'] != $id) {
                    return false;
                }
            }
        }
        return true;
    }
    
    public function DeleteInfo($categoryId)
    {
        return $this->Execute('DELETE FROM `'.fsConfig::GetInstance('db_prefix').'posts_category_info` WHERE `id_category` = "'.$categoryId.'"');
    }
    
    public function UpdateInfo($categoryId, $langiageId, $title, $alt, $keywords, $description)
    {
        return $this->ExecuteFormat('INSERT INTO `{0}posts_category_info` 
            (`id_category`, `id_language`, `title`, `alt`, `meta_keywords`, `meta_description`) VALUES
            ("{1}", "{2}", "{3}", "{4}", "{5}", "{6}") ON DUPLICATE KEY UPDATE 
            `title` = "{3}", `alt` = "{4}", `meta_keywords` = "{5}", `meta_description` = "{6}"
        ', array(
            fsConfig::GetInstance('db_prefix'), $categoryId, $langiageId, $title, $alt, $keywords, $description
        ));
    }
  
    public function GetAllCategories($languageId)
    {
        return $this->ExecuteFormat('
            SELECT `c`.`id`, `c`.`id_parent`, `c`.`tpl`, `c`.`tpl_short`, `c`.`tpl_full`, `c`.`auth`,
            `ci`.`title`, `ci`.`alt`, `ci`.`meta_keywords`, `ci`.`meta_description`, `ci`.`id_language` FROM
            `{0}posts_category` c JOIN `{0}posts_category_info` ci ON `c`.`id` = `ci`.`id_category`
            WHERE `ci`.`id_language` = "{1}"
            ORDER BY `ci`.`title`', 
            array(fsConfig::GetInstance('db_prefix'), $languageId), false
        );
    }
  
    public function GetCategories($languageId, $orderBy = array(), $excludeIds = array())
    {
        return $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `c`.`id`, `c`.`id_parent`, `c`.`tpl`, `c`.`tpl_short`, `c`.`tpl_full`, `c`.`auth`,
            `ci`.`title`, `ci`.`alt`, `ci`.`meta_keywords`, `ci`.`meta_description`, `ci`.`id_language` FROM
            `{0}posts_category` c JOIN `{0}posts_category_info` ci ON `c`.`id` = `ci`.`id_category`
            WHERE `ci`.`id_language` = "{1}" {2} {3} 
        ', array(
            fsConfig::GetInstance('db_prefix'), $languageId,
            count($excludeIds) == 0 ? '' : 'AND `c`.`id` NOT IN ('.implode(',', $excludeIds).')', 
            count($orderBy) == 0 ? '' : 'ORDER BY '.implode(',', $orderBy)
        )), 'id');
    }
    
    public function Get($languageId, $category)
    {
        $result = $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `c`.`id`, `c`.`id_parent`, `c`.`tpl`, `c`.`tpl_short`, `c`.`tpl_full`, `c`.`auth`,
            `ci`.`title`, `ci`.`alt`, `ci`.`meta_keywords`, `ci`.`meta_description`, `ci`.`id_language` FROM
            `{0}posts_category` c JOIN `{0}posts_category_info` ci ON `c`.`id` = `ci`.`id_category`
            WHERE `ci`.`id_language` = "{1}" AND (`ci`.`alt` = "{2}" OR CAST(`c`.`id` as CHAR) = "{2}")
        ', array(
            fsConfig::GetInstance('db_prefix'), $languageId, $category 
        )));
        return count($result) != 1 ? null : $result[0];
    }
  
    public function GetByParent($languageId, $parentId)
    {
        return $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `c`.`id`, `c`.`id_parent`, `c`.`tpl`, `c`.`tpl_short`, `c`.`tpl_full`, `c`.`auth`,
            `ci`.`title`, `ci`.`alt`, `ci`.`meta_keywords`, `ci`.`meta_description`, `ci`.`id_language` FROM
            `{0}posts_category` c JOIN `{0}posts_category_info` ci ON `c`.`id` = `ci`.`id_category`
            WHERE `ci`.`id_language` = "{1}" AND `c`.`id_parent` = "{2}"
            ORDER BY `ci`.`title` 
        ', array(
            fsConfig::GetInstance('db_prefix'), $languageId, $parentId 
        )));
    }
  
    public function Add($name, $alt = '')
    {
        if (empty($alt)) {
          $alt = fsFunctions::Chpu($alt);
        }
        $this->name = $name;
        $this->alt = $alt;
        $this->Insert()->Execute();
        return $this->insertedId;
    }
}