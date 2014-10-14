<?php
class MAuth extends cmsController 
{
  protected $_tableName = 'users';
  private $_authAdminCount = 5;
  private $_methodAuthAdmin = 'AuthAdmin';
  
  public function actionDoAuth($param)
  {
    if(AUTH) {
      return $this->Redirect(URL_ROOT);
    }
    $this->_Referer();
    $login = fsValidator::ClearData($param->login);
    $password =  fsValidator::ClearData($param->password);
    $userData = $this->_table->IsUser($login, $password);
    if($userData !== false) {
      fsSession::Create('AUTH', $userData);  
      if(fsSession::Exists('GUEST')) {
        fsSession::Delete('GUEST');
      }
    } else {
      $this->Message(T('XMLcms_text_login_error'));
    }
  }
  
  public function actionDoAuthAdmin($param)
  {
      if(AUTH) {
        return $this->Redirect(AUTH_ADMIN ? fsHtml::Url(URL_ROOT.'AdminPanel/Hello') : URL_ROOT);
      }
      if (!fsSession::Exists('AUTH_COUNT')) {
        fsSession::Create('AUTH_COUNT', 0);  
      }
      if (fsSession::GetInstance('AUTH_COUNT') >= $this->_authAdminCount) {
        $this->Message(T('XMLcms_text_max_log'));
        return $this->Redirect($this->_My($this->_methodAuthAdmin));
      }
      $login = fsValidator::ClearData($param->login);
      $userData = $this->_table->IsAdmin($login, fsValidator::ClearData($param->password));
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
      if ($this->_authAdminCount == fsSession::GetInstance('AUTH_COUNT')) {
        $this->Message(T('XMLcms_text_max_log'));
        $this->_table->SetActive($login, 0);
      } else {
        $this->Message(T('XMLcms_text_login_error'));
      }
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
      $this->Redirect(fsHtml::Url(URL_ROOT.'AdminPanel/Hello'));
    } else {
      $this->Tag('title', T('XMLcms_text_enter'));   
    }  
  }
          
  public function FormLogin($param) 
  {
    $html = $this->CreateView(array(), $this->_Template($param->Exists('template') && $param->template != '' ? $param->template : 'FormLogin'));
    return "<form method='post' action='".fsHtml::Url(URL_ROOT.'MAuth/DoAuth')."' id='user-login-form' class='user-login-form'>".$html.'</form>';
  }
  
  public function actionAuth($param)
  {
    if (AUTH) {
      $this->Redirect(URL_ROOT);
      return;
    }
    $page = array();
    $page['title'] = T('XMLcms_page_auth');
    $page['meta_keywords'] = $page['title'];
    $page['meta_description'] = $page['title'];
    $this->Html($this->CreateView(array('page' => $page)));
  }
}