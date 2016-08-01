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
                ->Where('`login` = "'.$login.'"')->Execute();
        }
        return false;
    }
  
    public function CheckLogin($login) 
    {
        $login = fsValidator::ClearData($login);
        $this->GetOne($login, false, 'login');
        return fsFunctions::ToLower($this->result->login) != fsFunctions::ToLower($login);
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
        $alph = '0123-zxcv456789bRTGBN%HYUJnk$lqwertyu@iop!QAZXSWE$DCmasdfghjVFMKIOLP';
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
  
    public function IsUser($login, $password, $maxAuth = 10)
    {
        $login = fsValidator::ClearData($login);
        if ($login == '') {
            return false;
        }
        
        $this->Select()->Where(array(array('login' => $login)))->Limit(1)->Execute();
        
        if ($this->_result->login == $login) {
            if($this->_result->active == 0 || $this->_result->auth_count >= $maxAuth) {
                return false;
            }
            if($this->_result->password != self::HashPassword($password)) {
                $this->Update(array('auth_count', 'active'), array($this->_result->auth_count + 1, $this->_result->auth_count + 1 >= $maxAuth ? 0 : 1))
                    ->Where('`login` = "'.$login.'"')->Execute();
                return false;
            }
            
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
            $this->Update(array('auth_count', 'date_last_auth'), array(0, date('Y-m-d H:i:s')))
                ->Where('`login` = "'.$login.'"')->Execute();
            return $result;
        } 
        return false;
    }

    public function GetList(&$users, $filterWhere = array(), $filterHaving = '', $count = 0, $from = 0)
    {
        $countQuery = 0;
        $limitString = $count > 0 ? $count : '';
        if($limitString != '') {
            if($from > 0) {
                $limitString = $from.', '.$limitString;
            }
            $limitString = 'LIMIT '.$limitString;
        }
        $isLimit = $limitString != '';
        
        $where = $this->_WhereArrayToString($filterWhere, 'u');
        if($where != '') {
            $where = 'WHERE '.$where;
        }
        if($filterHaving != '') {
            $filterHaving = 'HAVING '.$filterHaving;
        }
        
        $sql = fsFunctions::StringFormat('SELECT {0} `u`.*, `ut`.`name` as `link_type`, `ut`.`allow`, `ut`.`disallow`, GROUP_CONCAT(DISTINCT ui.value SEPARATOR "|") as info 
            FROM {1} `u` JOIN `{2}types_users` `ut` ON `u`.`type` = `ut`.`id`
            LEFT JOIN `{2}user_info` ui ON `ui`.`id_user` = `u`.`id`
            {3} GROUP BY `u`.`id` {4} {5}', array(
            $isLimit ? 'SQL_CALC_FOUND_ROWS' : '',
            $this->_struct->name,
            fsConfig::GetInstance('db_prefix'),
            $where,
            $filterHaving,
            $limitString    
        ));

        $users = $this->ExecuteToArray($sql);
        if($isLimit) {
            $this->Execute('SELECT FOUND_ROWS() as c');
            $countQuery = $this->result->mysqlRow['c'];
        } else {
            $countQuery = count($users);
        }
        return $countQuery;
    }
    
    public function IsAdmin($login, $password, $maxAuth = 5)
    {
        $result = $this->isUser($login, $password, $maxAuth);
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