<?php
/*
 * fsCMS Administrator panel base class
 */
class AdminPanel extends cmsController
{
    private $_updateFileName = 'release.zip';
    private $_updateFileFlag = 'update.ts';
    
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
        $menu = fsMenuGenerator::Get(URL_ROOT, 'admin_menu', 'parent', 'a-menu', 'name', 'name', 'text',  array('position'), '`in_panel` = "1"');
        return $this->CreateView(array('menu' => $menu), $this->_Template('Sidebar', 'AdminPanel'));
    }
    
    public function actionUpdate($param)
    {
        if(!file_exists($this->_updateFileName)) {
            //return $this->Redirect($this->_My('Hello'));
        }  
        $this->Tag('linkDownloadBackUp', fsFunctions::StringFormat('<a href="{0}" title="{1}">{1}</a>', array($this->_My('DownloadBackUp'), T('XMLcms_savebackup'))));
    }
    
    public function actionDoUpdate($param)
    {
        $this->Redirect($this->_My('Hello'));
        if(!file_exists($this->_updateFileFlag) || !file_exists($this->_updateFileName)) {
            return;
        }
        $nextCheck = explode(',', file_get_contents($this->_updateFileFlag));
        $zip = new ZipArchive();
        if ($zip->open($this->_updateFileName)) {
          $zip->extractTo(PATH_ROOT);
          $zip->close();
        }
        if(file_exists(PATH_ROOT.'update.php')) {
            $db = new fsDBconnection();
            include PATH_ROOT.'update.php';
            if(isset($history[$nextCheck[1]])) {
                foreach($history[$nextCheck[1]] as $query) {
                    $db->Query($query);
                }
            }
            $db->Close();
            fsFunctions::DeleteFile(PATH_ROOT.'update.php');
            fsFunctions::DeleteFile($this->_updateFileFlag);
            fsFunctions::DeleteFile($this->_updateFileName);
        }
        fsCache::Clear();
        $this->Message(T('XMLcms_auto_updated'));
    }
    
    public function actionDownloadBackUp()
    {
        $fileName = PATH_ROOT.'temp/backup_'.date('dmY').'.zip';
        $createZip = new createDirZip();
        if(!file_exists($fileName)) {
          $createZip->getFilesFromFolder(PATH_ROOT, '');
          $fd = fopen($fileName, 'wb');
          $out = fwrite($fd, $createZip->getZippedfile());
          fclose($fd);
        }
        $createZip->forceDownload($fileName);         
    }
    
    private function _CheckUpdate()
    {
        $url = 'http://release.onepagecms.net';
        if(!file_exists($this->_updateFileFlag)) {
            file_put_contents($this->_updateFileFlag, '0,'.$this->settings->version);
        }
        $nextCheck = explode(',', file_get_contents($this->_updateFileFlag));
        $time = time(); 
        if($nextCheck[0] < $time) {
            $nextCheck[0] = $time + (2 * 60 * 60);
            $response = fsFunctions::RequestGet($url);
            if($json = json_decode($response, true)) {
                file_put_contents($this->_updateFileFlag, $nextCheck[0].','.$json['version']);
                if($json['version'] != $this->settings->version && '' != $json['link']) {
                    file_put_contents($this->_updateFileName, fopen($json['link'], 'r'));
                    $nextCheck[1] = $json['version']; 
                }
            }
        }
        $template = $nextCheck[1] != $this->settings->version
            ? '<a href="{0}" title="{1}">{1}</a>'
            : '{1}';
        $this->Tag('cmsUpdateLink', fsFunctions::StringFormat($template, array(
            $this->_My('Update'),
            $nextCheck[1] != $this->settings->version ? fsFunctions::StringFormat(T('XMLcms_text_doupdate'), array($nextCheck[1])) : T('XMLcms_text_uptodate')
        )));
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

        $this->_updateFileName = PATH_ROOT.'temp/'.$this->_updateFileName;
        $this->_updateFileFlag = PATH_ROOT.'temp/'.$this->_updateFileFlag;
        $this->_CheckUpdate();

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
                $filesIncludes = fsFunctions::DirectoryInfo($pathInclude.$template[1], true, false, array(), array($template[1]), false);
                foreach($filesIncludes['NAMES'] as $include) {
                    $files[] = $include;  
                }
            }
        } else {      
          $controllers = fsFunctions::DirectoryInfo($pathCms.$template[0], false, true);
          $tplFormat = strlen(EXT_TPL) > 0 && substr(EXT_TPL, 0, 1) == '.' ? substr(EXT_TPL, 1) : EXT_TPL;
          foreach($controllers['NAMES'] as $controller) {
            $path = $pathCms.$template[0].'/'.$controller.'/';
            $templates = fsFunctions::DirectoryInfo($path, true, false, array(), array($tplFormat));
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
        fsSession::Delete('LanguageId');
        fsFunctions::DeleteDirectory(PATH_ROOT.'temp');
        $files = fsFunctions::DirectoryInfo(PATH_JS, true, false, array('initFsCMS'), array('js'));
        foreach($files['NAMES'] as $file) {
            fsFunctions::DeleteFile(PATH_JS.$file);
        }
        fsCache::Clear();
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
    * @return boolean Result of action 
    */
    public function actionActivate($param)
    {
        if($param->field == '') {
            $param->field = 'active';
        }
        if($param->value == '') {
            $param->value = '1';
        }
        return $this->actionUpdateField($param);
    }
    
    /*
    * Default deactivate action.
    * @param fsStruct $param User request.
    * @return boolean Result of action
    */
    public function actionDeActivate($param)
    {
        if($param->value == '') {
            $param->value = '0';
        }
        return $this->actionActivate($param);
    }
    
    /*
    * Default table one field change action.
    * @param fsStruct $param User request.
    * @return boolean Result of action 
    */
    public function actionUpdateField($param)
    {
        $this->_SetRedirect($param->referer);
        if ($param->key != '' && $param->field != '') {
          $table_key = $param->table_key;
          $obj = $param->table == '' ?  $this->_table : new fsDBTable($param->table); 
          if ($param->table_key == '') {
            $param->table_key = ($obj->key == '' ?  'id' : $obj->key);
          }
          if ($obj == null) {
            $this->Message(T('XMLcms_bme_activate'));
            return false;
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
            return $obj->Update(array($param->field), array($param->value))->Where($where)->Execute();
          }
        }
        return false;
    }
  
    /*
    * Default multiple action.
    * @param fsStruct $param User request.
    * @return boolean Result of action 
    */
    public function actionMultiAction($param)
    {
        $this->_SetRedirect($param->referer);
        if (!$param->Exists('type') || !$param->Exists('keys') || !is_array($param->keys)) {
          return false;
        }
        $param->key = $param->keys;
        $param->Delete('keys');
        switch($param->type) {
          case 'deactivate':  
              return $this->actionDeActivate($param);
          case 'activate':  
              return $this->actionActivate($param);
          case 'delete': 
              return $this->actionDelete($param);
          default: 
              return false;
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
    * @return integer Last inserted id. 
    */
    public function actionDoAdd($param)
    { 
        $this->_SetRedirect($param->referer);
        $return = -1;
        if ($param->Exists('call') && $param->call != '') {
          $call = $param->call;
          $param->Delete('call');
          $return = $this->_Do($param, $call, 'actionDoAdd');
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
    * @return integer Action status 
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
            $call = $param->call;
            $param->Delete('call');
            $return = $this->_Do($param, $call, 'actionDoEdit');
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

    /*
    * Get html options list of templates.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionAjaxTemplateFiles($param)
    {
        $this->_response->empty = true;
        $this->Html($this->_ThemeTemplateFiles($param->theme));
    }
    
    protected function _ThemeTemplateFiles($theme, $selected = false)
    {
        $theme = fsFunctions::Slash($theme);
        $arr = fsFunctions::DirectoryInfo(PATH_TPL.$theme.'MPages/', true, false, array('Index'), array(EXT_TPL));
        if ($selected === false) {
          $selected = $this->settings->main_template;
        } 
        $html = '';
        for ($i = 0; $i < $arr['LENGTH']; ++$i) {
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
        $templatesFiles = fsFunctions::DirectoryInfo(PATH_TPL.$this->settings->template.'/MPages/', true, false, array('Index'), array(EXT_TPL));
        $this->Tag('templates', $templates['NAMES']);
        $this->Tag('templatesFiles', $templatesFiles['NAMES']);
        $textPages = T('XMLcms_pages');
        $textCategories = T('XMLcms_text_categories');
        $textPosts = T('XMLcms_text_posts');
        $start_pages = array();
        $start_pages['group_pages'] = '[group='.$textPages.']';
        $db = new pages();
        $db->GetAllPages(fsSession::GetInstance('LanguageId'));
        while($db->Next())
        {
            $start_pages['page/'.$db->result->mysqlRow['alt']] = $db->result->mysqlRow['title'];
        }
        $start_pages['group_pages_end'] = '[/group='.$textPages.']';
        $start_pages['group_categories'] = '[group='.$textCategories.']';
        $db = new posts_category();
        $db->GetAllCategories(fsSession::GetInstance('LanguageId'));
        while($db->Next())
        {
            $start_pages['posts/'.$db->result->mysqlRow['alt']] = $db->result->mysqlRow['title'];
        }
        $start_pages['group_categories_end'] = '[/group='.$textCategories.']';
        $start_pages['group_posts'] = '[group='.$textPosts.']';
        $db = new posts();
        $db->GetAllPosts(fsSession::GetInstance('LanguageId'));
        while($db->Next())
        {
            $start_pages['post/'.$db->result->mysqlRow['alt']] = $db->result->mysqlRow['title'];
        }
        $start_pages['group_posts_end'] = '[/group='.$textPosts.']';
        $this->Tag('start_pages', $start_pages);
        
        $folders = fsFunctions::DirectoryInfo(PATH_PLUGINS, false, true);
        $libs = array();
        foreach ($folders['NAMES'] as $folder) { 
            if(file_exists(PATH_PLUGINS.$folder.'/'.FILE_OPTIONAL_LIBRARY)) {
                $libs[$folder] = file_exists(PATH_PLUGINS.$folder.'/init.php') ? 1 : 0;
            }
        }
        $this->Tag('libs', $libs);
    }
  
    /*
    * Default save config action.
    * @param fsStruct $param User request.
    * @return void 
    */
    public function actionDoConfig($param)
    {
        if ($param->Exists('call') && $param->call != '') {
          $call = $param->call;
          $param->Delete('call');
          $this->_Do($param, $call, 'actionDoConfig');
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
        
        if($param->controller = 'AdminPanel') {
          if($param->Exists('libs') && is_array($param->libs)) {
            foreach($param->libs as $lib => $shoudBeActive) {
              $path = PATH_PLUGINS.$lib.'/';
              if(file_exists($path.'init.php') && $shoudBeActive == '0') {
                  rename($path.'init.php', $path.'_init.php');
              } else if(file_exists($path.'_init.php') && $shoudBeActive == '1') {
                  rename($path.'_init.php', $path.'init.php');
              }
            }
          }
          if($param->Exists('links_suffix') && file_exists($settingsFile)) {
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
  
            $this->Redirect(PROTOCOL.'://'.$_SERVER['SERVER_NAME'].'/'.
              ($param->multi_language == 'true' ? fsSession::GetInstance('Language').'/' : '').
              'AdminPanel/Config'.$param->links_suffix
            );
            $scripts = fsFunctions::DirectoryInfo(PATH_JS, true, false, array('initFsCMS'), array('js'), true);
            foreach($scripts['NAMES'] as $name) {
                fsFunctions::DeleteFile(PATH_JS.$name);
            }
          }
        }
        if ($this->Message() == '') {
          $this->Message(T('XMLcms_settings_updated'));
        }
    }
}