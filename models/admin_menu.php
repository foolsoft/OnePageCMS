<?php
class admin_menu extends fsDBTableExtension
{
  private $_parent = 'AdminMModules/Index';

  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function GetModules()
  {
    return  $this->Select()
                 ->Where("`parent` = '".$this->_parent."'")
                 ->Order(array('name'))
                 ->ExecuteToArray();
  }
  
  public function AddModule($name, $text, $order = 0)
  {
    $this->name = $name;
    $this->text = $text;
    $this->order = $order;
    $this->parent = $this->_parent;
    return $this->Insert()->Execute();
  }
}
?>