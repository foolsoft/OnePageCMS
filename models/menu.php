<?php
class menu extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  function GetMenuTemplate($name)
  {
    $this->Select(array('tpl'))->Where("`name` = '".$name."'")->Execute();
    return $this->_result->tpl;
  }
}