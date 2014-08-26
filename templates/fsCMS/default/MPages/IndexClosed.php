<!DOCTYPE html>
<html>
<head>
	<title><?php echo $page['title']; ?> - <?php echo $tag->constants->title; ?></title>
	<meta name="keywords" content="<?php echo $page['meta_keywords']; ?>">
  <meta name="description" content="<?php echo $page['meta_description']; ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo URL_THEME_CSS.'styles.css'; ?>">
  <?php
  echo '<link rel="icon" type="image/vnd.microsoft.icon" href="'.(file_exists(PATH_THEME_IMG.'favicon.ico') ? URL_THEME_IMG : URL_IMG).'favicon.ico">'; 
  ?>
</head>
<body>
  <div class='div-body'>
    <div class='div-content'>
      [block-content]
        <?php echo $page['html']; ?>
      [endblock-content]
    </div>
  </div>
</body>
</html>