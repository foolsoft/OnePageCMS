<?php /* Css, Js, Ico files for global using */
fsInclude::AttachIco((file_exists(PATH_THEME_IMG.'favicon.ico') ? URL_THEME_IMG : URL_IMG).'favicon.ico');
fsInclude::AddJs(array(
    URL_JS.'dictionaries/'.fsSession::GetInstance('Language').'.js',
    URL_JS.'initFsCMS'.fsSession::GetInstance('Language').'.js',
    URL_JS.'fsCMS.js',
));
fsInclude::AttachJs(array(
    URL_JS.'jq.js',
)); 