<?php
class user_info extends fsDBTableExtension
{
    public function __destruct()
    {
        parent::__destruct();
    }
  
    public function GetInfo($userId, $includeDuty = false)
    {
        $sql = fsFunctions::StringFormat(
          'SELECT `uf`.`id`,
                  `uf`.`name`,
                  `uf`.`title`,
                  `uf`.`expression`,
                  `ui`.`value`
           FROM `{0}` uf JOIN `{1}` ui ON `uf`.`id` = `ui`.`id_field`
           WHERE `ui`.`id_user` = "{2}" {3}
           ORDER BY `uf`.`position`   
          ',
          array(
            fsConfig::GetInstance('db_prefix').'user_fields',
            fsConfig::GetInstance('db_prefix').'user_info',
            $userId,
            ($includeDuty ? '' : 'AND `uf`.`duty` = "0"')
          )    
        );
        return $this->ExecuteToArray($sql, 'name');
    }
  
    public function GetValue($userId, $fieldId)
    {
        $result = $this->Select(array('value'))->Where(array(array('id_user' => $userId), array('id_field' => $fieldId)))->ExecuteToArray();
        return count($result) != 1 ? null : $result[0]['value'];
    }
  
    public function FindByValue($fieldIdOrName, $fieldValue)
    {
        if(is_numeric($fieldIdOrName)) {
            return $this->Select()->Where(array(array('value' => $fieldValue), array('id_field' => $fieldIdOrName)))->ExecuteToArray();
        }
        return $this->ExecuteToArray('SELECT * 
            FROM `'.fsConfig::GetInstance('db_prefix').'user_info` 
            WHERE  
                `value` = "'.$fieldValue.'" AND 
                `id_field` = (SELECT `id` FROM `'.fsConfig::GetInstance('db_prefix').'user_fields` WHERE `name` = "'.$fieldIdOrName.'")'
        );    
    }
  
    public function GetValueByFieldName($userId, $fieldName)
    {
        $result = $this->ExecuteToArray('SELECT `value` 
            FROM `'.fsConfig::GetInstance('db_prefix').'user_info` 
            WHERE 
                `id_user` = "'.$userId.'" AND 
                `id_field` = (SELECT `id` FROM `'.fsConfig::GetInstance('db_prefix').'user_fields` WHERE `name` = "'.$fieldName.'")'
        );
        return count($result) != 1 ? null : $result[0]['value'];
    }
  
    public function GetValueBySpecialType($userId, $specialType)
    {
        $result = $this->ExecuteToArray('SELECT `value` 
            FROM `'.fsConfig::GetInstance('db_prefix').'user_info` 
            WHERE 
                `id_user` = "'.$userId.'" AND 
                `id_field` = (SELECT `id` FROM `'.fsConfig::GetInstance('db_prefix').'user_fields` WHERE `special_type` = "'.$specialType.'")'
        );
        return count($result) != 1 ? null : $result[0]['value'];
    }
  
    public function Change($userId, $fieldId, $value = '')
    {
        return $this->Execute(fsFunctions::StringFormat('
            INSERT INTO `{0}` (`id_user`, `id_field`, `value`) VALUES("{1}", {2}, "{3}") ON DUPLICATE KEY UPDATE value="{3}"
        ', array(
            fsConfig::GetInstance('db_prefix').'user_info',
            $userId,
            is_numeric($fieldId) ? $fieldId : '(SELECT `id` FROM `'.fsConfig::GetInstance('db_prefix').'user_fields` WHERE `name` = "'.$fieldId.'")',
            $value
        )));
    }
  
}