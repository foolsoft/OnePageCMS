<!DOCTYPE html>
<html>
<head>
	<title>OnePageCMS - <?php echo T('XMLcms_panel').($tag->title != '' ? ' - '.$tag->title : ''); ?></title>
  <?php include PATH_TPL.'shared.php'; ?>
  <link rel="stylesheet" type="text/css" href="<?php echo URL_CSS.'jqui/jqui-datepicker.css'; ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo URL_ATHEME_CSS.'admin.css'; ?>">
  <script src="<?php echo URL_PLUGINS."ace/ace.js";?>" type="text/javascript"></script>
  <script src="<?php echo URL_PLUGINS."ckeditor/ckeditor.js";?>" type="text/javascript"></script>
  <script src="<?php echo URL_JS.'jqui/jqui-datepicker.js';?>" type="text/javascript"></script>
  <script src="<?php echo URL_JS.'fsCMSAdmin/admin.js';?>" type="text/javascript"></script>
  <?php
  if (file_exists(PATH_ATHEME_JS.$_REQUEST['controller'].'.js')) {
    echo "<script src='".URL_ATHEME_JS.$_REQUEST['controller'].".js' type='text/javascript'></script>";
  }
  if (file_exists(PATH_ATHEME_CSS.$_REQUEST['controller'].'.css')) {
    echo "<link rel='stylesheet' type='text/css' href='".URL_ATHEME_CSS.$_REQUEST['controller'].".css'>";
  }
  ?>
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
      [block-content]<!-- Method Template -->[endblock-content]
    </div>
    <div class="clr"></div>  
  </div>
  <div class='admin-footer'>
    2011-<?php echo date("Y"); ?> (c) <a href="http://foolsoft.ru" title="<?php _T('XMLcms_a'); ?>" target="_blank"><?php _T('XMLcms_a'); ?></a> | 
    <a class="fancybox" href="#support" title="<?php _T('XMLcms_support'); ?>"><?php _T('XMLcms_support'); ?></a>
  </div>
  <div id="support" class="support hidden">
    <?php echo $tag->panelSupport; ?>  
  </div>
</body>
</html>