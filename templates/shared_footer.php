<?php /* Css, Js, Ico files for global using */
fsInclude::AddCss(array(
    URL_PLUGINS.'fancybox/jquery.fancybox.css'
), true, fsInclude::GetVersion());
fsInclude::AttachJs(array(
    URL_PLUGINS.'fancybox/jquery.fancybox.pack.js'
), true, fsInclude::GetVersion());
if (file_exists(PATH_JS.$_REQUEST['controller'].'.js')) {
    fsInclude::AttachJs(URL_JS.$_REQUEST['controller'].'.js', true, fsInclude::GetVersion());
}
if (file_exists(PATH_CSS.$_REQUEST['controller'].'.css')) {
    fsInclude::AttachCss(URL_CSS.$_REQUEST['controller'].'css', true, fsInclude::GetVersion());
}