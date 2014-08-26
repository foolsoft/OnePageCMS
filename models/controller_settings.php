<?php
class controller_settings extends fsDBTable
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
  
  public function Add($controller, $name, $value = '')
  {
    $this->name = $name;
    $this->value = $value;
    $this->controller = $controller;
    return $this->Insert()->Execute(); 
  }
  
	public function Set($controllerName, $settingName, $value)
	{
    return $this->Update(array('value'), array($value))
                ->Where('`controller` = "'.$controllerName.'" AND
                        `name` = "'.$settingName.'"')
                ->Execute();
  }
	
  public function Load($className)
  {
    $isAdminController = substr(strtolower($className), 0, 5) == 'admin';
    $secondName = ($isAdminController ? substr($className, 5) : 'Admin'.$className );
    return $this->Select()
                ->Where("`controller` = '".$className."' OR `controller` = '".$secondName."'")
                ->Execute('', false);
  }
  
}
?>