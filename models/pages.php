<?php
class pages extends fsDBTableExtension
{
    public function __destruct()
    {
        parent::__destruct();
    }

    public function IsUniqueAlt($alts, $id = -1) 
    {
        foreach($alts as $languageId => $alt) {
            $pages = $this->ExecuteToArray(fsFunctions::StringFormat('SELECT `id_page` FROM `{0}pages_info` 
                WHERE `alt` = "{1}" AND `id_language` = "{2}"
            ', array(
                fsConfig::GetInstance('db_prefix'),
                $alt,
                $languageId
            )));
            foreach($pages as $page) {
                if($page['id_page'] != $id) {
                    return false;
                }
            }
        }
        return true;
    }
    
    public function DeleteInfo($pageId) 
    {
        return $this->Execute('DELETE FROM `'.fsConfig::GetInstance('db_prefix').'pages_info` WHERE `id_page` = "'.$pageId.'"');
    }
    
    public function GetFullInfo($pageId) 
    {
        $resultQuery = $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `p`.`id`, `p`.`active`, `pi`.`alt`, `p`.`in_menu`, `p`.`auth`, `p`.`tpl`, `pi`.`title`, `pi`.`html`, `pi`.`keywords`, `pi`.`description`, `pi`.`id_language` 
            FROM `{0}pages` p JOIN `{0}pages_info` pi ON `pi`.`id_page` = `p`.`id`
            WHERE `p`.`id` = "{1}"
        ', array(
            fsConfig::GetInstance('db_prefix'),
            $pageId,
        )));
        if(count($resultQuery) == 0) {
            return null;
        }
        $result = array(
            'id' => $resultQuery[0]['id'],
            'in_menu' => $resultQuery[0]['in_menu'],
            'active' => $resultQuery[0]['active'],
            'tpl' => $resultQuery[0]['tpl'],
            'auth' => $resultQuery[0]['auth'],
            'title' => array(),
            'html' => array(),
            'alt' => array(),
            'keywords' => array(),
            'description' => array()
        );
        foreach($resultQuery as $row) {
            $result['title'][$row['id_language']] = $row['title'];
            $result['html'][$row['id_language']] = $row['html'];
            $result['alt'][$row['id_language']] = $row['alt'];
            $result['keywords'][$row['id_language']] = $row['keywords'];
            $result['description'][$row['id_language']] = $row['description'];
        }
        return $result;
    }
    
    public function UpdateInfo($pageId, $languageId, $title, $alt, $html, $keywords, $description) 
    {
        $this->Execute(fsFunctions::StringFormat('INSERT INTO `{0}pages_info` 
            (`id_page`, `id_language`, `title`, `alt`, `html`, `keywords`, `description`) VALUES
            ("{1}", "{2}", "{3}", "{4}", "{5}", "{6}", "{7}") ON DUPLICATE KEY UPDATE 
            `title` = "{3}", `alt` = "{4}", `html` = "{5}", `keywords` = "{6}", `description` = "{7}"
        ', array(
            fsConfig::GetInstance('db_prefix'),
            $pageId,
            $languageId,
            $title,
            $alt,
            $html,
            $keywords,
            $description
        )));
    }
    
    public function GetPages($languageId, $page = 1, $pageCount = 20, $title = '')
    {
        if(!is_numeric($page) || $page < 1) {
            $page = 1;
        }
        if(!is_numeric($pageCount) || $pageCount < 1) {
            $pageCount = 20;
        }
        return $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `p`.`id`, `p`.`active`, `pi`.`alt`, `p`.`in_menu`, `p`.`auth`, `p`.`tpl`, `pi`.`title`, `pi`.`html`, `pi`.`keywords`, `pi`.`description`, `pi`.`id_language` 
            FROM `{0}pages` p JOIN `{0}pages_info` pi ON `pi`.`id_page` = `p`.`id`
            WHERE `pi`.`id_language` = "{1}" {4}
            LIMIT {2}, {3}
        ', array(
            fsConfig::GetInstance('db_prefix'),
            $languageId,
            ($page - 1) * $pageCount,
            $pageCount,
            $title == '' ? '' : 'AND `pi`.`title` LIKE "%'.$title.'%"'
        )));
    }
    
    public function GetMenuPages($languageId)
    {
        return $this->ExecuteToArray(
            fsFunctions::StringFormat('
                SELECT `p`.`id`, `pi`.`alt`, `p`.`active`, `p`.`auth`, `p`.`tpl`, `pi`.`title`, `pi`.`html`, `pi`.`keywords`, `pi`.`description`, `pi`.`id_language`
                FROM `{0}pages` p JOIN `{0}pages_info` pi ON `p`.`id` = `pi`.`id_page` 
                WHERE `pi`.`id_language` = "{1}" AND `p`.`in_menu` = "1"
                ORDER BY `pi`.`title`
            ', array(
                fsConfig::GetInstance('db_prefix'),
                $languageId
            ))
        );
    }
  
    public function Load($languageId, $pageId = -1, $pageAlt = '')
    {
        if(!is_numeric($pageId) && $pageAlt === '') {
            return array();
        }
        $result = $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `p`.`id`, `pi`.`alt`, `p`.`in_menu`, `p`.`auth`, `p`.`tpl`, `pi`.`title`, `pi`.`html`, `pi`.`keywords`, `pi`.`description`, `pi`.`id_language` 
            FROM `{0}pages` p JOIN `{0}pages_info` pi ON `pi`.`id_page` = `p`.`id`
            WHERE {1} AND `p`.`active` = "1" AND `pi`.`id_language` = "{2}"
            LIMIT 1
        ', array(
            fsConfig::GetInstance('db_prefix'),
            $pageAlt !== '' ? '`pi`.`alt` = "'.$pageAlt.'"' : '`p`.`id` = "'.$pageId.'"',
            $languageId
        )));
        return count($result) == 0 ? null : $result[0];
    }
}