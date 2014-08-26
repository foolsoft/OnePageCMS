<script src="<?php echo URL_JS.'jq.js';?>" type="text/javascript"></script>
<script src="<?php echo URL_JS.'initFsCMS.js?v='.time();?>" type="text/javascript"></script>
<script src="<?php echo URL_PLUGINS.'fancybox/jquery.fancybox.pack.js';?>" type="text/javascript"></script>
<script src="<?php echo URL_JS.'fsCMS.js';?>" type="text/javascript"></script>
<script src="<?php echo URL_JS.'dictionaries/'.fsSession::GetInstance('Language').'.js';?>" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo URL_PLUGINS.'fancybox/jquery.fancybox.css'; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_CSS.'shared.css'; ?>" />
<link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo (file_exists(PATH_THEME_IMG.'favicon.ico') ? URL_THEME_IMG : URL_IMG); ?>favicon.ico" />
<?php
if (file_exists(PATH_JS.$_REQUEST['controller'].'.js')) {
  echo "<script src='".URL_JS.$_REQUEST['controller'].".js' type='text/javascript'></script>";
}
if (file_exists(PATH_CSS.$_REQUEST['controller'].'.css')) {
  echo "<link rel='stylesheet' type='text/css' href='".URL_CSS.$_REQUEST['controller'].".css'>";
}
?>