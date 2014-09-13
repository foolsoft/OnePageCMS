<?php
header('Content-type: text/html; charset=utf-8');
session_start();
$_REQUEST['includeBody'] = '';
$_REQUEST['includeHead'] = ''; 
define('PATH_ROOT',       dirname(__FILE__).'/../');
define('PATH_PLUGINS',    PATH_ROOT.'plugins/'); 
define('PATH_CACHE',      PATH_ROOT.'cache/');
define('PATH_CACHE_DB',   PATH_CACHE.'db/');
define('PATH_LANG',       PATH_ROOT.'languages/');
define('EXT_TPL',         '.php');
define('SYSTEM_LANGUAGE', 'ru');
define('PATH_TPL', PATH_ROOT.'templates/');
define('PATH_CSS', PATH_ROOT.'includes/css/');
define('PATH_JS',  PATH_ROOT.'includes/js/');
define('PATH_IMG', PATH_ROOT.'includes/img/');
include PATH_ROOT.'kernel/fsFunctions.php';
fsFunctions::CreateDirectory(PATH_CACHE);
fsFunctions::RestrucGlobalFILES();
fsFunctions::IncludeFolder(PATH_ROOT.'kernel/interfaces/');
fsFunctions::IncludeFiles(array(
    PATH_ROOT.'settings/Settings.php',  
    PATH_ROOT.'settings/dbSettings.php',
    PATH_ROOT.'kernel/fsStruct.php',
    PATH_ROOT.'kernel/fsSession.php',
    PATH_ROOT.'kernel/fsConfig.php',
    PATH_ROOT.'kernel/fsLanguage.php'
));
fsLanguage::GetInstance();
define('URL_ROOT',    'http://'.$_SERVER['SERVER_NAME'].'/'.(fsConfig::GetInstance('multi_language') === true ? fsSession::GetInstance('Language').'/' : ''));
define('URL_PLUGINS', 'http://'.$_SERVER['SERVER_NAME'].'/plugins/');
define('URL_TPL',     'http://'.$_SERVER['SERVER_NAME'].'/templates/');
define('URL_CSS',     'http://'.$_SERVER['SERVER_NAME'].'/includes/css/');
define('URL_JS',      'http://'.$_SERVER['SERVER_NAME'].'/includes/js/');
define('URL_IMG',     'http://'.$_SERVER['SERVER_NAME'].'/includes/img/');
fsFunctions::IncludeFiles(array(
    PATH_ROOT.'kernel/fsFileWorker.php',
    PATH_ROOT.'kernel/fsDBconnection.php',
    PATH_ROOT.'kernel/fsValidator.php',
    PATH_ROOT.'kernel/fsDBTable.php',
    PATH_ROOT.'kernel/fsDBTableExtension.php',
    PATH_ROOT.'kernel/fsHtml.php',
    PATH_ROOT.'kernel/fsHtaccess.php',
    PATH_ROOT.'kernel/Response.php',
    PATH_ROOT.'kernel/View.php',
    PATH_ROOT.'kernel/fsController.php',
    PATH_ROOT.'kernel/fsKernel.php',
    PATH_ROOT.'kernel/fsRoute.php',
    PATH_ROOT.'kernel/fsCache.php',
    PATH_ROOT.'kernel/fsInclude.php',
));
fsRoute::Request();
fsFunctions::IncludeFile(PATH_PLUGINS.'init.php');
fsFunctions::IncludeFile(PATH_ROOT.'controllers/init.php');
fsFunctions::IncludeFolder(PATH_CACHE, false, array('php'));
fsFunctions::IncludeFolder(PATH_CACHE_DB, false, array('php'));
fsFunctions::IncludeFolder(PATH_ROOT.'models/', false, array('php'));
?>