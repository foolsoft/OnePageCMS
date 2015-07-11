<?php
/*
 * Initialize fsKernel 
 */
header('Content-type: text/html; charset=utf-8');
session_set_cookie_params(null, '/', null, null, true); // httponly
session_start();
$_REQUEST['includeBody'] = $_REQUEST['includeHead'] = '';
define('PATH_ROOT',       dirname(dirname(__FILE__)).'/');
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
include PATH_ROOT.'kernel/fsGlobals.php';
include PATH_ROOT.'kernel/fsFunctions.php';
fsFunctions::CreateDirectory(PATH_CACHE);
fsFunctions::RestrucGlobalFILES();
fsFunctions::IncludeFolder(PATH_ROOT.'kernel/interfaces/');
fsFunctions::IncludeFiles(array(
    PATH_ROOT.'settings/Settings.php',  
    PATH_ROOT.'settings/dbSettings.php',
    PATH_ROOT.'kernel/fsStruct.php',
    PATH_ROOT.'kernel/fsFileWorker.php',
    PATH_ROOT.'kernel/fsDBconnection.php',
    PATH_ROOT.'kernel/fsValidator.php',
    PATH_ROOT.'kernel/fsDBTable.php',
    PATH_ROOT.'kernel/fsDBTableExtension.php',
    PATH_ROOT.'kernel/fsSession.php',
    PATH_ROOT.'kernel/fsConfig.php',
    PATH_ROOT.'kernel/fsLanguage.php'
));
fsLanguage::GetInstance();
define('URL_ROOT_CLEAR', 'http://'.$_SERVER['SERVER_NAME'].'/');
define('URL_ROOT',    URL_ROOT_CLEAR.(fsConfig::GetInstance('multi_language') === true ? fsSession::GetInstance('Language').'/' : ''));
define('URL_PLUGINS', URL_ROOT_CLEAR.'plugins/');
define('URL_CACHE',   URL_ROOT_CLEAR.'cache/');
define('URL_TPL',     URL_ROOT_CLEAR.'templates/');
define('URL_CSS',     URL_ROOT_CLEAR.'includes/css/');
define('URL_JS',      URL_ROOT_CLEAR.'includes/js/');
define('URL_IMG',     URL_ROOT_CLEAR.'includes/img/');
fsFunctions::IncludeFiles(array(
    PATH_ROOT.'kernel/fsHtml.php',
    PATH_ROOT.'kernel/fsHtaccess.php',
    PATH_ROOT.'kernel/Response.php',
    PATH_ROOT.'kernel/View.php',
    PATH_ROOT.'kernel/fsController.php',
    PATH_ROOT.'kernel/fsKernel.php',
    PATH_ROOT.'kernel/fsRoute.php',
    PATH_ROOT.'kernel/fsCache.php',
    PATH_ROOT.'kernel/fsInclude.php',
    PATH_ROOT.'kernel/fsCaptcha.php',
    PATH_ROOT.'kernel/fsPaginator.php',
    PATH_ROOT.'kernel/fsMenuGenerator.php',
));
fsRoute::Request();
define('IS_ADMIN_CONTROLLER', isset($_REQUEST['controller']) && strpos($_REQUEST['controller'], 'Admin') === 0);
fsFunctions::IncludeFolder(PATH_ROOT.'models', array(), array('php'));
fsFunctions::IncludeFile(PATH_PLUGINS.'init.php');
fsFunctions::IncludeFile(PATH_ROOT.'controllers/init.php');
fsFunctions::IncludeFolder(PATH_CACHE, array(), array('php'));
fsFunctions::IncludeFolder(PATH_CACHE_DB, array(), array('php'));
