<?php
/*
 * Css, Js, Ico files for global using
 */
fsInclude::AddIco((file_exists(PATH_THEME_IMG.'favicon.ico') ? URL_THEME_IMG : URL_IMG).'favicon.ico');
fsInclude::AddCss(array(URL_PLUGINS.'fancybox/jquery.fancybox.css', URL_CSS.'shared.css'));
fsInclude::AddJs(array(URL_JS.'initFsCMS'.fsSession::GetInstance('Language').'.js', URL_JS.'fsCMS.js', URL_JS.'dictionaries/'.fsSession::GetInstance('Language').'.js'));
fsInclude::AttachJs(array(URL_JS.'jq.js', URL_PLUGINS.'fancybox/jquery.fancybox.pack.js'));
if (file_exists(PATH_JS.$_REQUEST['controller'].'.js')) {
    fsInclude::AttachJs(URL_JS.$_REQUEST['controller'].'.js');
}
if (file_exists(PATH_CSS.$_REQUEST['controller'].'.css')) {
    fsInclude::AttachCss(URL_CSS.$_REQUEST['controller'].'css');
}