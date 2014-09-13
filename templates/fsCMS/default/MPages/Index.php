<!DOCTYPE html>
<html>
<head>
  <title><?php echo isset($page['title']) ? $page['title'] : $tag->title; ?> - <?php echo $tag->constants->title; ?></title>
  <meta name="keywords" content="<?php echo isset($page['meta_keywords']) ? $page['meta_keywords'] : $tag->meta_keywords; ?>">
  <meta name="description" content="<?php echo isset($page['meta_description']) ? $page['meta_description'] : $tag->meta_description; ?>">
  <link href="http://fonts.googleapis.com/css?family=Arvo:400,700" rel="stylesheet" type="text/css" />
  <?php fsFunctions::IncludeFile(PATH_TPL.'shared.php'); ?>
  [block-head]<!-- ACCESS TO <HEAD> FOR CHILD TEMPLATES -->[endblock-head]
  <?php
  fsInclude::AddJs(URL_THEME_JS.'scripts.js');
  fsInclude::AddCss(URL_THEME_CSS.'styles.css');
  if (file_exists(PATH_THEME_JS.$_REQUEST['controller'].'.js')) {
    fsInclude::AddJs(URL_THEME_JS.$_REQUEST['controller'].'.js');
  } else if (file_exists(PATH_DTHEME_JS.$_REQUEST['controller'].'.js')) {
    fsInclude::AddJs(URL_DTHEME_JS.$_REQUEST['controller'].'.js');
  } 
  if (file_exists(PATH_THEME_CSS.$_REQUEST['controller'].'.css')) {
    fsInclude::AddCss(URL_THEME_CSS.$_REQUEST['controller'].'.css');
  } else if (file_exists(PATH_DTHEME_CSS.$_REQUEST['controller'].'.css')) {
    fsInclude::AddCss(URL_DTHEME_CSS.$_REQUEST['controller'].'.css');
  } 
  echo fsInclude::Generate(array('ico', 'css'));
  ?>
</head>
<body>
  <div id="wrapper">
  	<div id="wrapper-bgtop">
  		<div id="header-wrapper">
  			<div id="header">
  				<div id="logo">
  					<h1><a href="/"><?php echo $tag->constants->title; ?></a></h1>
  				</div>
  				<div id="menu">
  					{% MMenu/Menu | name=main %}
            <div class="clr"></div>
  				</div>
  			</div>
  		</div>
  		<div id="page" class="container">
				[block-content]
          <?php echo $page['html']; ?>
        [endblock-content]
        
        <?php /* Comments block
        {% MComments/Form | group=<?php echo $page['id']; ?> %}
        {% MComments/Comments | group=<?php echo $page['id']; ?> %}
        */ ?>
      
      </div>
  	</div>
  </div>
  <div id="bg1">
  	<div id="bg2">
  		<div id="three-cols" class="container">
  			<div id="column1">
  				<h2>***************** 1 *****************</h2>
  				***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
  			</div>
  			<div id="column2">
  				<h2>***************** 2 *****************</h2>
					###################
					###################
					###################
					###################
					###################
					###################
					###################
					###################
					###################
					###################
  			</div>
  			<div id="column3">
  				<h2>***************** 3 *****************</h2>
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************
					***********************************                                                                                                                                                                        
  			</div>
  		</div>
  	</div>
  </div>
  <div id="footer">
  	<p><?php echo $tag->constants->copy; ?></p>
  </div>
  <?php echo fsInclude::Generate(array('js')); ?>
</body>
</html>