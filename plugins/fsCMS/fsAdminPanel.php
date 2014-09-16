<?php
class AdminPanel extends cmsController
{
  public function CreateView($params = array(), $template = '', $show = false, $adminMode = null) 
  {
    return parent::CreateView($params, $template, $show, is_null($adminMode) ? true : $adminMode);
  }
  
  protected function _TemplatePath($folder = null)
  { 
    if ($folder === null) {
      $folder = get_class($this);
    }
    $path = PATH_TPL.CMSSettings::GetInstance('template_admin').'/'.$folder;
    if (!is_dir($path)) {
      $path = parent::_TemplatePath($folder);
    }
    return fsFunctions::Slash($path);
  }
  
  protected function _Do($Param, $Method, $Prefix = '')
  {
    $Prefix .= $Method;
    try {
      if (!empty($Method) && method_exists($this, $Prefix)) {
        return $this->$Prefix($Param);
      } else {
        $this->Message(T('XMLcms_invalid_command'));
      }
    } catch (Exception $e) {
      $this->Message(T('XMLcms_invalid_data').': '.$e);
    }
  }

  protected function _SetRedirect($url = '')
  {
    $this->Redirect(!empty($url) ? $this->_My($url) : $this->_Referer(false));
  }
  
  private function _PanelTop()
  {
    return $this->CreateView(array(), $this->_Template('PanelTop', 'AdminPanel'));   
  }
  
  private function _PanelSupport()
  {
    return '<form action="'.fsHtml::Url($myLink.'Support').'" method="post" id="cms-support">'.
      $this->CreateView(array(), $this->_Template('PanelSupport', 'AdminPanel')).'</form>';   
  }
  
  private function _Sidebar()
  {
    $menu = MenuGenerator::Get(URL_ROOT, 'admin_menu', 'parent', 'a-menu', 'name', 'name', 'text',  array('order'), '1');
    return $this->CreateView(array('menu' => $menu), $this->_Template('Sidebar', 'AdminPanel'));
  }
 
  public function actionDictionary($param)
  {
    $xml = fsCache::GetXml('_text_dictionary_'.fsSession::GetInstance('Language').'.xml');
    if($xml !== null) {
      $result = $xml->xpath('/dictionary');    
      $json = array();
      $substr = $param->Exists('substr') && $param->substr != '' ? strtolower($param->substr) : false;
      foreach($result[0] as $name => $text) {
        $cleanName = substr($name, 3);
        if($substr === false || (strpos($cleanName, $substr) !== false || strpos(strtolower(T($name)), $substr) !== false)) {
          $json[] = array('text' => $cleanName, 'value' => $name);
        }
      }
      $this->Json($json);
    } else {
      $this->EmptyResult(true);
    }
  }
  
  public function actonEdit($param)
  {
    if (!$param->Exists('key') || $param->key == '') {
     $this->Message(T('XMLcms_nokey'));
     return $this->_Referer(); 
    }
    $this->_Do($param, $param->call, "Edit");
  }
  
