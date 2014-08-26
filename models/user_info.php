<?php
class user_info extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function Add($userId, $fieldId, $value = '')
  {
    $this->id_user = $userId;
    $this->id_field = $fieldId;
    $this->value = $value;
    return $this->Insert()->Execute();
  }
  
  public function GetInfo($userId)
  {
    $sql = fsFunctions::StringFormat(
      'SELECT `{0}`.`id`,
              `{0}`.`name`,
              `{0}`.`title`,
              `{1}`.`value`
       FROM `{0}` JOIN `{1}` ON `{0}`.`id` = `{1}`.`id_field`
       WHERE `{1}`.`id_user` = "{2}"   
      ',
      array(
        fsConfig::GetInstance('db_prefix').'user_fields',
        fsConfig::GetInstance('db_prefix').'user_info',
        $userId
      )    
    );
    return $this->ExecuteToArray($sql);
  }
  
  public function Change($userId, $fieldId, $value)
  {
    return $this->Update(array('value'), array($value))
                ->Where("`id_user` = '".$userId."' AND `id_field` = '".$fieldId."'")
                ->Execute();
  }
  
}
?>