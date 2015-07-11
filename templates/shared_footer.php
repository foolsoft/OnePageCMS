<?php /* Css, Js, Ico files for global using */
fsInclude::AddCss(array(
    URL_PLUGINS.'fancybox/jquery.fancybox.css'
));
fsInclude::AttachJs(array(
    URL_JS.'jq.js', 
    URL_PLUGINS.'fancybox/jquery.fancybox.pack.js'
));
if (file_exists(PATH_JS.$_REQUEST['controller'].'.js')) {
    fsInclude::AttachJs(URL_JS.$_REQUEST['controller'].'.js');
}
if (file_exists(PATH_CSS.$_REQUEST['controller'].'.css')) {
    fsInclude::AttachCss(URL_CSS.$_REQUEST['controller'].'css');
}