<!DOCTYPE html>
<html>
<head>
    <title>OnePageCMS - <?php echo T('XMLcms_panel').($tag->title != '' ? ' - '.$tag->title : ''); ?></title>
    <?php 
    fsFunctions::IncludeFile(PATH_TPL.'shared.php');
    fsInclude::AddJs(array(URL_JS.'fsCMSAdmin/admin.js'));
    fsInclude::AddCss(array(URL_PLUGINS.'jqueryui/jquery-ui.min.css', URL_ATHEME_CSS.'admin.css'));
    echo fsInclude::Generate(array('ico')).fsInclude::GenerateCache(array('css'), 'admin');
    if (file_exists(PATH_ATHEME_JS.$_REQUEST['controller'].'.js')) {
        fsInclude::AttachJs(URL_ATHEME_JS.$_REQUEST['controller'].'.js');
    }
    if (file_exists(PATH_ATHEME_CSS.$_REQUEST['controller'].'.css')) {
        fsInclude::AttachCss(URL_ATHEME_CSS.$_REQUEST['controller'].'.css');
    }
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
        [block-content]<?php /* Method Template */ ?>[endblock-content]
      </div>
      <div class="clr"></div>  
    </div>
    <div class='admin-footer'>
      2011-<?php echo date("Y"); ?> &copy; <a href="http://foolsoft.ru" title="<?php _T('XMLcms_a'); ?>" target="_blank"><?php _T('XMLcms_a'); ?></a> | 
      <a class="fancybox" href="#support" title="<?php _T('XMLcms_support'); ?>"><?php _T('XMLcms_support'); ?></a>
    </div>
    <div id="support" class="support hidden">
      <?php echo $tag->panelSupport; ?>  
    </div>
    <?php 
    echo fsInclude::GenerateCache(array('js'), 'admin_'.fsSession::GetInstance('Language')); 
    fsInclude::AttachJs(array(URL_PLUGINS.'ace/ace.js', URL_PLUGINS.'ckeditor/ckeditor.js', URL_PLUGINS.'jqueryui/jquery-ui.min.js'));
    ?>
</body>
</html>