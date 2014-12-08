<?php
/*
 * fsCMS Administrator panel base class
 */
class AdminPanel extends cmsController
{
    public function CreateView($params = array(), $template = '', $show = false, $adminMode = null) 
    {
        return parent::CreateView($params, $template, $show, is_null($adminMode) ? true : $adminMode);
    }

    /*
    * Get full path for template folder.
    * @param string $folder Needed template folder.
    * @return string Template path. 
    */
    protected function _TemplatePath($folder = '')
    { 
        if ($folder === '') {
          $folder = get_class($this);
        }
        $path = PATH_TPL.CMSSettings::GetInstance('template_admin').'/'.$folder;
        if (!is_dir($path)) {
          $path = parent::_TemplatePath($folder);
        }
        return fsFunctions::Slash($path);
    }
  
    /*
    * Call controller method.
    * @param fsStruct $param Request data.
    * @param string $method Method name for call.
    * @param string $prefix (optional) Prefix for method name. Default <b>empty string</b>.
    * @return void 
    */
    protected function _Do($param, $method, $prefix = '')
    {
        $prefix .= $method;
        try {
          if (!empty($method) && method_exists($this, $prefix)) {
            return $this->$prefix($param);
          } else {
            $this->Message(T('XMLcms_invalid_command'));
          }
        } catch (Exception $e) {
          $this->Message(T('XMLcms_invalid_data').': '.$e);
        }
    }

    /*
    * Set redirect response.
    * @param string $method (optional) Controller method for redirection. If empty redirect to request referer. Default <b>empty string</b>.
    * @return void 
    */
    protected function _SetRedirect($method = '')
    {
        $this->Redirect(!empty($method) ? $this->_My($method) : $this->_Referer(false));
    }
  
    private function _PanelTop()
    {
        return $this->CreateView(array(), $this->_Template('PanelTop', 'AdminPanel'));   
    }
  
    private function _PanelSupport()
    {
        return '<form action="'.fsHtml::Url(URL_ROOT.'AdminPanel/Support').'" method="post" id="cms-support">'.
            $this->CreateView(array(), $this->_Template('PanelSupport', 'AdminPanel')).'</form>';   
    }
  
    private function _Sidebar()
    {
        $menu = MenuGenerator::Get(URL_ROOT, 'admin_menu', 'parent', 'a-menu', 'name', 'name', 'text',  array('order'), '`in_panel` = "1"');
        return $this->CreateView(array('menu' => $menu), $this->_Template('Sidebar', 'AdminPanel'));
    }
    
    /*
    * Get dictionary data.
    * @param fsStruct $param User request.
    * @return void 
    */
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
  
    /*
    * Call block of Edit functions.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actonEdit($param)
    {
        if (!$param->Exists('key') || $param->key == '') {
         $this->Message(T('XMLcms_nokey'));
         return $this->_Referer(); 
        }
        $this->_Do($param, $param->call, 'Edit');
    }
  
    /*
    * Action before main conroller action.
    * @param fsStruct $request User request.
    * @return void 
    */
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
 
