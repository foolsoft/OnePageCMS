<?php 
/*
 * Initialize fsCMS plugin
 */
define('AUTH', fsSession::Exists('AUTH'));
define('GUEST_USER_ID', 0);
define('GUEST_USER_TYPE', 0);
define('ADMIN_USER_TYPE', 1);
define('USER_TYPE', 2);
define('ALL_TYPES', 0);
define('AUTH_ADMIN', AUTH && fsSession::GetArrInstance('AUTH', 'type') == ADMIN_USER_TYPE);
define('FILE_LOCK', PATH_ROOT.'lock.fsCMS');
define('FILE_OPTIONAL_LIBRARY',  'optional.fsCMS');
define('URL_DTHEME_CSS',  URL_CSS.'fsCMS/default/');
define('URL_DTHEME_JS',   URL_JS.'fsCMS/default/');
define('URL_DTHEME_IMG',  URL_IMG.'fsCMS/default/');
define('PATH_DTHEME_CSS',  PATH_CSS.'fsCMS/default/');
define('PATH_DTHEME_JS',   PATH_JS.'fsCMS/default/');
define('PATH_DTHEME_IMG',  PATH_IMG.'fsCMS/default/');
define('EMAIL_SUPPORT',  'support@onepagecms.net');

if (!file_exists(PATH_ROOT.'settings/dbSettings.php') || !file_exists(PATH_ROOT.'settings/Settings.php')) {
  fsFunctions::Redirect(URL_ROOT.'setup.php');
}

if(!file_exists('.htaccess')) {
  fsHtaccess::Create();
  header('Location: /');
  exit;
}

if(!AUTH && !fsSession::Exists('GUEST')) {
    $types_users = new types_users();
    $guestInfo = array('id' => GUEST_USER_ID, 'login' => T('XMLcms_guest'), 'type' => GUEST_USER_TYPE, 'active' => 1);
    $type = $types_users->Get(GUEST_USER_TYPE);
    foreach($type as $field => $value) {
        if($field != 'id') {
            $type['type_'.$field] = $value;
        }
    }
    fsSession::Create('GUEST', $guestInfo);
    unset($types_users);
    unset($type);
    unset($guestInfo);
}

fsFunctions::CreateDirectory(PATH_ROOT.'temp');
fsFunctions::CreateDirectory(PATH_ROOT.'uploads');

fsFunctions::IncludeFiles(array(
    PATH_PLUGINS.'fsCMS/CMSSettings.php',
    PATH_PLUGINS.'fsCMS/fsCMS.php',
    PATH_PLUGINS.'fsCMS/fsCMSAuth.php',
    PATH_PLUGINS.'fsCMS/fsAdminPanel.php'
 ));
               
$includeFilesPrefix = array('!Admin'); 
if(IS_ADMIN_CONTROLLER) {
    $includeFilesPrefix[0] = substr($includeFilesPrefix[0], 1);
    $includeFilesPrefix[] = 'Functions';
}
fsFunctions::IncludeFolder(PATH_ROOT.'controllers/fsCMS/', $includeFilesPrefix, array('php'), array('init.php'));
unset($includeFilesPrefix);

if (!file_exists(PATH_JS.'initFsCMS'.fsSession::GetInstance('Language').'.js')) {
  $fw = new fsFileWorker(PATH_JS.'initFsCMS'.fsSession::GetInstance('Language').'.js', 'w+');
  $fw->WriteLine('function T(name){return cmsDictionary[name]==undefined?name:cmsDictionary[name];}');
  $fw->Write('var URL_ROOT="{0}",', array(URL_ROOT));
  $fw->Write('URL_IMG="{0}",', array(URL_IMG));
  $fw->Write('URL_JS="{0}",', array(URL_JS));
  $fw->Write('URL_CSS="{0}",', array(URL_CSS));
  $fw->Write('URL_SUFFIX="{0}";', array(fsConfig::GetInstance('links_suffix')));
  $fw->Close();
}  

if (file_exists(FILE_LOCK) 
  && !AUTH_ADMIN 
  && $_SERVER['REQUEST_URI'] != fsHtml::Url((fsConfig::GetInstance('multi_language') ? '/'.fsSession::GetInstance('Language') : '').'/page/closed') 
  && ($_REQUEST['controller'] != 'MAuth' || ($_REQUEST['method'] != 'AuthAdmin' && $_REQUEST['method'] != 'DoAuthAdmin'))) {
    fsFunctions::Redirect(fsHtml::Url(URL_ROOT.'page/closed'));
}

if (AUTH_ADMIN && !IS_ADMIN_CONTROLLER) {
  $HL = T('XMLcms_hide'); $PA = T('XMLcms_panel');
  $_REQUEST['includeBody'] .= "<div class='admin_panel_top' id='admin-panel-top' style='border-bottom:2px solid #000;margin:0px;position:fixed;top:0px;background:rgba(0,0,0,0.5);width:100%;height:22px;left:0px;z-index:99999999999;'><a style='float:left;text-decoration:none;color:#EEE;font-family:Tahoma;padding-left:15px;' onmouseout=\"this.style.color='#EEE';\" onmouseover=\"this.style.color='#FFFF00';\" href='".fsHtml::Url(URL_ROOT."AdminPanel/Hello")."' title='".$PA."'>".$PA."</a><a href='#' onclick=\"$('#admin-panel-top').slideUp('slow');\" style='float:right;text-decoration:none;color:#EEE;font-family:Tahoma;margin-right:10px;' onmouseout=\"this.style.color='#EEE';\" onmouseover=\"this.style.color='#FFFF00';\" title='".$HL."'>X</a></div>";  
  $_REQUEST['includeHead'] .= '<meta name="generator" content="OnePageCMS" />';
  unset($HL); unset($PA);
}