<?php
class AdminMUsers extends AdminPanel {

  protected $_tableName = 'users';

  private function _CheckParamField($Param)
  {
    if ($Param->name == '') {
      $this->Message(T('XMLcms_text_need_all_data'));
      $this->_Referer();
      return false;
    }
    return true;
  }
  
  private function _CheckParam($Param, $checkPasswordEmpty = true)
  {
    if ($Param->password == '' && $checkPasswordEmpty) {
      $this->Message(T('XMLcms_text_empty_pwd'));
    } else if ($Param->password != $Param->rpassword) {
      $this->Message(T('XMLcms_text_bad_pwd_confirm'));
    } else if (!$this->_table->IsUnique($Param->login)) { 
      if (!$Param->Exists('key') ||
          ($Param->Exists('key') && $Param->key != $Param->login)) {
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

  public function actionAddField($Param)
  {
  }

  public function actionEditField($Param)
  {
    $user_fields = new user_fields();
    $user_fields->current = $Param->key;
    if ($user_fields->id != $Param->key) {
      $this->_Referer();
      return;
    }
    $this->Tag('field', $user_fields->result);
  }

  public function actionDoEditField($Param)
  {
    if(!$this->_CheckParamField($Param)) {
      return;
    }
    if (!$this->_CheckUnique($Param->name, 'name', $Param->key, 'id', 'user_fields')) {
      return;
    }
    parent::actionDoEdit($Param);
  }

  public function actionDoAddField($Param)
  {
    if(!$this->_CheckParamField($Param)) {
      return;
    }
    if (!$this->_CheckUnique($Param->name, 'name', false, false, 'user_fields')) {
      return;
    }
    $fieldId = parent::actionDoAdd($Param);
    if ($fieldId > 0) {
      $user_info = new user_info();
      $users = $this->_table->GetAll();
      foreach ($users as $user) {
        $user_info->Add($user['id'], $fieldId);  
      }
    }
  }

  public function actionDoAdd($Param)
  {
    if (!$this->_CheckParam($Param)) {
      return;
    }
    $Param->active =  $Param->Exists('active') ? 1 : 0;
    $Param->password = md5($Param->password);
    $userId = parent::actionDoAdd($Param);
    if ($userId > 0 && $Param->Exists('user_field')) {
      $user_info = new user_info();
      foreach ($Param->user_field as $fieldId => $value) {
        $user_info->Add($userId, $fieldId, $value);  
      }
    }
  }

  public function actionAdd($Param)
  {
    $types_users = new types_users();
    $user_fields = new user_fields();
    $this->Tag('types', $types_users->GetAll(true, false, array('name')));
    $this->Tag('fields', $user_fields->GetAll(true, false, array('title')));
  }

  public function actionFields($Param)
  {
    $user_fields = new user_fields();
    $this->Tag('fields', $user_fields->GetAll(true, false, array('name')));
  }

  public function actionIndex($Params)
  {
    $this->Tag('users', $this->_table->GetAll(true, true));
  }
  
  public function actionDoEdit($Param)
  {
  	if (!$this->_CheckParam($Param, false)) {
      return;
    }
    if (!$this->_CheckUnique($Param->login, 'login', $Param->key, 'id')) {
      return;
    }
    if ($Param->login != fsConfig::GetInstance('main_admin')) {
      $Param->active = $Param->Exists('active') ? 1 : 0;
    } else {
      $Param->active = 1;
    }
    if ($Param->password != '') {
      $Param->password = md5($Param->password);
    } else {
      $Param->Delete('password');
    }
    if (parent::actionDoEdit($Param) == 0) {
      if ($Param->Exists('user_field')) {
        $user_info = new user_info();
        foreach ($Param->user_field as $fieldId => $value) {
          $user_info->Change($Param->key, $fieldId, $value);
        }
      }  
    }
  }
  
  public function actionEdit($Param)
  {
    $this->_table->current = $Param->key;
    if ($this->_table->result->id != $Param->key) {
      $this->_Referer();
      return;
    }
    $types_users = new types_users();
    $this->Tag('fields', $this->_GetUserFields($Param->key));
    $this->Tag('types', $types_users->GetAll());
    $this->Tag('user', $this->_table->result);
  }
  
  
  public function actionDelete($Param)
  {
    if (parent::actionDelete($Param) == 0) {
      $user_info = new user_info();
      if (!$Param->Exists('table')) {
        $user_info->DeleteBy($Param->key, 'id_user');
      } else {
        $user_info->DeleteBy($Param->key, 'id_field');
      }
    }
  }
  
}
?>