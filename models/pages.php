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
  
  public function Load($page, $filter)
  {
    return $this->Select()
                ->Where(array(
                              array(
                                    array('id' => $page, 'logic' => 'OR', 'key' => $filter),
                                    array('alt' => $page),
                                    'logic' => 'AND'
                                   ),
                              array('active' => 1)
                             )
                        )
                ->Limit(1)
                ->ExecuteToArray();
  }

}