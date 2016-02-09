<?php 
fsInclude::AddCss(array(URL_THEME_CSS.'styles.css'));
$title = isset($page['title']) ? $page['title'] : $tag->title;
?>
<!DOCTYPE html>
<html lang="<?php echo fsSession::GetInstance('Language'); ?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="<?php echo isset($page['meta_keywords']) ? $page['meta_keywords'] : $tag->meta_keywords; ?>">
    <meta name="description" content="<?php echo isset($page['meta_description']) ? $page['meta_description'] : $tag->meta_description; ?>">
    <meta name="author" content="Mamatov Andrey / http://foolsoft.ru">
    <title><?php echo $title.' - '.$tag->constants->title; ?></title>
    <link href="/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/plugins/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    <script src="/plugins/bootstrap/js/ie-emulation-modes-warning.js"></script>
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php 
    fsFunctions::IncludeFile(PATH_TPL.'shared_header.php');
    fsInclude::AddJs(array(URL_THEME_JS.'scripts.js'));
    ?>
    [block-head]<?php /* ACCESS TO <HEAD> FOR CHILD TEMPLATES */ ?>[endblock-head]
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only"><?php _T('XMLcms_text_menu'); ?></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo URL_ROOT; ?>"><?php echo $tag->constants->title; ?></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            {% MMenu/Menu | name=main %}
        </div>
      </div>
    </nav>

    <div class="container">
      <div class="starter-template">
        <h1><?php echo $title; ?></h1>
        <p class="lead">
        [block-content]
          <?php echo $page['html']; ?>
        [endblock-content]
        </p>
      </div>
    
      [block-comments]
      <?php /* Comments block
      {% MComments/Form | group=<?php echo $page['id']; ?> %}
      {% MComments/Comments | group=<?php echo $page['id']; ?> %}
      */ ?>
      [endblock-comments]
    </div>

    <footer class="container text-center">
        <?php echo $tag->constants->copy; ?>
    </footer>
      
    <?php
    fsFunctions::IncludeFile(PATH_TPL.'shared_footer.php');
    echo fsInclude::GenerateCache(array('js'), fsSession::GetInstance('Language')); 
    echo fsInclude::GenerateCache(array('css'), fsSession::GetInstance('Language'));
    if (file_exists(PATH_THEME_JS.$_REQUEST['controller'].'.js')) {
        fsInclude::AttachJs(URL_THEME_JS.$_REQUEST['controller'].'.js');
    } else if (file_exists(PATH_DTHEME_JS.$_REQUEST['controller'].'.js')) {
        fsInclude::AttachJs(URL_DTHEME_JS.$_REQUEST['controller'].'.js');
    } 
    if (file_exists(PATH_THEME_CSS.$_REQUEST['controller'].'.css')) {
        fsInclude::AttachCss(URL_THEME_CSS.$_REQUEST['controller'].'.css');
    } else if (file_exists(PATH_DTHEME_CSS.$_REQUEST['controller'].'.css')) {
        fsInclude::AttachCss(URL_DTHEME_CSS.$_REQUEST['controller'].'.css');
    }
    ?>
    <script src="/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/plugins/bootstrap/js/ie10-viewport-bug-workaround.js"></script>
    [block-footer]<?php /* ACCESS TO FOOTER FOR CHILD TEMPLATES */ ?>[endblock-footer]
  </body>
</html>