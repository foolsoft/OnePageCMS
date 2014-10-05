<?php
class users extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function CheckLogin($login) 
  {
    $this->GetOne($login, false, 'login');
    return $this->result->login != $login;
  }
  
  public function Add($login, $password, $active = 1, $type = 2) 
  {                    
    $this->login = $login;
    $this->password = md5($password);
    $this->active = $active;
    $this->type = $type;
    $this->Insert()->Execute();
    return $this->insertedId;
  }
  
  public function IsUser($login, $password)
  {
    if ($login == '') {
      return false;
    }
    $this->Select()->Where(array(array('login' => $login),
                                 array('password' => md5($password)),
                                 array('active' => 1)))->Limit(1)->Execute();
    if ($this->_result->login == $login) {
      return $this->_result->mysqlRow;
    } 
    return false;
  }
  
  public function IsAdmin($login, $password)
  {
    if ($this->isUser($login, $password) !== false &&
        $this->_result->type == ADMIN_USER_TYPE) {
      return $this->_result->mysqlRow;
    }
    return false;                                 
  }
  
  public function SetActive($login, $value) 
  {
    $this->Update(array('active'), array($value))
         ->Where('`login` = "'.$login.'"')->Execute();
  }
  
}