  public function Init($request) 
  {
    if (!AUTH_ADMIN) {
      return $this->Redirect(fsHtml::Url(URL_ROOT.'MAuth/AuthAdmin'));
    }
    $this->_view->actionsCompile = false;
    
    $this->Tag('cmsVersion', $this->settings->version);
    $this->Tag('cmsCacheTableStatus', fsConfig::GetInstance('cache_table') ? T('XMLcms_on') : T('XMLcms_off'));
    $this->Tag('cmsLoadTableCacheStatus', fsConfig::GetInstance('cache_use') ? T('XMLcms_on') : T('XMLcms_off'));               
    $OP = T("XMLcms_homepage");
    $this->Tag('developerHomePage', '<a href="http://foolsoft.ru" title="'.$OP.'" target="_blank">'.T('XMLcms_a').'</a>');
    
    $altLock = 'XMLcms_lock';
    $textLock = 'XMLcms_unlocked';
    if (file_exists(FILE_LOCK)) {
      $altLock = 'XMLcms_unlock';
      $textLock = 'XMLcms_locked';
    }
    $textClearCache = T('XMLcms_cache_clear');
    $this->Tag('linkClearCache', 
      '<a href="javascript:fsCMS.Ajax(\''.fsHtml::Url($this->_link.'AjaxClearCache').'\', \'POST\', false, \'a_clear_cache\', \'a_clear_cache\', 16);" title=\''.$textClearCache.'\'>'.$textClearCache.'</a>');
    $this->Tag('linkLock',
      '<a href="javascript:fsCMS.Ajax(\''.fsHtml::Url($this->_link.'AjaxLockSite').'\', \'POST\', false, \'a_site_lock\', \'a_site_lock\', 16);" title=\''.T($altLock).'\'>'.T($textLock).'</a>');
    $this->Tag('linkTemplateManager', fsHtml::Link($this->_link.'TemplateManager', T('XMLcms_template_editor'), false, array('class' => 'fancybox fancybox.ajax')));

    $this->Tag('panelTop', $this->_PanelTop());
    $this->Tag('sidebar', $this->_Sidebar());
    $this->Tag('panelSupport', $this->_PanelSupport());

    parent::Init($request);
  }
  
  public function actionTemplateManagerSave($param)
  {
    $pathCms = PATH_TPL.'fsCMS/';
    $template = $param->template; 
    $file = $param->file;
    $array = array('/', '.');
    $path = $pathCms.$template.'/'.$file;
    if($template == '' || $file == '' || in_array($template[0], $array)  
      || in_array($file[0], $array) || !file_exists($path)) {
      return $this->Html(T('XMLcms_error_action'));
    }
    fsFileWorker::UpdateFile($path, $param->text);
    $this->Html(T('XMLcms_updated'));
  }
  
  public function actionTemplateManager($param)
  {
    $pathCms = PATH_TPL.'fsCMS/';
    $template = $param->template;
    $templatesCms = fsFunctions::DirectoryInfo($pathCms, false, true);
    $templatesCms = $templatesCms['NAMES'];
    if(count($templatesCms) == 0) {
      return $this->Message(T('XMLcms_error_action'));
    }
    if($template == '' || in_array($template[0], array('/', '.'))) {
      $template = $templatesCms[0]; 
    }
    
    if($param->file != '') {
      $path = $pathCms.$param->template.'/'.$param->file;
      return $this->Html('<textarea name="text" id="template-manager-content" class="hidden"></textarea><div id="template-manager-editor" style="width:98%;height:300px;">'.
        (file_exists($path) ? htmlspecialchars(trim(file_get_contents($path))) : '').'</div>');  
    }
    
    $files = array();
    $controllers = fsFunctions::DirectoryInfo($pathCms.$template, false, true);
    $tplFormat = strlen(EXT_TPL) > 0 && substr(EXT_TPL, 0, 1) == '.' ? substr(EXT_TPL, 1) : EXT_TPL;
    foreach($controllers['NAMES'] as $controller) {
      $path = $pathCms.$template.'/'.$controller.'/';
      $templates = fsFunctions::DirectoryInfo($path, true, false, false, array($tplFormat));
      foreach($templates['NAMES'] as $tpl) {
        $files[] = $controller.'/'.$tpl;  
      }
    }
    $this->Tag('templates', $templatesCms);
    $this->Tag('template', $template);
    $this->Tag('files', $files);
    
    if(!$param->Exists('template')) {
      $this->Html('<form action="'.$this->_My('TemplateManagerSave').'" method="post" onsubmit="return FormAjax(this);" data-result-id="template-manager-message">'.
        $this->CreateView(array(), $this->_Template('TemplateManager', 'AdminPanel')).'</form><script type="text/javascript">'.
        'TemplateManagerLoadFile("'.(count($files) > 0 ? $files[0] : '').'");</script>');
      
    } else {
      if(count($files) == 0) {
        $this->EmptyResponse(true);
      } else {
        foreach($files as $file) {
          $this->Html('<option value="'.$file.'">'.$file.'</option>');
        }
      }
    }
  }
  
