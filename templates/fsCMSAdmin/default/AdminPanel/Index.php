<?php
fsInclude::AddCss(array(
    URL_PLUGINS.'jqueryui/jquery-ui.min.css', 
    URL_ATHEME_CSS.'admin.css'
));
?>
<!DOCTYPE html>
<html language="<?php echo fsSession::GetInstance('Language'); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo ($tag->title != '' ? $tag->title.' - ' : '').T('XMLcms_panel'); ?> - OnePageCMS</title>
    <?php 
    fsFunctions::IncludeFile(PATH_TPL.'shared_header.php');
    fsInclude::AddJs(array(URL_JS.'fsCMSAdmin/admin.js'));
    ?>
    [block-head]<?php /* ACCESS TO <HEAD> FOR CHILD TEMPLATES */ ?>[endblock-head]
</head>
<body>
    <div class='admin-top'>
      <?php echo $tag->panelTop; ?>
    </div>
    <div>
      <div class='admin-left'>
        <?php echo $tag->sidebar; ?>
      </div>
      <div class='admin-content'>
        <?php echo $tag->message; ?>
        [block-content]<?php /* Method template */ ?>[endblock-content]
      </div>
      <div class="clr"></div>  
    </div>
    <div class="admin-footer">
      2011-<?php echo date('Y'); ?> &copy; <a href="http://foolsoft.ru" title="<?php _T('XMLcms_a'); ?>" target="_blank"><?php _T('XMLcms_a'); ?></a> | 
      <a class="fancybox" href="#support" title="<?php _T('XMLcms_support'); ?>"><?php _T('XMLcms_support'); ?></a>
    </div>
    <div id="support" class="support hidden">
      <?php echo $tag->panelSupport; ?>  
    </div>
    <?php 
    fsFunctions::IncludeFile(PATH_TPL.'shared_footer.php');
    echo fsInclude::GenerateCache(array('js'), 'admin_'.fsSession::GetInstance('Language')); 
    echo fsInclude::GenerateCache(array('css'), 'admin_'.fsSession::GetInstance('Language'));
    fsInclude::AttachJs(array(
        URL_PLUGINS.'jqueryui/jquery-ui.min.js'
    ));
    if (file_exists(PATH_ATHEME_JS.$_REQUEST['controller'].'.js')) {
        fsInclude::AttachJs(URL_ATHEME_JS.$_REQUEST['controller'].'.js');
    }
    if (file_exists(PATH_ATHEME_CSS.$_REQUEST['controller'].'.css')) {
        fsInclude::AttachCss(URL_ATHEME_CSS.$_REQUEST['controller'].'.css');
    }
    ?>
    [block-footer]<?php /* ACCESS TO FOR CHILD TEMPLATES */ ?>[endblock-footer]
</body>
</html>