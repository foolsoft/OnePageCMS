<?php
class MUsers extends cmsController 
{
  protected $_tableName = 'users';
  protected $_loginRegex = '/^[a-zA-Z0-9_\-]+$/';

  public function FormRegistration($param) 
  {
    $html = $this->CreateView(array(), $this->_Template('FormRegistration'));
    return '<form method="post" action="'.$this->_My('DoRegistration').'" id="user-registration-form" class="user-registration-form">'.
            $html.
        '</form>';
  }
  
  public function actionAjaxCheckEmail($param)
  {
    $user_fields = new user_fields();
    $user_fields->GetSpecialField(fsSpecialFields::Email);
    $userFieldEmailName = $user_fields->result->name;
    $user_info = new user_info();
    $emailExists = array();
    if($param->email != '' && $userFieldEmailName != '') {
      $emailExists = $user_info->FindByValue($userFieldEmailName, $param->email);
    }
    $result = count($emailExists) > 0;
    if(!$result && $param->login != '') {
        $result = $this->_table->CheckLogin($param->login);
    }
    $this->Json(array('status' => $result));
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
    
    $user_fields = new user_fields();
    $user_fields->GetSpecialField(fsSpecialFields::Email);
    $userFieldEmailName = $user_fields->result->name;
    $user_info = new user_info();
    $uf = $param->user_field;
    if(!empty($uf[$userFieldEmailName])) {
        $emailExists = $user_info->FindByValue($userFieldEmailName, $uf[$userFieldEmailName]);
        if(count($emailExists) > 0) {
            return $this->Message(T('XMLcms_text_exist_email'));
        }
    }

    $userId = $this->_table->Add($param->login, $param->password);
    if ($userId > 0 && $param->Exists('user_field')) {
      $user_fields = $user_fields->GetAll();
      foreach ($user_fields as $field) {
        if(isset($uf[$field['name']]) && ($field['expression'] == '' || preg_match('/^'.$field['expression'].'$/', $uf[$field['name']]))) {
            if($userFieldEmailName == $field['name'] && !empty($uf[$field['name']])) {
                $mailHtml = $this->CreateView(array('data' => $param->ToArray()), $this->_Template('RegistrationMail'));
                fsFunctions::Mail($uf[$field['name']], T('XMLcms_text_reg_on_site').' '.$_SERVER['SERVER_NAME'], $mailHtml, CMSSettings::GetInstance('robot_email'));
            }
            $user_info->Change($userId, $field['id'], $uf[$field['name']]);
        }
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