  public function actionAjaxClearCache($param)
  {
    $this->Html(T('XMLcms_deleted'));
    fsSession::Delete('Language');
    fsCache::Clear();
    fsFunctions::DeleteFile(PATH_JS.'initFsCMS.js');
  }
  
  public function actionSupport($param)
  {
    $this->_Referer();
    if($param->name == '' || $param->text == '' || $param->email == '') {
      return $this->Message(T('XMLcms_text_need_all_data'));
    }
    $text = $param->text.'<hr />'.$param->name.' ('.$param->email.') / '.date('Y-m-d H:i:s').' / '.fsFunctions::GetIp().' / '.URL_ROOT;
    if(fsFunctions::Mail(EMAIL_SUPPORT, T('XMLcms_support_mail_title'), $text, $this->settings->robot_email)) {
      $this->Message(T('XMLcms_request_sent'));
    } else {
      $this->Message(T('XMLcms_error_action'));
    }
  }
  
  public function actionAjaxLockSite($Param)
  {
    $altLock = 'XMLcms_unlock';
    if (file_exists(FILE_LOCK)) {
      unlink(FILE_LOCK);
      $altLock = 'XMLcms_lock';
      $this->Html('XMLcms_unlocked');
    } else {
      fclose(fopen(FILE_LOCK, 'w+'));
      $this->Html('XMLcms_locked');
    }
    $altLock = T($altLock);
    $this->Html(
      fsFunctions::StringFormat(
        "<a href=\"javascript:fsCMS.Ajax('{0}', 'POST', false, 'a_site_lock', 'a_site_lock', 16);\" title='{1}'>{2}</a>",
        array($this->_My('AjaxLockSite'), $altLock, T($this->Html()))
        ),
      true
    );
  } 
  
 
  public function actionActivate($Param)
  {
    $this->_SetRedirect($Param->referer);
    if ($Param->Exists('key')) {
      if ($Param->field == '') {
        $Param->field = 'active';
      }
      $table_key = $Param->table_key;
      $obj = $Param->table == '' ?  $this->_table : new fsDBTable($Param->table); 
      if ($Param->table_key == '') {
        $Param->table_key = ($obj->key == '' ?  'id' : $obj->key);
      }
      if ($obj == null) {
        $this->Message(T('XMLcms_bme_activate'));
        return -1;
      } else {
        $key = $Param->key;
        $where = "`".$Param->table_key."`";
        if(is_array($key)) {
          $c = count($key);
          $where .= ' IN (';
          for($i = 0; $i < $c; ++$i) {
            $where .= "'".$key[$i]."'".($i == $c - 1 ? '' : ', ');  
          }
          $where .= ')';
        } else {
          $where .= " = '".$Param->key."'";
        }
        $obj->Update(array($Param->field), array('1'))->Where($where)->Execute();
        return 0;
      }
    }
    return -1;
  }
  
  public function actionMultiAction($param)
  {
    $this->_SetRedirect($Param->referer);
    if (!$param->Exists('type') || !$param->Exists('keys')) {
      return -1;
    }
    $keys = $param->keys;
    if (!is_array($keys)) {
      return -1;
    }
    $param->key = $keys;
    $param->Delete('keys');
    switch($param->type) {
      case 'activate':  return $this->actionActivate($param);
      case 'delete': return $this->actionDelete($param);
      default: return 404;
    }
  }
  
