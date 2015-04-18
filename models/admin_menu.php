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
  
  public function AddModule($contoller, $startPage, $text, $order = 0)
  {
    $this->name = 'Admin'.$contoller.'/'.$startPage;
    $this->text = $text;
    $this->position = $order;
    $this->parent = $this->_parent;
    $this->in_panel = $startPage != '' ? 1 : 0;
    return $this->Insert()->Execute();
  }
}