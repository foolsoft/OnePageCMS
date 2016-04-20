<html>
<head>
    <meta charset="utf-8">
    <title><?php _T('XMLcms_text_reg_thanks'); ?></title>
</head>
<body>
    <?php _T('XMLcms_text_dear'); ?>, <b><?php echo $data['login']; ?></b>!<br />
    <?php _T('XMLcms_text_mail_register'); ?>
    <br /><br />
    <?php _T('XMLcms_text_login'); ?>: <?php echo $data['login']; ?><br />
    <?php _T('XMLcms_text_password'); ?>: <?php echo $data['password']; ?>
    <br /><br />
    <hr />
    <div style="font-size:10px"><?php _T('XMLcms_text_team'); ?> <?php echo $_SERVER['SERVER_NAME']; ?></div>
</body>
</html>