    /*
    * Template redactor save action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionTemplateManagerSave($param)
    {
        $pathCms = PATH_TPL.'fsCMS/';
        $pathInclude = PATH_ROOT.'includes/';
        $template = $param->template; 
        $file = $param->file;
        $array = array('/', '.');
        $path = '';

        if($template == '' || $file == '' || in_array($template[0], $array) || in_array($file[0], $array)) {
            return $this->Html(T('XMLcms_error_action'));
        }

        $template = explode('/', $template);  
        if(count($template) == 2) {
          if(in_array($template[1], array('css', 'js'))) { 
              $path = $pathInclude.$template[1].'/'.$file;
          }
        } else {
          $path = $pathCms.$template[0].'/'.$file;
        }

        if($path == '' || !file_exists($path)) {
            return $this->Html(T('XMLcms_error_action'));
        }

        fsFileWorker::UpdateFile($path, $param->text);

        $this->Html(T('XMLcms_updated'));
    }
  
    /*
    * Template redactor action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionTemplateManager($param)
    {
        $pathCms = PATH_TPL.'fsCMS/';
        $pathInclude = PATH_ROOT.'includes/';
        $template = $param->template;

        $templatesCms = fsFunctions::DirectoryInfo($pathCms, false, true);
        $templatesCms = $templatesCms['NAMES'];
        $templatesCms[] = 'includes/css';
        $templatesCms[] = 'includes/js';

        if(count($templatesCms) == 0) {
          return $this->Message(T('XMLcms_error_action'));
        }
        if($template == '' || in_array($template[0], array('/', '.'))) {
          $template = $templatesCms[0]; 
        }

        if($param->file != '') {
          $template = explode('/', $template);  
          $path = '';
          if(count($template) == 2) {
            if(in_array($template[1], array('css', 'js'))) { 
                $path = $pathInclude.$template[1].'/'.$param->file;
            }
          } else {
            $path = $pathCms.$template[0].'/'.$param->file;
          }

          return $this->Html('<textarea name="text" id="template-manager-content" class="hidden"></textarea><div id="template-manager-editor" style="width:98%;height:300px;">'.
            ($path != '' && file_exists($path) ? htmlspecialchars(trim(file_get_contents($path))) : '').'</div>');  
        }

        $files = array();
        $template = explode('/', $template);

        if(count($template) == 2) {
            if(in_array($template[1], array('css', 'js'))) { 
                $filesIncludes = fsFunctions::DirectoryInfo($pathInclude.$template[1], true, false, '', array($template[1]), false);
                foreach($filesIncludes['NAMES'] as $include) {
                    $files[] = $include;  
                }
            }
        } else {      
          $controllers = fsFunctions::DirectoryInfo($pathCms.$template[0], false, true);
          $tplFormat = strlen(EXT_TPL) > 0 && substr(EXT_TPL, 0, 1) == '.' ? substr(EXT_TPL, 1) : EXT_TPL;
          foreach($controllers['NAMES'] as $controller) {
            $path = $pathCms.$template[0].'/'.$controller.'/';
            $templates = fsFunctions::DirectoryInfo($path, true, false, false, array($tplFormat));
            foreach($templates['NAMES'] as $tpl) {
              $files[] = $controller.'/'.$tpl;  
            }
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
  
    /*
    * Clear cache action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionAjaxClearCache($param)
    {
        $this->Html(T('XMLcms_deleted'));
        fsSession::Delete('Language');
        fsCache::Clear();
        fsFunctions::DeleteFile(PATH_JS.'initFsCMS.js');
    }
  
    /*
    * Send message to support.
    * @param fsStruct $param User request.
    * @return void 
    */
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
  
