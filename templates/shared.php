<?php
fsInclude::AddCss(array(URL_PLUGINS.'fancybox/jquery.fancybox.css', URL_CSS.'shared.css'));
fsInclude::AddIco((file_exists(PATH_THEME_IMG.'favicon.ico') ? URL_THEME_IMG : URL_IMG).'favicon.ico');
fsInclude::AddJs(array(URL_JS.'jq.js', URL_JS.'initFsCMS.js?v='.time(), URL_PLUGINS.'fancybox/jquery.fancybox.pack.js', URL_JS.'fsCMS.js', URL_JS.'dictionaries/'.fsSession::GetInstance('Language').'.js'));
if (file_exists(PATH_JS.$_REQUEST['controller'].'.js')) {
    fsInclude::AddJs(URL_JS.$_REQUEST['controller'].'.js');
}
if (file_exists(PATH_CSS.$_REQUEST['controller'].'.css')) {
    fsInclude::AddCss(URL_CSS.$_REQUEST['controller'].'css');
}
?>