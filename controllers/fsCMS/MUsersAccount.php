<?php
class MUsersAccount extends cmsNeedAuthController 
{
  protected $_tableName = 'user_info';
  
  public function Finnaly()
  {
    $this->Tag('meta_keywords', $this->Tag('title'));
    $this->Tag('meta_description', $this->Tag('title'));
  }
  
  public function actionDoChangeFields($param)
  {
    $this->_Referer();
    if(!$param->Exists('user_field') || !is_array($param->user_field)) {
        return;
    }
    $user = fsSession::GetInstance('AUTH');
    $userId = $user['id'];
    $user_fields = new user_fields();
    
    $fieldsDb = $user_fields->GetAll();
    $fieldsRequest = $param->user_field;
    
    foreach ($fieldsDb as $field) {
      if($field['duty'] == 1) {
        continue;
      }
      $newValue = null;
      if(isset($fieldsRequest[$field['id']])) {
        $newValue = $fieldsRequest[$field['id']];  
      } else if (isset($fieldsRequest[$field['name']])) {
        $newValue = $fieldsRequest[$field['name']];
      }
      if($newValue !== null) {
        if($field['expression'] === '' || preg_match('/^'.$field['expression'].'$/u', $newValue)) {
            $this->_table->Change($userId, $field['id'], $newValue);
            $user['fields'][$field['name']]['value'] = $newValue;
        }
      }
    }

    fsSession::Set('AUTH', $user);
    
    $this->Message(T('XMLcms_updated'));
  }

  public function FormFields($param)
  {
    $user_fields = new user_fields();
    $fields_all = $user_fields->GetAll();
    $user_fields =  $this->_table->GetInfo(fsSession::GetArrInstance('AUTH', 'id'));
    
    $first = false;
    if(count($user_fields) > 0) {
      reset($user_fields);
      $first = key($user_fields);
    }
    
    if($first === false) {
        $user_fields = array();
        foreach($fields_all as $field) {
            $user_fields[$field['name']] = $field;
            unset($user_fields[$field['name']]['name']);
        }
    } else {
      foreach($fields_all as $field) {
          if(!isset($user_fields[$field['name']]) && $field['duty'] == 0) {
              foreach($user_fields[$first] as $key => $value) {
                if($key != 'name') {
                    $user_fields[$field['name']][$key] = $field[$key];
                }
              }
          }
      }
    }
    
    $html = $this->CreateView(array('fields' => $user_fields), $this->_Template('FormFields'));
    return "<form method='post' action='".fsHtml::Url(URL_ROOT.'MUsersAccount/DoChangeFields')."' id='user-change-fields-form' class='user-change-fields-form'>".$html.'</form>';
  }

  public function actionHello($param)
  {
    $this->Tag('title', T('XMLcms_lk'));                            
  }
  
  public function actionDoChangePassword($param)
  {
    $this->_Referer();
    
    if(ADMIN_USER_TYPE == fsSession::GetArrInstance('AUTH', 'type')) {
        return $this->Message(T('XMLcms_admin_password_in_adminpanel'));
    }
    if($param->new_password == '') {
        return $this->Message(T('XMLcms_text_empty_pwd'));
    }
    if($param->new_password != $param->new_password_again) {
        return $this->Message(T('XMLcms_text_bad_pwd_confirm'));
    }
    
    $users = new users();                                 
    if(!$users->IsUser(fsSession::GetArrInstance('AUTH', 'login'), $param->password)) {
        return $this->Message(T('XMLcms_text_invalid_password'));
    }
     
    if($users->ChangePassword(fsSession::GetArrInstance('AUTH', 'login'), $param->new_password)) {
        return $this->Message(T('XMLcms_updated'));    
    }
    $this->Message(T('XMLcms_error_action'));
  }
   
  public function FormChangePassword($param)
  {
    $html = $this->CreateView(array(), $this->_Template('FormChangePassword'));
    return "<form method='post' action='".fsHtml::Url(URL_ROOT.'MUsersAccount/DoChangePassword')."' id='user-change-password-form' class='user-change-password-form'>".$html.'</form>';   
  }
  
}