    /*
    * Lock/Unlock site action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionAjaxLockSite($param)
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
        $this->Html(fsFunctions::StringFormat(
            "<a href=\"javascript:fsCMS.Ajax('{0}', 'POST', false, 'a_site_lock', 'a_site_lock', 16);\" title='{1}'>{2}</a>",
            array($this->_My('AjaxLockSite'), $altLock, T($this->Html()))
            ), true);
    } 
  
    /*
    * Default activate action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionActivate($param)
    {
        $this->_SetRedirect($param->referer);
        if ($param->Exists('key')) {
          if ($param->field == '') {
            $param->field = 'active';
          }
          $table_key = $param->table_key;
          $obj = $param->table == '' ?  $this->_table : new fsDBTable($param->table); 
          if ($param->table_key == '') {
            $param->table_key = ($obj->key == '' ?  'id' : $obj->key);
          }
          if ($obj == null) {
            $this->Message(T('XMLcms_bme_activate'));
            return -1;
          } else {
            $key = $param->key;
            $where = "`".$param->table_key."`";
            if(is_array($key)) {
              $c = count($key);
              $where .= ' IN (';
              for($i = 0; $i < $c; ++$i) {
                $where .= "'".$key[$i]."'".($i == $c - 1 ? '' : ', ');  
              }
              $where .= ')';
            } else {
              $where .= " = '".$param->key."'";
            }
            $obj->Update(array($param->field), array('1'))->Where($where)->Execute();
            return 0;
          }
        }
        return -1;
    }
  
    /*
    * Default multiple action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionMultiAction($param)
    {
        $this->_SetRedirect($param->referer);
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
          case 'activate':  
              return $this->actionActivate($param);
          case 'delete': 
              return $this->actionDelete($param);
          default: 
              return 404;
        }
    }
 
    /*
    * Default delete action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionDelete($param)
    {
        $this->_SetRedirect($param->referer);
        if (!$param->Exists('key')) {
          return -1;
        }
        $return = 0;
        if ($param->Exists('confirm')) {
          $obj = !$param->Exists('table') ? $this->_table : new fsDBTable($param->table);
          if ($obj == null) {
            $this->Message(T('XMLcms_bme_delete'));
            $return = 4;
          } else {
            $key = $param->key;
            $where = !$param->Exists('table_key')
                      ? '`'.($obj->key == '' ?  'id' : $obj->key).'`'
                      : "`".$param->table_key."`";
            if(is_array($key)) {
              $c = count($key);
              $where .= ' IN (';
              for($i = 0; $i < $c; ++$i) {
                $where .= "'".$key[$i]."'".($i == $c - 1 ? '' : ', ');  
              }
              $where .= ')';
            } else {
              $where .= " = '".$param->key."'";
            }
            $obj->Delete()->Where($where)->Execute();
            if ($param->referer == '') {
              $this->_SetRedirect('Index');
            }
          }
          $this->Message(T('XMLcms_record_deleted'));
        } else {
          $arr = $param->GetStruct();
          $paramStr = '';
          foreach ($arr as $P) {
            if ($P == 'PHPSESSID' || $P == 'controller' || $P == 'method' ||
                $P == 'includeBody' || $P == 'includeHead') {
                continue;
            } else {
              $temp = $param->$P;
              if(!is_array($temp)) {
                $paramStr .= '&'.$P.'='.$param->$P;
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
  
    /*
    * Default add action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionDoAdd($param)
    { 
        $this->_SetRedirect($param->referer);
        $return = -1;
        if ($param->Exists('call') && $param->call != '') {
          $return = $this->_Do($param, $param->call, 'actionDoAdd');
        } else {
          $obj = !$param->Exists('table') ? $this->_table : new fsDBTable($param->table);
          if ($obj == null) {
            $this->Message(T('XMLcms_bme_add'));
          } else {  
            foreach($obj->columns as $c) {
              if($param->Exists($c)) {
                $obj->$c = $param->$c;
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
  
    /*
    * Default edit action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionDoEdit($param)
    { 
        $this->_SetRedirect($param->referer);
        $return = 0;
        if (!$param->Exists('key')) {
          $this->Message(T('XMLcms_unknow_key'));
          $return = 2;
        } else {
          if ($param->Exists('call') && $param->call != '') {
            $return = $this->_Do($param, $param->call, 'actionDoEdit');
          } else {
            $obj = !$param->Exists('table') ? $this->_table : new fsDBTable($param->table);
            if ($obj == null) {
              $this->Message(T('XMLcms_bme_edit'));
              $return = 1;
            } else {  
              $tk = !$param->Exists('table_key') ? ($obj->key == '' ?  'id' : $obj->key) : $param->table_key;
              $where = '`'.$tk.'` = "'.$param->key.'"';
              $new_values = array();
              $idx = 0;
              $cols_to_edit = array();
              foreach($obj->columns as $c) {
                if ($param->Exists($c) || $obj->GetType($c) == 'enum') {
                  $new_values[$idx] = $param->Exists($c) ? $param->$c : 0;
                  $cols_to_edit[] = $c;
                  if ($c == $tk) {
                    $redirect = str_replace('key='.$param->key, 'key='.$new_values[$idx], $this->Redirect()); 
                    $redirect = str_replace('/key/'.$param->key, '/key/'.$new_values[$idx], $redirect); 
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

    public function actionAjaxTemplateFiles($param)
    {
        $this->_response->empty = true;
        $this->Html($this->_ThemeTemplateFiles($param->theme));
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

    /*
    * Main page.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionHello($param)
    {
    }

    /*
    * Config page.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionConfig($param)
    {
        $this->Tag('settings', $this->settings);
        $templates = fsFunctions::DirectoryInfo(PATH_TPL.'fsCMS', false, true);
        $templatesFiles = fsFunctions::DirectoryInfo(PATH_TPL.$this->settings->template.'/MPages/', true, false, 'Index', array('php'));
        $this->Tag('templates', $templates['NAMES']);
        $this->Tag('templatesFiles', $templatesFiles['NAMES']);
        $start_pages = array();
        $start_pages['group_pages'] = '[group='.T('XMLcms_pages').']';
        $db = new fsDBTableExtension('pages');
        $db->Select()->Order(array('title'))->Execute('', false);
        while($db->Next())
        {
            $start_pages['page/'.$db->result->alt] = T($db->result->title);
        }
        $start_pages['group_pages_end'] = '[/group='.T('XMLcms_pages').']';
        $start_pages['group_categories'] = '[group='.T('XMLcms_text_categories').']';
        $db = new fsDBTableExtension('posts_category');
        $db->Select()->Order(array('name'))->Execute('', false);
        while($db->Next())
        {
            $start_pages['posts/'.$db->result->alt] = T($db->result->name);
        }
        $start_pages['group_categories_end'] = '[/group='.T('XMLcms_text_categories').']';
        $start_pages['group_posts'] = '[group='.T('XMLcms_text_posts').']';
        $db = new fsDBTableExtension('posts');
        $db->Select()->Order(array('title'))->Execute('', false);
        while($db->Next())
        {
            $start_pages['post/'.$db->result->alt] = T($db->result->title);
        }
        $start_pages['group_posts_end'] = '[/group='.T('XMLcms_text_posts').']';
        $this->Tag('start_pages', $start_pages);
    }
  
    /*
    * Default save config action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionDoConfig($param)
    {
        if ($param->Exists('call') && $param->call != '') {
          $this->_Do($param, $param->call, 'doConfig');
        } else {
          $cs = new controller_settings();
          $arr = $this->settings->GetStruct();
          foreach($arr as $f) {
            if ($f == 'controller' || !$param->Exists($f)) {
              continue;
            }
            $cs->Set($this->settings->controller, $f, $param->$f);
          }
          $this->_Referer();  
        }
        $settingsFile = PATH_ROOT.'settings/Settings.php';
        $startPage = $param->start_page_custom != '' ? $param->start_page_custom : $param->start_page; 
        if($param->controller = 'AdminPanel' && $param->Exists('links_suffix') && file_exists($settingsFile)) {
          $s = file_get_contents($settingsFile);
          $s = preg_replace(
            "/'links_suffix'\s+=>\s+array\('ReadOnly'\s+=>\s+true,\s+'Value'\s+=>\s+'[^']*'\)/i", 
            "'links_suffix' => array('ReadOnly' => true, 'Value' => '".$param->links_suffix."')", $s);
          $s = preg_replace(
            "/'start_page'\s+=>\s+array\('ReadOnly'\s+=>\s+true,\s+'Value'\s+=>\s+'[^']*'\)/i", 
            "'start_page' => array('ReadOnly' => true, 'Value' => '".$startPage."')", $s);
          $s = preg_replace(
            "/'url_404'\s+=>\s+array\('ReadOnly'\s+=>\s+true,\s+'Value'\s+=>\s+'[^']*'\)/i", 
            "'url_404' => array('ReadOnly' => true, 'Value' => 'http://".$_SERVER['SERVER_NAME'].'/404'.$param->links_suffix."')", $s);
          $s = preg_replace(
            "/'multi_language'\s+=>\s+array\('ReadOnly'\s+=>\s+true,\s+'Value'\s+=>\s+(true|false)\)/i", 
            "'multi_language' => array('ReadOnly' => true, 'Value' => ".$param->multi_language.")", $s);  
          fsFileWorker::UpdateFile($settingsFile, $s);
          fsHtaccess::Create($param->links_suffix, $param->multi_language == 'true');

          $this->Redirect('http://'.$_SERVER['SERVER_NAME'].'/'.
            ($param->multi_language == 'true' ? fsSession::GetInstance('Language').'/' : '').
            'AdminPanel/Config'.$param->links_suffix
          );

          fsFunctions::DeleteFile(PATH_JS.'initFsCMS.js');
        }
        if ($this->Message() == '') {
          $this->Message(T('XMLcms_settings_updated'));
        }
    }
}