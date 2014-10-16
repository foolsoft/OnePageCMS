<?php
class AdminMUsers extends AdminPanel
{
  protected $_tableName = 'users';

  private function _CheckParamField($param)
  {
    if ($param->name == '') {
      $this->Message(T('XMLcms_text_need_all_data'));
      $this->_Referer();
      return false;
    }
    return true;
  }
  
  private function _CheckParamGroup($param)
  {
    $param->table = 'types_users';
    return $this->_CheckParamField($param);
  }
  
  private function _CheckParam($param, $checkPasswordEmpty = true)
  {
    if($param->login == '') {
      $this->Message(T('XMLcms_text_need_all_data'));
    } else if ($param->password == '' && $checkPasswordEmpty) {
      $this->Message(T('XMLcms_text_empty_pwd'));
    } else if ($param->password != $param->rpassword) {
      $this->Message(T('XMLcms_text_bad_pwd_confirm'));
    } else if (true !== ($userId = $this->_table->IsUnique($param->login, 'login', 'id'))) { 
      if (!$param->Exists('key', true) || $param->key != $userId) {
        $this->Message(T('XMLcms_text_login_not_unique'));
      }
    } 
    if ($this->Message() != '') {
      $this->_Referer();
      return false;
    }
    return true;
  }

  private function _GetUserFields($userId)
  {
    $user_info = new user_info();
    return $user_info->GetInfo($userId);
  }
  
  public function actionConfig($param)
  {
    $this->Tag('settings', $this->settings);
  }
  
  public function Init($request)
  {
    $this->Tag('title', T('XMLcms_text_users'));
    parent::Init($request);
  }

  public function actionAddField($param)
  {
  }

  public function actionAddGroup($param)
  {
  }

  public function actionDoAddGroup($param)
  {
    if(!$this->_CheckParamGroup($param)) {
        return;
    }
    parent::actionDoAdd($param);
  }

  public function actionEditField($param)
  {
    $user_fields = new user_fields();
    $user_fields->current = $param->key;
    if ($user_fields->id != $param->key) {
      return $this->_Referer();
    }
    $this->Tag('field', $user_fields->result);
  }

  public function actionDoEditField($param)
  {
    if(!$this->_CheckParamField($param)) {
      return;
    }
    if (!$this->_CheckUnique($param->name, 'name', $param->key, 'id', 'user_fields')) {
      return;
    }
    parent::actionDoEdit($param);
  }

  public function actionEditGroup($param)
  {
    $types_users = new types_users();
    $type = $types_users->Get($param->key);
    if($type == null) {
        return $this->_Referer();
    }
    $this->Tag('type', $type);
  }

  public function actionDoEditGroup($param)
  {
    if(!$this->_CheckParamGroup($param)) {
        return;
    }
    parent::actionDoEdit($param);
  }

  public function actionDoAddField($param)
  {
    if(!$this->_CheckParamField($param)) {
      return;
    }
    if (!$this->_CheckUnique($param->name, 'name', false, false, 'user_fields')) {
      return;
    }
    $fieldId = parent::actionDoAdd($param);
    if ($fieldId > 0) {
      $user_info = new user_info();
      $users = $this->_table->GetAll();
      foreach ($users as $user) {
        $user_info->Add($user['id'], $fieldId);  
      }
    }
  }

  public function actionDoAdd($param)
  {
    if (!$this->_CheckParam($param)) {
      return;
    }
    $param->active =  $param->Exists('active') ? 1 : 0;
    $param->password = md5($param->password);
    $userId = parent::actionDoAdd($param);
    if ($userId > 0 && $param->Exists('user_field')) {
      $user_info = new user_info();
      foreach ($param->user_field as $fieldId => $value) {
        $user_info->Add($userId, $fieldId, $value);  
      }
    }
  }

  public function actionAdd($param)
  {
    $types_users = new types_users();
    $user_fields = new user_fields();
    $this->Tag('types', $types_users->GetAll(true, false, array('name')));
    $this->Tag('fields', $user_fields->GetAll(true, false, array('title')));
  }

  public function actionFields($param)
  {
    $user_fields = new user_fields();
    $this->Tag('fields', $user_fields->GetAll(true, false, array('name')));
  }

  public function actionIndex($param)
  {
    $this->Tag('users', $this->_table->GetAll(true, true));
  }
  
  public function actionGroups($param)
  {
    $types_users = new types_users();
    $this->Tag('types', $types_users->Get());
  }
  
  public function actionDoEdit($param)
  {
  	if (!$this->_CheckParam($param, false)) {
      return;
    }
    if (!$this->_CheckUnique($param->login, 'login', $param->key, 'id')) {
      return;
    }
    if ($param->login != fsConfig::GetInstance('main_admin')) {
      $param->active = $param->Exists('active') ? 1 : 0;
    } else {
      $param->active = 1;
    }
    if ($param->password != '') {
      $param->password = md5($param->password);
    } else {
      $param->Delete('password');
    }
    if (parent::actionDoEdit($param) == 0) {
      if ($param->Exists('user_field')) {
        $user_info = new user_info();
        foreach ($param->user_field as $fieldId => $value) {
          $user_info->Change($param->key, $fieldId, $value);
        }
      }  
    }
  }
  
  public function actionEdit($param)
  {
    $this->_table->current = $param->key;
    if ($this->_table->result->id != $param->key) {
      return $this->_Referer();
    }
    $types_users = new types_users();
    $this->Tag('fields', $this->_GetUserFields($param->key));
    $this->Tag('types', $types_users->GetAll());
    $this->Tag('user', $this->_table->result);
  }
  
  
  public function actionDelete($param)
  {
    if($param->table == 'types_users' && $param->key < 2) {
        return $this->HttpNotFound();
    }
    if (parent::actionDelete($param) == 0) {
      $user_info = new user_info();
      switch($param->table) {
        case 'user_fields':
            $user_info->DeleteBy($param->key, 'id_field');
            break;
        case 'types_users':
            $users = new users();
            $usersArray = $users->Get($param->key);
            foreach($usersArray as $user) {
                $user_info->DeleteBy($user['id'], 'id_user');
            }
            $users->DeleteBy($param->key, 'type');
            break;
        case 'users':
        default:
            $user_info->DeleteBy($param->key, 'id_user');
            break;
      }
    }
  }
}