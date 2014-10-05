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
  
  private function _CheckParam($param, $checkPasswordEmpty = true)
  {
    if ($param->password == '' && $checkPasswordEmpty) {
      $this->Message(T('XMLcms_text_empty_pwd'));
    } else if ($param->password != $param->rpassword) {
      $this->Message(T('XMLcms_text_bad_pwd_confirm'));
    } else if (!$this->_table->IsUnique($param->login)) { 
      if (!$param->Exists('key') ||
          ($param->Exists('key') && $param->key != $param->login)) {
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

  public function actionEditField($param)
  {
    $user_fields = new user_fields();
    $user_fields->current = $param->key;
    if ($user_fields->id != $param->key) {
      $this->_Referer();
      return;
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
      $this->_Referer();
      return;
    }
    $types_users = new types_users();
    $this->Tag('fields', $this->_GetUserFields($param->key));
    $this->Tag('types', $types_users->GetAll());
    $this->Tag('user', $this->_table->result);
  }
  
  
  public function actionDelete($param)
  {
    if (parent::actionDelete($param) == 0) {
      $user_info = new user_info();
      if (!$param->Exists('table')) {
        $user_info->DeleteBy($param->key, 'id_user');
      } else {
        $user_info->DeleteBy($param->key, 'id_field');
      }
    }
  }
}