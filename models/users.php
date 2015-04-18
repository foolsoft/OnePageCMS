<?php
class users extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function ChangePassword($login, $newPassword)
  {
    $newPassword = users::HashPassword($newPassword);
    if($newPassword !== '') {
        return $this->Update(array('password'), array($newPassword))
            ->Where('`login` = "'.$login.'"')
            ->Execute();
    }
    return false;
  }
  
  public function CheckLogin($login) 
  {
    $login = fsValidator::ClearData($login);
    $this->GetOne($login, false, 'login');
    return $this->result->login != $login;
  }
  
  public function Get($idType = false) 
  {
    $this->Select();
    if($idType !== false) {
        $this->Where('`type` = "'.$idType.'"');
    } 
    return $this->Order(array('login'))->ExecuteToArray();
  }
  
  public static function HashPassword($password)
  {
    if($password === '') {
        return '';
    }
    return sha1(fsConfig::GetInstance('secret').$password); 
  }
  
  public static function GeneratePassword($length = 7)
  {
    if($length < 1) {
        $length = 7;
    }
    $result = '';
    $alph = '0123-zxcv456789bRTGBNHYUJnk$lqwertyuiop!QAZXSWEDCmasdfghjVFMKIOLP';
    $alphLength = strlen($alph);
    for($i = 0; $i < $length; ++$i) {
        $result .= $alph[rand(0, $alphLength)];
    }
    return $result; 
  }
  
  public function Add($login, $password, $active = 1, $type = 2) 
  {                    
    $this->login = $login;
    $this->password = self::HashPassword($password);
    $this->active = $active;
    $this->type = $type;
    $this->Insert()->Execute();
    return $this->insertedId;
  }
  
  public function IsUser($login, $password)
  {
    $login = fsValidator::ClearData($login);
    $password = fsValidator::ClearData($password);
    if ($login == '') {
      return false;
    }
    $this->Select()->Where(array(array('login' => $login),
                                 array('password' => self::HashPassword($password)),
                                 array('active' => 1)))->Limit(1)->Execute();
    if ($this->_result->login == $login) {
      $result = array();
      foreach($this->_result->mysqlRow as $field => $value) {
        if($field != 'password') {
            $result[$field] = $value;
        }
      }
      $types_users = new types_users();
      $type = $types_users->Get($result['type']);
      foreach($type as $field => $value) {
        if($field != 'id') {
            $result['type_'.$field] = $value;
        }
      }
      return $result;
    } 
    return false;
  }
  
  public function IsAdmin($login, $password)
  {
    $result = $this->isUser($login, $password);
    if ($result !== false && $result['type'] == ADMIN_USER_TYPE) {
       return $result;
    }
    return false;                                 
  }
  
  public function SetActive($login, $value) 
  {
    return $this->Update(array('active'), array($value))->Where('`login` = "'.$login.'"')->Execute();
  }
  
}