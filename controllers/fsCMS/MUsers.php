<?php
class MUsers extends cmsController 
{
  protected $_tableName = 'users';
  protected $_loginRegex = '/^[a-zA-Z0-9_\-]+$/';

  public function FormRegistration($param) 
  {
    $html = $this->CreateView(array(), $this->_Template('FormRegistration'));
    return "<form method=\"post\" action=\"".$this->_My('DoRegistration')."\" id=\"user-registration-form\" class=\"user-registration-form\">".
              $html.
           '</form>';
  }
  
  public function actionDoRegistration($param) 
  {
    if($this->settings->allow_register != '1') {
      return $this->HttpNotFound();
    }
    $this->_Referer();
    if ($param->login == '' || $param->password == '') {
        return $this->Message(T('XMLcms_text_need_all_data'));
    }
    if (!fsValidator::Match($param->login, $this->_loginRegex)) {
      return $this->Message(T('XMLcms_invalid_login'));
    }
    if ($param->password != $param->repassword) {
      return $this->Message(T('XMLcms_text_bad_pwd_confirm'));
    }
    if (!$this->_table->CheckLogin($param->login)) {
      return $this->Message(T('XMLcms_text_login_not_unique'));
    } 
    $userId = $this->_table->Add($param->login, $param->password);
    if ($userId > 0) {
      $user_info = new user_info();
      $user_fields = new user_fields();
      $user_fields = $user_fields->GetAll();
      $uf = $param->Exists('user_field') ? $param->user_field : array();
      foreach ($user_fields as $field) {
        $value = isset($uf[$field['name']]) ? $uf[$field['name']] : '';
        $user_info->Add($userId, $field['id'], $value);
      }
    }
    $this->Redirect('');
    $this->Tag('compleate', true);
    $this->_GeneratePage(T('XMLcms_text_registration'));
  }
  
  private function _GeneratePage($title, $kw = '', $decription = '')
  {
    $page = array(
        'title' => $title,
        'meta_keywords' => $kw == '' ? $title : $kw,
        'meta_description' => $decription == '' ? $title : $decription,
    );
    $this->Html($this->CreateView(array('page' => $page), $this->_Template('Registration')));
  }
  
  public function actionRegistration($param) 
  {
    if($this->settings->allow_register != '1') {
      return $this->HttpNotFound();
    }
    if (AUTH) {
      return $this->Redirect(URL_ROOT);
    }
    $this->_GeneratePage(T('XMLcms_text_registration'));
  } 
}