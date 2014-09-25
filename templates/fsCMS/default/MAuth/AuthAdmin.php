<!DOCTYPE html>
<html>
<head>
	<title>OnePageCMS - <?php _T('XMLcms_panel').' '.($tag->title != '' ? '- '.$tag->title : ''); ?></title>
  <?php fsFunctions::IncludeFile(PATH_TPL.'shared.php'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo URL_ATHEME_CSS.'admin.css'; ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo URL_THEME_CSS.'MAuth.css'; ?>">
</head>
<body>
  <?php echo $tag->message; ?>
  <div class='authadmin-div-form'>
      <form action="<?php echo fsHtml::Url($myLink.'DoAuthAdmin'); ?>" method="post">
      		<center>
            <table>
        			<tr>
        				<td align="right"><b><?php _T('XMLcms_text_login'); ?>:</b></td>
        				<td align="left"><input type="text" name="login" /></td>
        			</tr>
        			<tr>
        				<td align="right"><b><?php _T('XMLcms_text_password'); ?>:</b></td>
        				<td align="left"><input type="password" name="password" /></td>
        			</tr>
        			<tr>
        				<td align="right" colspan="2">
        					<input type="submit" value="<?php _T('XMLcms_text_enter'); ?>" class='fsCMS-btn authadmin-btn-login' id='authadmin-btn-login' />
        				</td>
        			</tr>
        		</table>
      		</center>
      </form>
  </div>
  <div class='authadmin-div-footer'>
      2011-<?php echo date("Y"); ?> &copy; <a href='http://foolsoft.ru' title='<?php _T('XMLcms_a'); ?>' target='_blank'><?php _T('XMLcms_a'); ?></a>
  </div>
</body>
</html>