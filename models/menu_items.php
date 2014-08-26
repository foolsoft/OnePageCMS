<?php
class menu_items extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function NullParent($id)
  {
    return $this->Update(array('parent'), array(0))
                ->Where("`id` = '".$id."'")
                ->Execute();
  }
  
  public function UpdateName($oldName, $newName)
  {
    return $this->Update(array('menu_name'), array($newName))
                ->Where("`menu_name` = '".$oldName."'")
                ->Execute();
  }
  
  public function GetMenuName($id) 
  {
    $this->current = $id;
    return $this->menu_name;
  }
  
  public function GetItemsInMenu($menuName)
  {
    return $this->Select()
                ->Order(array('title'))
                ->Where("`menu_name` = '".$menuName."'")
                ->ExecuteToArray();
  }
  
}
?>