  public function actionDelete($Param)
  {
    $this->_SetRedirect($Param->referer);
    if (!$Param->Exists('key')) {
      return -1;
    }
    $return = 0;
    if ($Param->Exists('confirm')) {
      $obj = !$Param->Exists('table') ? $this->_table : new fsDBTable($Param->table);
      if ($obj == null) {
        $this->Message(T('XMLcms_bme_delete'));
        $return = 4;
      } else {
        $key = $Param->key;
        $where = !$Param->Exists('table_key')
                  ? '`'.($obj->key == '' ?  'id' : $obj->key).'`'
                  : "`".$Param->table_key."`";
        if(is_array($key)) {
          $c = count($key);
          $where .= ' IN (';
          for($i = 0; $i < $c; ++$i) {
            $where .= "'".$key[$i]."'".($i == $c - 1 ? '' : ', ');  
          }
          $where .= ')';
        } else {
          $where .= " = '".$Param->key."'";
        }
        $obj->Delete()->Where($where)->Execute();
        if ($Param->referer == '') {
          $this->_SetRedirect('Index');
        }
      }
      $this->Message(T('XMLcms_record_deleted'));
    } else {
      $arr = $Param->GetStruct();
      $paramStr = '';
      foreach ($arr as $P) {
        if ($P == 'PHPSESSID' || $P == 'controller' || $P == 'method' ||
            $P == 'includeBody' || $P == 'includeHead') {
            continue;
        } else {
          $temp = $Param->$P;
          if(!is_array($temp)) {
            $paramStr .= '&'.$P.'='.$Param->$P;
          } else {
            $c = count($temp);
            foreach($temp as $idx => $value) {
              $paramStr .= '&'.$P.'['.$idx.']='.$value;
            }
          }
        }
      }
      $this->Tag('urlNo', $this->Redirect());
      $this->Tag('urlYes', $this->_My('Delete?confirm=ok'.$paramStr));
      $this->Html($this->CreateView(array(), $this->_Template('Delete', 'AdminPanel')));
      $this->Redirect('');
      $return = 2;
    }
    return $return;
  }
  
  public function actionDoAdd($Param)
  { 
    $this->_SetRedirect($Param->referer);
    $return = -1;
    if ($Param->Exists('call') && $Param->call != '') {
      $return = $this->_Do($Param, $Param->call, 'actionDoAdd');
    } else {
      $obj = !$Param->Exists('table') ? $this->_table : new fsDBTable($Param->table);
      if ($obj == null) {
        $this->Message(T('XMLcms_bme_add'));
      } else {  
        foreach($obj->columns as $c) {
          if($Param->Exists($c)) {
            $obj->$c = $Param->$c;
          }
        }
        $obj->Insert()->Execute();
        $return = $obj->insertedId;
      }
    }
    if ($this->Message() == '') {
      $this->Message(T('XMLcms_added'));
    }
    return $return;
  }
  
  public function actionDoEdit($Param)
  { 
    $this->_SetRedirect($Param->referer);
    $return = 0;
    if (!$Param->Exists('key')) {
      $this->Message(T('XMLcms_unknow_key'));
      $return = 2;
    } else {
      if ($Param->Exists('call') && $Param->call != '') {
        $return = $this->_Do($Param, $Param->call, 'actionDoEdit');
      } else {
        $obj = !$Param->Exists('table') ? $this->_table : new fsDBTable($Param->table);
        if ($obj == null) {
          $this->Message(T('XMLcms_bme_edit'));
          $return = 1;
        } else {  
          $tk = !$Param->Exists('table_key') ? ($obj->key == '' ?  'id' : $obj->key) : $Param->table_key;
          $where = '`'.$tk.'` = "'.$Param->key.'"';
          $new_values = array();
          $idx = 0;
          $cols_to_edit = array();
          foreach($obj->columns as $c) {
            if ($Param->Exists($c) || $obj->GetType($c) == 'enum') {
              $new_values[$idx] = $Param->Exists($c) ? $Param->$c : 0;
              $cols_to_edit[] = $c;
              if ($c == $tk) {
                $redirect = str_replace('key='.$Param->key, 'key='.$new_values[$idx], $this->Redirect()); 
                $redirect = str_replace('/key/'.$Param->key, '/key/'.$new_values[$idx], $redirect); 
                $this->Redirect($redirect);
              }
            }
            if (isset($new_values[$idx])) {
              ++$idx; 
            }
          }
          $obj->Update($cols_to_edit, $new_values)->Where($where)->Execute();
        }
      }
    }
    if ($this->Message() == '') {
      $this->Message(T('XMLcms_updated'));
    } 
    return $return;
  }

