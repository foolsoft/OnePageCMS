<?php
class MAuth extends cmsController 
{
  protected $_tableName = 'users';
  private $_authCount = 5;
  private $_methodAuthAdmin = 'AuthAdmin';
  
  public function Finnaly()
  {
    $this->Tag('meta_keywords', $this->Tag('title'));
    $this->Tag('meta_description', $this->Tag('title'));
  }
  
  public function actionDoAuth($param)
  {
    if(AUTH) {
        return $this->Redirect(URL_ROOT);
    }
    $this->_Referer();
    $userData = $this->_table->IsUser($param->login, $param->password);
    if($userData !== false) {
        fsSession::Create('AUTH', $userData);
        if(fsSession::Exists('GUEST')) {
            fsSession::Delete('GUEST');
        }
        return;
    }
    $this->Message(T('XMLcms_text_login_error'));
    $this->Redirect(fsHtml::Url(URL_ROOT.'user/auth'));
  }
  
  public function actionDoAuthAdmin($param)
  {
      if(AUTH) {
        return $this->Redirect(AUTH_ADMIN ? fsHtml::Url(URL_ROOT.'AdminPanel/Hello') : URL_ROOT);
      }
      if (!fsSession::Exists('AUTH_COUNT')) {
        fsSession::Create('AUTH_COUNT', 0);  
      }
      if (fsSession::GetInstance('AUTH_COUNT') >= $this->_authCount) {
        $this->Message(T('XMLcms_text_max_log'));
        return $this->Redirect($this->_My($this->_methodAuthAdmin));
      }
      $login = fsValidator::ClearData($param->login);
      $userData = $this->_table->IsAdmin($login, fsValidator::ClearData($param->password), $this->_authCount);
      if($userData !== false) {
        fsSession::Delete('AUTH_COUNT');
        fsSession::SetOrCreate('AUTH', $userData);
        if(fsSession::Exists('GUEST')) {
            fsSession::Delete('GUEST');
        }
        return $this->Redirect(fsHtml::Url(URL_ROOT.'AdminPanel/Hello'));
      }
      $this->Redirect($this->_My($this->_methodAuthAdmin));
      fsSession::Set('AUTH_COUNT', fsSession::GetInstance('AUTH_COUNT') + 1);
      if ($this->_authCount == fsSession::GetInstance('AUTH_COUNT')) {
        $this->Message(T('XMLcms_text_max_log'));
        $this->_table->SetActive($login, 0);
      } else {
        $this->Message(T('XMLcms_text_login_error'));
      }
  }
  
  public function actionForgot($param) 
  {
    if(AUTH) {
        return $this->Redirect(URL_ROOT);
    }
    $this->Tag('title', T('XMLcms_text_passwordrecover'));
  }
  
  public function FormForgot($param) 
  {
    if(AUTH) {
        return '';
    }
    $html = $this->CreateView(array(), $this->_Template('FormForgot'));
    return "<form method='post' action='".fsHtml::Url(URL_ROOT.'MAuth/DoForgot')."' id='user-forgot-form' class='user-forgot-form'>".$html.'</form>';     
  }
  
  public function actionDoForgot($param) 
  {
    /*
     * Put here user filed name for restore (e.g. email). It should be created in AdminPanel.
     * Next code - procedure of restore by email address.
     * If needed you can change restore algorithm.
     */
    $userFieldForRestore = fsSpecialFields::Email;
    
    $user_fields = new user_fields();
    $user_fields->GetSpecialField($userFieldForRestore);
    $userFieldForRestoreName = $user_fields->result->name;
    unset($user_fields);
    
    if(AUTH) {
        return $this->Redirect(URL_ROOT);
    }
    
    $this->Redirect(fsHtml::Url(URL_ROOT.'user/forgot'));
    
    if($userFieldForRestore == '') {
        return $this->Message(T('XMLcms_text_not_implemented'));
    }
    
    $user_info = new user_info();
    if($param->code != '' && $param->user != '') {
        $this->_table->Select()->Where(array(array('login' => $param->user), array('password' => $param->code)))->Execute();
        if($this->_table->result->login == $param->user) {
            $userId = $this->_table->result->id;
            $newPassword = users::GeneratePassword();
            $this->_table->ChangePassword($param->user, $newPassword);
            if(fsFunctions::Mail($user_info->GetValueBySpecialType($userId, $userFieldForRestore), T('XMLcms_text_new_password'), fsFunctions::StringFormat(T('XMLcms_you_new_password'), array($newPassword)), CMSSettings::GetInstance('robot_email'))) {
                return $this->Message(T('XMLcms_text_look_new_password'));
            }
        }
        return $this->Message(T('XMLcms_error_again'));
    }
    
    if($param->$userFieldForRestoreName == '') {
        return $this->Message(T('XMLcms_text_need_all_data'));
    }
    
    if(!fsCaptcha::Check($param->captcha)) {
        return $this->Message(T('XMLcms_captcha_wrong'));
    }
    
    $this->Message(T('XMLcms_text_no_email_users'));
    $userInfo = $user_info->FindByValue($userFieldForRestoreName, $param->$userFieldForRestoreName);
    if(count($userInfo) != 1) {
        return;
    }
    $this->_table->GetOne($userInfo[0]['id_user'], false);
    if($this->_table->result->id != $userInfo[0]['id_user'] || fsConfig::GetInstance('main_admin') == $this->_table->result->login) {
        return;
    }
    $confirmUrl = $this->_My('DoForgot?user='.$this->_table->result->login.'&code='.$this->_table->result->password);
    if(fsFunctions::Mail(
        $user_info->GetValueBySpecialType($this->_table->result->id, $userFieldForRestore), 
        T('XMLcms_request_new_password'),
        fsFunctions::StringFormat(T('XMLcms_request_forgot_text'), array($confirmUrl, fsFunctions::GetIp(), date('d.m.Y H:i:s'), URL_ROOT )),
        CMSSettings::GetInstance('robot_email'))) {
        return $this->Message(T('XMLcms_request_forgot_confirm'));
    }
    return $this->Message(T('XMLcms_error_again'));
  }
  
  public function actionDoLogout($param)
  {
    if (fsSession::Exists('AUTH')) {
        fsSession::Delete('AUTH');
    } 
    $this->Redirect(URL_ROOT);
  }
  
  public function actionDoLogoutAdmin($param)
  {
    $this->actionDoLogout($param);
    $this->Redirect($this->_My($this->_methodAuthAdmin));
  }
  
  public function actionAuthAdmin($param)
  {
    if(AUTH_ADMIN) {
        return $this->Redirect(fsHtml::Url(URL_ROOT.'AdminPanel/Hello'));
    } 
    $this->Tag('title', T('XMLcms_text_enter'));   
  }
          
  public function FormLogin($param) 
  {
    if(AUTH) {
        return '';
    }
    $html = $this->CreateView(array(), $this->_Template($param->Exists('template') && $param->template != '' ? $param->template : 'FormLogin'));
    return "<form method='post' action='".fsHtml::Url(URL_ROOT.'MAuth/DoAuth')."' id='user-login-form' class='user-login-form'>".$html.'</form>';
  }
  
  public function actionAuth($param)
  {
    if (AUTH) {
        return $this->Redirect(fsHtml::Url(URL_ROOT.'user/account'));
    }
    $this->Tag('title', T('XMLcms_page_auth'));
  }
}