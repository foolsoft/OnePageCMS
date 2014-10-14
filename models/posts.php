<?php
class posts extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
	
	public function GetCountByCategory($idOrAlt = ALL_TYPES, $activeOnly = true)
  {
    $sql = '';
    if ($idOrAlt == -1) { //No category
      $sql = fsFunctions::StringFormat(
        "SELECT COUNT(`{0}`.`id`) as `c`
         FROM `{0}` LEFT JOIN `{1}` ON `{0}`.`id` = `{1}`.`id_post`
         WHERE `{1}`.`id_category` IS NULL"
         .($activeOnly ? ' AND `{0}`.`active` = "1"' : ''),
         array(fsConfig::GetInstance('db_prefix').'posts',
               fsConfig::GetInstance('db_prefix').'post_category'
         )
      ); 
    } else {
      $where = 'WHERE 1';
      if ($idOrAlt != ALL_TYPES) {
        $where = " AND ((`{1}`.`id` = '{2}' OR `{1}`.`alt` = '{2}') OR (`{1}`.`id` = '{4}'))"; 
      }
      if ($activeOnly) {
        $where .= ' AND `{0}`.`active` = "1"';
      }
      $sql = fsFunctions::StringFormat(
        "SELECT COUNT(`{0}`.`id`) as `c`
         FROM `{0}` JOIN `{3}` ON `{0}`.`id` = `{3}`.`id_post`
                    JOIN `{1}` ON `{3}`.`id_category` = `{1}`.`id`
         ".$where,
         array(fsConfig::GetInstance('db_prefix').'posts',
               fsConfig::GetInstance('db_prefix').'posts_category',
               $idOrAlt,
               fsConfig::GetInstance('db_prefix').'post_category',
               ALL_TYPES
         )
      );
    }
    $this->Execute($sql);
    return $this->_result->mysqlRow['c'];
  } 
	
	public function GetByCategory($idOrAlt = ALL_TYPES, $start = 0, $count = 15, $activeOnly = true)
	{
    $sql = '';
    if ($idOrAlt == -1) { //No category
      $sql = fsFunctions::StringFormat(
        "SELECT `{0}`.*
         FROM `{0}` LEFT JOIN `{1}` ON `{0}`.`id` = `{1}`.`id_post`
         WHERE `{1}`.`id_category` IS NULL
         ".($activeOnly ? ' AND `{0}`.`active` = "1"' : '')." 
         ORDER BY `{0}`.`date` DESC
         LIMIT {2}, {3}
         ",
         array(fsConfig::GetInstance('db_prefix').'posts',
               fsConfig::GetInstance('db_prefix').'post_category',
               $start,
               $count
         )
      ); 
    } else {
      $where = 'WHERE 1';
      if ($idOrAlt != ALL_TYPES) {
        $where = " AND ((`{1}`.`id` = '{2}' OR `{1}`.`alt` = '{2}') OR (`{1}`.`id` = '{6}'))"; 
      }
      if ($activeOnly) {
        $where .= ' AND `{0}`.`active` = "1"';
      }
      $sql = fsFunctions::StringFormat(
        "SELECT `{0}`.*,
                `{1}`.`id` as `category_id`,
                `{1}`.`name` as `category_name`,
                `{1}`.`alt` as `category_alt`,
                `{1}`.`tpl` as `category_tpl`
         FROM `{0}` JOIN `{5}` ON `{0}`.`id` = `{5}`.`id_post`
                    JOIN `{1}` ON `{5}`.`id_category` = `{1}`.`id`
         ".$where."
         ORDER BY `{0}`.`order`, `{0}`.`date` DESC
         LIMIT {3}, {4}
         ",
         array(fsConfig::GetInstance('db_prefix').'posts',
               fsConfig::GetInstance('db_prefix').'posts_category',
               $idOrAlt,
               $start,
               $count,
               fsConfig::GetInstance('db_prefix').'post_category',
               ALL_TYPES
         )
      );
    }
    return $this->ExecuteToArray($sql);
  }
  
  public function Get($idOtAlt)
  {
    $this->Select()
         ->Where('`id` = "'.$idOtAlt.'" OR `alt` = "'.$idOtAlt.'"')
         ->Execute();
  }
	
}