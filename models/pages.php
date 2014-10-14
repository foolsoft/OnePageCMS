<?php
class pages extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
	
  public function GetMenuPages()
  {
    return $this->Select()
                ->Order(array('title'))
                ->Where("`in_menu` = '1'")
                ->ExecuteToArray();
  }
  
  public function Load($pageId = false, $pageAlt = false)
  {
    if(!is_numeric($pageId) && $pageAlt === false) {
        return array();
    }
    $this->Select();
    $where = array(array('active' => 1));
    if($pageAlt !== false) {
        $where[] = array('alt' => $pageAlt);
    } else {
        $where[] = array('id' => $pageId);
    }
    $this->Where($where);
    return $this->Limit(1)->ExecuteToArray();
  }
}