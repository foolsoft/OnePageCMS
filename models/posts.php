<?php
class posts extends fsDBTableExtension
{
    public function __destruct()
    {
        parent::__destruct();
    }

    public function GetPostCountInCategory($idCategory, $calculateChilds = false, &$filter = array())
    {
        $cache = 'posts_GetPostCountInCategory_'.$idCategory.'_'.($calculateChilds ? '1' : '0');
        $cacheData = fsCache::GetText($cache);
        if($cacheData !== null) {
            return $cacheData;
        }
        $count = 0;
        $this->Execute(fsFunctions::StringFormat('
            SELECT `id_post` FROM `{0}post_category`
            WHERE `id_category` = "{1}" {2}', array(
            fsConfig::GetInstance('db_prefix'), $idCategory,
            count($filter) > 0 ? 'AND `id_post` NOT IN ('.implode(',', $filter).')' : ''
        )), false);
        while($this->Next())  {
            ++$count;
            $filter[] = $this->result->mysqlRow['id_post'];
        }
        if($calculateChilds === true) {
            $rows = $this->ExecuteToArray(fsFunctions::StringFormat('
                SELECT `id` FROM `{0}posts_category` WHERE `id_parent` = "{1}"', array(
                fsConfig::GetInstance('db_prefix'), $idCategory
            )), false);
            foreach($rows as $row) {
                $count += $this->GetPostCountInCategory($row['id'], true, $filter);
            }
        }
        fsCache::CreateOrUpdate($cache, $count);
        return $count;
    }
    
    public function GetRandom($languageId, $limit = 1, $categories = array())
    {
        $resultQuery = $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `p`.*, `pi`.`title`, `pi`.`alt`, `pi`.`meta_keywords`, `pi`.`meta_description`,
            `pi`.`id_language`, `pi`.`html_full`, `pi`.`html_short`
            FROM `{0}posts` p JOIN `{0}posts_info` pi ON `pi`.`id_post` = `p`.`id`
                JOIN `{0}post_category` pc ON `pc`.`id_post` = `p`.`id`
            WHERE `p`.`active` = "1"
                AND `pi`.`id_language` = "{3}"
            GROUP BY `p`.`id`
            ORDER BY RAND()
            LIMIT {2}
        ', array(
            fsConfig::GetInstance('db_prefix'),
            count($categories) > 0 ? 'AND `pc`.`id_category` IN ('.implode(',', $categories).')' : '',
            $limit,
            $languageId
        )));
        if(count($resultQuery) == 0) {
            return null;
        }
        $result = array();
        foreach($resultQuery as $row) {
            $result[] = array(
                'id' => $row['id'],
                'id_user' => $row['id_user'],
                'date' => $row['date'],
                'active' => $row['active'],
                'position' => $row['position'],
                'image' => $row['image'],
                'tpl_short' => $row['tpl_short'],
                'tpl' => $row['tpl'],
                'auth' => $row['auth'],
                'title' => $row['title'],
                'alt' => $row['alt'],
                'html_short' => $row['html_short'],
                'html_full' => $row['html_full'],
                'meta_keywords' => $row['meta_keywords'],
                'meta_description' => $row['meta_description']
            );
        }
        return $result;
    }
    
    public function GetCountByCategory($idOrAlt = ALL_TYPES, $activeOnly = true)
    {
        $sql = '';
        if ($idOrAlt == -1) { //No category
          $sql = fsFunctions::StringFormat(
            'SELECT COUNT(`p`.`id`) as `c`
             FROM `{0}posts` p LEFT JOIN `{0}post_category` pc ON `p`.`id` = `pc`.`id_post`
             WHERE `pc`.`id_category` IS NULL'
             .($activeOnly ? ' AND `p`.`active` = "1"' : ''),
             array(fsConfig::GetInstance('db_prefix'))
          ); 
        } else {
          $where = 'WHERE 1';
          if ($idOrAlt != ALL_TYPES) {
            $where .= ' AND ((CAST(`pcs`.`id` as CHAR) = "{1}" OR `pcsi`.`alt` = "{1}") OR (`pcs`.`id` = "{2}"))'; 
          }
          if ($activeOnly) {
            $where .= ' AND `p`.`active` = "1"';
          }
          $sql = fsFunctions::StringFormat(
            'SELECT COUNT(DISTINCT `p`.`id`) as `c` FROM `{0}posts` p JOIN `{0}post_category` pc ON `p`.`id` = `pc`.`id_post`
             JOIN `{0}posts_category` pcs ON `pc`.`id_category` = `pcs`.`id` 
             JOIN `{0}posts_category_info` pcsi ON `pcs`.`id` = `pcsi`.`id_category` '.$where,
             array(fsConfig::GetInstance('db_prefix'), $idOrAlt, ALL_TYPES)
          );
        }
        $this->Execute($sql);
        return $this->_result->mysqlRow['c'];
    } 
	
    public function GetAllPosts($languageId)
    {
        return $this->ExecuteFormat('
            SELECT `p`.*, `pi`.`title`, `pi`.`alt`, `pi`.`html_short`, `pi`.`html_full`, 
                `pi`.`meta_keywords`, `pi`.`meta_description`, `pi`.`id_language`,
                `pcs`.`id` as `category_id`, `pcsi`.`title` as `category_name`,
                `pcsi`.`alt` as `category_alt`, `pcs`.`tpl` as `category_tpl`
             FROM `{0}posts` p 
             JOIN `{0}posts_info` pi ON `p`.`id` = `pi`.`id_post`
             JOIN `{0}post_category` pc ON `p`.`id` = `pc`.`id_post`
             JOIN `{0}posts_category` pcs ON `pc`.`id_category` = `pcs`.`id`
             JOIN `{0}posts_category_info` pcsi ON `pcs`.`id` = `pcsi`.`id_category`
             WHERE `pi`.`id_language` = "{1}" AND `pcsi`.`id_language` = "{1}"
             GROUP BY `p`.`id`
             ORDER BY `p`.`position`, `p`.`date` DESC',
             array(fsConfig::GetInstance('db_prefix'), $languageId),
             false
        );
    }
    
    public function GetByCategory($languageId, $idOrAlt = ALL_TYPES, $start = 1, $count = 20, $activeOnly = true)
    {
        $sql = '';
        if ($idOrAlt == -1) { //No category
          $sql = fsFunctions::StringFormat(
            'SELECT `p`.* FROM `{0}posts` p LEFT JOIN `{0}post_category` pc ON `p`.`id` = `pc`.`id_post`
             WHERE `pc`.`id_category` IS NULL '.
             ($activeOnly ? ' AND `p`.`active` = "1" ' : ''). 
             'ORDER BY `p`.`position`, `p`.`date` DESC LIMIT {1}, {2}', 
             array(fsConfig::GetInstance('db_prefix'), ($start - 1) * $count, $count)
          );
        } else {
          $where = 'WHERE `pi`.`id_language` = "'.$languageId.'" AND `pcsi`.`id_language` = "'.$languageId.'"';
          if ($idOrAlt != ALL_TYPES) {
            $where .= ' AND ((CAST(`pcs`.`id` as CHAR) = "{1}" OR `pcsi`.`alt` = "{2}") OR (`pcs`.`id` = "{4}"))'; 
          }
          if ($activeOnly) {
            $where .= ' AND `p`.`active` = "1"';
          }
          $sql = fsFunctions::StringFormat('
            SELECT `p`.*, `pi`.`title`, `pi`.`alt`, `pi`.`html_short`, `pi`.`html_full`, 
                `pi`.`meta_keywords`, `pi`.`meta_description`, `pi`.`id_language`,
                `pcs`.`id` as `category_id`, `pcsi`.`title` as `category_name`,
                `pcsi`.`alt` as `category_alt`, `pcs`.`tpl` as `category_tpl`
             FROM `{0}posts` p 
             JOIN `{0}posts_info` pi ON `p`.`id` = `pi`.`id_post`
             JOIN `{0}post_category` pc ON `p`.`id` = `pc`.`id_post`
             JOIN `{0}posts_category` pcs ON `pc`.`id_category` = `pcs`.`id`
             JOIN `{0}posts_category_info` pcsi ON `pcs`.`id` = `pcsi`.`id_category`
             '.$where.'
             GROUP BY `p`.`id`
             ORDER BY `p`.`position`, `p`.`date` DESC
             LIMIT {2}, {3}',
             array(fsConfig::GetInstance('db_prefix'), $idOrAlt, ($start - 1) * $count, $count, ALL_TYPES)
          );
        }
        return $this->ExecuteToArray($sql);
    }
  
    public function Get($languageId, $idOtAlt)
    {
        $result = $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `p`.*, `pi`.`title`, `pi`.`alt`, `pi`.`html_short`, `pi`.`html_full`,
            `pi`.`meta_description`, `pi`.`meta_keywords` 
            FROM `{0}posts` p JOIN `{0}posts_info` pi ON `p`.`id` = `pi`.`id_post`
            WHERE `pi`.`id_language` = "{1}" AND (CAST(`p`.`id` as CHAR) = "{2}" OR `pi`.`alt` = "{2}") 
        ', array(fsConfig::GetInstance('db_prefix'), $languageId, $idOtAlt)));
        return count($result) != 1 ? null : $result[0];
    }
    
    public function DeleteInfo($postId)
    {
        return $this->Execute('DELETE FROM `'.fsConfig::GetInstance('db_prefix').'posts_info` WHERE `id_post` = "'.$postId.'"');
    }
    
    public function UpdateInfo($postId, $langiageId, $title, $alt, $short, $full, $keywords, $description)
    {
        return $this->ExecuteFormat('INSERT INTO `{0}posts_info` 
            (`id_post`, `id_language`, `title`, `alt`, `html_short`, `html_full`, `meta_keywords`, `meta_description`) VALUES
            ("{1}", "{2}", "{3}", "{4}", "{5}", "{6}", "{7}", "{8}") ON DUPLICATE KEY UPDATE 
            `title` = "{3}", `alt` = "{4}", `meta_keywords` = "{7}", `meta_description` = "{8}", `html_short` = "{5}", `html_full` = "{6}"
        ', array(
            fsConfig::GetInstance('db_prefix'), $postId, $langiageId, $title, $alt, $short, $full, $keywords, $description
        ));
    }
    
    public function GetFullInfo($postId) 
    {
        $resultQuery = $this->ExecuteToArray(fsFunctions::StringFormat('
            SELECT `p`.*, `pi`.`title`, `pi`.`alt`, `pi`.`meta_keywords`, `pi`.`meta_description`, 
            `pi`.`id_language`, `pi`.`html_full`, `pi`.`html_short`  
            FROM `{0}posts` p JOIN `{0}posts_info` pi ON `pi`.`id_post` = `p`.`id`
            WHERE `p`.`id` = "{1}"
        ', array(
            fsConfig::GetInstance('db_prefix'),
            $postId,
        )));
        if(count($resultQuery) == 0) {
            return null;
        }
        $result = array(
            'id' => $resultQuery[0]['id'],
            'id_user' => $resultQuery[0]['id_user'],
            'date' => $resultQuery[0]['date'],
            'active' => $resultQuery[0]['active'],
            'position' => $resultQuery[0]['position'],
            'image' => $resultQuery[0]['image'],
            'tpl_short' => $resultQuery[0]['tpl_short'],
            'tpl' => $resultQuery[0]['tpl'],
            'auth' => $resultQuery[0]['auth'],
            'title' => array(),
            'alt' => array(),
            'html_short' => array(),
            'html_full' => array(),
            'meta_keywords' => array(),
            'meta_description' => array()
        );
        foreach($resultQuery as $row) {
            $result['html_short'][$row['id_language']] = $row['html_short'];
            $result['html_full'][$row['id_language']] = $row['html_full'];
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
            $posts = $this->ExecuteToArray(fsFunctions::StringFormat('SELECT `id_post` FROM `{0}posts_info` 
                WHERE `alt` = "{1}" AND `id_language` = "{2}"
            ', array(
                fsConfig::GetInstance('db_prefix'),
                $alt,
                $languageId
            )));
            foreach($posts as $post) {
                if($post['id_post'] != $id) {
                    return false;
                }
            }
        }
        return true;
    }
}