  public function actionAjaxTemplateFiles($Param)
  {
    $this->_response->empty = true;
    $this->Html($this->_ThemeTemplateFiles($Param->theme));
  }
  protected function _ThemeTemplateFiles($theme, $selected = false)
  {
    $arr = fsFunctions::DirectoryInfo(PATH_TPL.$theme, false, true);
    if ($selected === false) {
      $selected = $this->settings->main_template;
    } 
    $html = '';
    $theme = fsFunctions::Slash($theme);
    for ($i = 0; $i < $arr['LENGTH']; ++$i) {
      if (!file_exists(PATH_TPL.$theme.$arr['NAMES'][$i].'/Index'.EXT_TPL)) {
        continue;
      }
      $html .= "<option value='".$arr['NAMES'][$i]."' ".
        ($arr['NAMES'][$i] == $selected ? "selected" : "").">".
          $arr['NAMES'][$i]."</option>";
    }
    return $html;
  }

  public function actionHello($Params)
  {
  }

  public function actionConfig($Param)
  {
      $this->Tag('settings', $this->settings);
      $templates = fsFunctions::DirectoryInfo(PATH_TPL.'fsCMS', false, true);
      $templatesFiles = fsFunctions::DirectoryInfo(PATH_TPL.$this->settings->template.'/MPages/', true, false, 'Index', array('php'));
      $theme = fsFunctions::Slash($this->settings->template);
      $this->Tag('templates', $templates['NAMES']);
      $this->Tag('templatesFiles', $templatesFiles['NAMES']);
  }
  
  public function actionDoConfig($Param)
  {
    if ($Param->Exists('call') && $Param->call != '') {
      $this->_Do($Param, $Param->call, 'doConfig');
    } else {
      $cs = new controller_settings();
      $arr = $this->settings->GetStruct();
      foreach($arr as $f) {
        if ($f == 'controller' || !$Param->Exists($f)) {
          continue;
        }
        $cs->Set($this->settings->controller, $f, $Param->$f);
      }
      $this->_Referer();  
    }
    $settingsFile = PATH_ROOT.'settings/Settings.php';
    if($Param->controller = 'AdminPanel' && $Param->Exists('links_suffix') 
      && file_exists($settingsFile)) {
      $s = file_get_contents($settingsFile);
      $s = preg_replace(
        "/'links_suffix'\s+=>\s+array\('ReadOnly'\s+=>\s+true,\s+'Value'\s+=>\s+'[^']*'\)/i", 
        "'links_suffix' => array('ReadOnly' => true, 'Value' => '".$Param->links_suffix."')", $s);
      $s = preg_replace(
        "/'url_404'\s+=>\s+array\('ReadOnly'\s+=>\s+true,\s+'Value'\s+=>\s+'[^']*'\)/i", 
        "'url_404' => array('ReadOnly' => true, 'Value' => 'http://".$_SERVER['SERVER_NAME'].'/404'.$Param->links_suffix."')", $s);
      $s = preg_replace(
        "/'multi_language'\s+=>\s+array\('ReadOnly'\s+=>\s+true,\s+'Value'\s+=>\s+(true|false)\)/i", 
        "'multi_language' => array('ReadOnly' => true, 'Value' => ".$Param->multi_language.")", $s);  
      fsFileWorker::UpdateFile($settingsFile, $s);
      fsHtaccess::Create($Param->links_suffix, $Param->multi_language == 'true');
      
      $this->Redirect('http://'.$_SERVER['SERVER_NAME'].'/'.
        ($Param->multi_language == 'true' ? fsSession::GetInstance('Language').'/' : '').
        'AdminPanel/Config'.$Param->links_suffix
      );
      
      fsFunctions::DeleteFile(PATH_JS.'initFsCMS.js');
    }
    if ($this->Message() == '') {
      $this->Message(T('XMLcms_settings_updated'));
    }
  }
}
?>