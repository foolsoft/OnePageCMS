<?php
class search extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function CreateQuery($textForSearch)
  {
    if ($textForSearch == '') {
      return array();
    }
    $this->Select()->Execute('', false);
    $result = array();
    while ($this->Next()) {
      $fields = explode(',', $this->result->search_fields);
      $clause = '';
      foreach ($fields as $field) {
        $clause .= ($clause == '' ? '' : ' OR ').'`'.$field.'` LIKE "%'.$textForSearch.'%"';  
      }
      $result[] = array( 
          'query' => fsFunctions::StringFormat(
                      "SELECT * FROM `{0}{1}` WHERE {2}",
                      array(
                        fsConfig::GetInstance('db_prefix'),
                        $this->result->table_name,
                        $clause  
                      )
                     ),
          'title' => $this->result->title,
          'link' => $this->result->link   
      );
    }
    return $result;
  }
}