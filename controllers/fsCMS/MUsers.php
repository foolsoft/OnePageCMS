<?php
class MUsers extends cmsController 
{
  protected $_tableName = 'users';
  
  public function FormRegistration($param) 
  {
    $html = $this->CreateView(array(), $this->_Template('FormRegistration'));
    return "<form method='post' action='".$this->_My('DoRegistration')."' id='user_registration_form' class='user_registration_form'>".
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
        $this->Message(T('XMLcms_text_need_all_data'));
        return;    
    }
    if ($param->password != $param->repassword) {
      $this->Message(T('XMLcms_text_bad_pwd_confirm'));
      return;
    }
    if (!$this->_table->CheckLogin($param->login)) {
      $this->Message(T('XMLcms_text_login_not_unique'));
      return;
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
    $page = array();
    $page['title'] = $title;
    $page['meta_keywords'] = $kw == '' ? $title : $kw;
    $page['meta_description'] = $decription == '' ? $title : $decription;
    $this->Html($this->CreateView(array('page' => $page), $this->_Template('Registration')));
  }
  
  public function actionRegistration($param) 
  {
    if($this->settings->allow_register != '1') {
      return $this->HttpNotFound();
    }
    if (AUTH) {
      $this->Redirect(URL_ROOT);
      return;
    }
    $this->_GeneratePage(T('XMLcms_text_registration'));
  } 
}