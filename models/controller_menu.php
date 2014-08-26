<?php
class controller_menu extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function DeleteByControllerName($names)
  {
    if (!is_array($names)) {
      return false;
    }
    $where = '0';
    foreach ($names as $name) {
      $where .= ' OR `controller` = "'.$name.'"';  
    }
    return $this->Delete()->Where($where)->Execute();
  }
  
  public function Add($controller, $title, $link)
  {
    $this->title = $title;
    $this->href = $link;
    $this->controller = $controller;
    return $this->Insert()->Execute(); 
  }
  
}
?>