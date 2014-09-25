<?php header("Content-type: text/html; charset=utf-8");
$ALLOW_DBCHARSET = false;
$path = 'settings/';
$error = '';
$lang = 'ru';
include 'kernel/fsFunctions.php';
include 'kernel/fsFileWorker.php';
@session_start();
@session_destroy();
function i($var, $def = '', $show = true) { $res = isset($_POST[$var]) ? $_POST[$var] : $def; if($show === true) echo $res; return $res; }
fsFunctions::CreateDirectory($path);
if (file_exists($path.'dbSettings.php') && file_exists($path.'Settings.php')) {
  fsFunctions::Redirect('/');
}
$url_suffix = i('suffix', '/', false);
$L = array();
fsFunctions::IncludeFolder('languages/setup');
////////INSTALL/////////////////////////////////////////////////////////////////
if ($_GET && isset($_GET['lang'])) {
  if (isset($L[$_GET['lang']])) {
    $lang = $_GET['lang'];
  }
  if(!$_POST) {
    $L = $L[$lang];
  }
}
if ($_POST && isset($_POST['lang']) && isset($L[$_POST['lang']])) {
  $lang = $_POST['lang'];
}
if ($_POST) {
  $L = $L[$lang];
  while (true) {
    if (empty($_POST['admin_login'])) {
      $error = $L['text_no_admin'];
      break;
    } 
    if (empty($_POST['admin_password'])) {
      $error = $L['text_no_password'];
      break;
    } 
    if ($_POST['admin_password'] != $_POST['admin_rpassword']) {
      $error = $L['text_bad_repassword'];
      break;
    } 
    if (empty($_POST['db_name'])) {
      $error = $L['text_no_base'];
      break;
    } 
    if (empty($_POST['db_prefix'])) {
      $error = $L['text_no_prefix'];
      break;
    } 
    $connection = @mysqli_connect($_POST['db_server'], $_POST['db_login'], $_POST['db_password']);
    if (!$connection) {
      $error = $L['text_bad_connect'];
      break;
    } 
    $selectbase = $connection->select_db($_POST['db_name']);
    if (!$selectbase) {
      $error = "'".$_POST['db_name']."' - ".$L['text_base_not_found'];
      break;
    }
    $f = new fsFileWorker($path.'dbSettings.php', 'w+');
    $f->WriteLine('<?php');
    $f->WriteLine('class DBsettings');
    $f->WriteLineWithTabsAction('{', array(), 1);
    $f->WriteLine("public static \$server   = '{0}';", array($_POST['db_server']));
    $f->WriteLine("public static \$user   = '{0}';", array($_POST['db_login']));
    $f->WriteLine("public static \$password   = '{0}';", array($_POST['db_password']));
    $f->WriteLineWithTabsAction("public static \$base   = '{0}';", array($_POST['db_name']), -1);
    $f->WriteLine('}');
    $f->Write('?'.'>');
    $f->Close();
    $f = new fsFileWorker($path.'Settings.php', 'w+');
    $f->WriteLine('<?php');
    $f->WriteLine("\$GLOBALS['CONFIG'] = array(");
    $alph = '0123tyuiop456789qwerasdfghjklzVFRTGxcvbnmQAZXSWEDCBNH$%^YUJMKIOLP!@#&*()_ +=-';
    $secret = '';
    while(strlen($secret) < 25) {
        $secret .= $alph[rand(0, strlen($alph))];
    }
    $f->WriteLine("'secret' => array('ReadOnly' => true, 'Value' => '{0}'),",
                  array($secret));
    $f->WriteLine("'main_admin' => array('ReadOnly' => true, 'Value' => '{0}'),",
                  array($_POST["admin_login"]));
    $f->WriteLine("'system_language' => array('ReadOnly' => true, 'Value' => '{0}'),",
                  array($lang));
    $f->WriteLine("'db_prefix' => array('ReadOnly' => true, 'Value' => '{0}'),",
                  array($_POST['db_prefix']));
    $f->WriteLine("'cache_table' => array('ReadOnly' => true, 'Value' => '{0}'),",
                  array('true'));              
    $f->WriteLine("'cache_use' => array('ReadOnly' => true, 'Value' => '{0}'),",
                  array('true'));
    $f->WriteLine("'db_codepage' => array('ReadOnly' => true, 'Value' => '{0}'),",
                  array($_POST['db_codepage']));
    $f->WriteLine("'links_suffix' => array('ReadOnly' => true, 'Value' => '{0}'),",
                  array($url_suffix));                  
    $f->WriteLine("'url_404' => array('ReadOnly' => true, 'Value' => '{0}'),",
                  array('http://'.$_SERVER['SERVER_NAME'].'/404'.$url_suffix));
    $f->WriteLine("'multi_language' => array('ReadOnly' => true, 'Value' => {0}),",
                  array('page/index')); 
    $f->WriteLine("'start_page' => array('ReadOnly' => true, 'Value' => {0}),",
                  array(i('multilang', 'true', false))); 
    $f->WriteLine(");");
    $f->Write('?'.'>');
    $f->Close();
    $connection->Query("SET NAMES ".$_POST['db_codepage']);
    $connection->Query("
       CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."admin_menu` (
      `name` varchar(50) NOT NULL,
      `text` varchar(50) NOT NULL,
      `order` tinyint(4) NOT NULL DEFAULT '0',
      `parent` varchar(50) NOT NULL,
       PRIMARY KEY (`name`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
   
    $connection->Query("
       CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."posts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(100) NOT NULL,
        `alt` varchar(100) NOT NULL,
        `html_short` text NOT NULL,
        `html_full` text NOT NULL,
        `meta_description` varchar(500) NOT NULL,
        `meta_keywords` varchar(500) NOT NULL,
        `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `order` smallint(6) NOT NULL DEFAULT '0',
        `tpl` varchar(50) NOT NULL,
        `tpl_short` varchar(50) NOT NULL,
        `active` enum('0','1') NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM  DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
   
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."search` (
        `table_name` varchar(50) NOT NULL,
        `link` varchar(100) NOT NULL,
        `title` varchar(50) NOT NULL,
        `search_fields` varchar(250) NOT NULL,
        PRIMARY KEY (`table_name`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
   
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."post_category` (
        `id_post` int(11) NOT NULL COMMENT '".$_POST['db_prefix']."posts:title:id#',
        `id_category` int(11) NOT NULL COMMENT '".$_POST['db_prefix']."posts_category:name:id#',
        UNIQUE KEY `id_post` (`id_post`,`id_category`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."posts_category` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `alt` varchar(255) NOT NULL,
        `tpl` varchar(50) NOT NULL,
        `tpl_short` varchar(50) NOT NULL,
        `tpl_full` varchar(50) NOT NULL,
        `meta_description` varchar(500) NOT NULL,
        `meta_keywords` varchar(500) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage']." AUTO_INCREMENT=1 ;
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."constants` (
        `name` varchar(100) NOT NULL,
        `value` text NOT NULL,
        PRIMARY KEY (`name`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."controller_menu` (
        `title` varchar(50) NOT NULL,
        `controller` varchar(150) NOT NULL,
        `href` varchar(500) NOT NULL,
        PRIMARY KEY (`title`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."user_fields` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL,
        `title` varchar(50) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage']." AUTO_INCREMENT=1;
    ");
    
     $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."user_info` (
        `id_user` int(11) NOT NULL,
        `id_field` int(11) NOT NULL,
        `value` text NOT NULL,
        UNIQUE KEY `id_user` (`id_user`,`id_field`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."controller_settings` (
        `controller` varchar(100) NOT NULL,
        `name` varchar(50) NOT NULL,
        `value` text NOT NULL,
        UNIQUE KEY `controller` (`controller`,`name`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."menu` (
        `name` varchar(50) NOT NULL,
        `title` varchar(100) NOT NULL,
        `tpl` varchar(50) NOT NULL,
        PRIMARY KEY (`name`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."menu_items` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `menu_name` varchar(50) NOT NULL,
        `title` varchar(75) NOT NULL,
        `href` varchar(500) NOT NULL,
        `parent` int(11) NOT NULL,
        `order` int(11) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."pages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(100) NOT NULL,
        `alt` varchar(100) NOT NULL,
        `html` text NOT NULL,
        `in_menu` enum('0','1') NOT NULL DEFAULT '1',
        `active` enum('0','1') NOT NULL DEFAULT '1',
        `tpl` varchar(100) NOT NULL,
        `description` varchar(500) NOT NULL,
        `keywords` varchar(500) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `alt` (`alt`)
      ) ENGINE=MyISAM  DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."types_users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM  DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `login` varchar(50) NOT NULL,
        `password` varchar(125) NOT NULL,
        `active` enum('0','1') NOT NULL DEFAULT '1',
        `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '".$_POST['db_prefix']."types_users:name:id#',
        PRIMARY KEY (`id`),
        UNIQUE KEY `login` (`login`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."comment_fields` (
        `name` varchar(15) NOT NULL,
        `title` varchar(50) NOT NULL,
        `required` enum('0','1') NOT NULL DEFAULT '0',
        PRIMARY KEY (`name`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("
      CREATE TABLE IF NOT EXISTS `".$_POST['db_prefix']."comments` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `group` varchar(15) NOT NULL,
        `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `ip` varchar(50) NOT NULL,
        `text` text NOT NULL,
        `parent` bigint(20) NOT NULL DEFAULT '0',
        `main_parent` bigint(20) NOT NULL DEFAULT '0',
        `active` enum('0','1') NOT NULL DEFAULT '0',
        `author_id` int(11) NOT NULL DEFAULT '0',
        `author_name` varchar(125) NOT NULL,
        `additional` text NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=".$_POST['db_codepage'].";
    ");
    
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."menu` (`title`, `name`, `tpl`) VALUES ('".$L['text_main_menu']."', 'main', 'Menu.php');");
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."posts_category` (`name`, `alt`, `tpl`, `tpl_short`, `tpl_full`, `meta_keywords`, `meta_description`) VALUES ('".$L['text_all_category']."', 'all', 'Index.php', 'ShortPost.php', 'Post.php', '".$L['text_all_category']."', '".$L['text_all_category']."');");
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."menu_items` (`id`, `menu_name`, `title`, `href`, `parent`, `order`) VALUES ('1', 'main', '".$L['text_main']."', 'http://".$_SERVER["SERVER_NAME"]."/".(i('multilang', 'true', false) == 'true' ? $lang.'/' : '')."', '0', '0');");
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."types_users` (`id`, `name`) VALUES (1, '".$L['text_admin']."'), (2, '".$L['text_user']."');");
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."users` (`login`, `password`, `active`, `type`) VALUES ('".$_POST["admin_login"]."', '".md5($_POST["admin_password"])."', '1', '1');");
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."pages` (`title`, `alt`, `html`, `in_menu`, `active`, `tpl`) VALUES ('".$L['text_main']."', 'index', '".fsFunctions::StringFormat($L['text'], array('{URL_ROOT}AdminPanel/Hello{URL_SUFFIX}'))."', '1', '1', 'Index.php');");
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."pages` (`title`, `alt`, `html`, `in_menu`, `active`, `tpl`) VALUES ('".$L['text_closed']."', 'closed', '<h1 style=\"text-align: center;\"><br /> </h1><h4 style=\"text-align: center;\"><span style=\"color:#808080;\"><span style=\"font-family: tahoma,geneva,sans-serif;\"><strong>".$L['text_soon']."</strong></span></span></h4><h5 style=\"text-align: center;\"><u><span style=\"font-family:tahoma,geneva,sans-serif;\"><span style=\"color: rgb(128, 128, 128);\">2013 (c) </span><a alt=\"".$L['text_dblog']."\" href=\"http://foolsoft.ru/\" target=\"_blank\" title=\"".$L['text_dblog']."\"><span style=\"color: rgb(128, 128, 128);\">".$L['text_a']."</span></a></span></u></h5>', '0', '1', 'IndexClosed.php');");
                                                                                                                                                      
    $url = substr($_SERVER["SERVER_NAME"], 0, 4) == 'www.' ? substr($_SERVER["SERVER_NAME"], 4) : $_SERVER["SERVER_NAME"];
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."controller_settings` (`controller`, `name`, `value`) VALUES
      ('MComments', 'sorting', 'ASC'),
      ('MComments', 'min_length', '5'),
      ('MComments', 'max_length', '500'),
      ('MComments', 'edit_time', '300'),
      ('MComments', 'block_ip', ''),
      ('MComments', 'block_users', ''),
      ('MComments', 'allow_guests', '1'),
      ('MComments', 'comments_on_page', '0'),
      ('Panel', 'start_page_custom', ''),
      ('Panel', 'robot_email', 'robot@".$url."'),
      ('Panel', 'version', '2.1.0.0'),
      ('Panel', 'main_template', 'Index.php'),
      ('Panel', 'template', 'fsCMS/default'),
      ('Panel', 'template_admin', 'fsCMSAdmin/default'),
      ('MMenu', 'default_template', 'Menu.php'),
      ('MPosts', 'page_count', '10'),
      ('MUsers', 'allow_register', '0'),
      ('Panel', 'default_description', '".$L['text_description']."'),
      ('Panel', 'default_keywords', '".$L['text_kw']."'),
      ('Panel', 'page_not_found', '".$L['text_nf']."');");
                     
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."constants` (`name`, `value`) VALUES
      ('title', '".$L['text_title']."'),
      ('copy', '2011-".date('Y')." © <a href=\"http://foolsoft.ru\" style=\"text-decoration:none;\" title=\"".$L['text_dblog']."\">".$L['text_a']."</a>');");
    
    $connection->Query("INSERT INTO `".$_POST['db_prefix']."admin_menu` (`name`, `text`, `order`, `parent`) VALUES
      ('AdminPanel/Hello', '".$L['text_mmain']."', 0, ''),
      ('AdminMPages/Index', '".$L['text_mpages']."', 2, ''),
      ('AdminMPosts/Index', '".$L['text_mposts']."', 2, ''),
      ('AdminMConstants/Index', '".$L['text_mconst']."', 3, ''),
      ('AdminMModules/Index', '".$L['text_mmodules']."', 10, ''),
      ('AdminMMenu/Index', '".$L['text_mmenu']."', 8, ''),
      ('AdminMComments/Index', '".$L['text_mcomments']."', 7, ''),
      ('AdminMDictionary/Index', '".$L['text_words']."', 9, ''),
      ('AdminMUsers/Index', '".$L['text_musers']."', 1, '');");
    
    $connection->Query("
      INSERT INTO `".$_POST['db_prefix']."search` (`table_name`, `link`, `title`, `search_fields`) VALUES
      ('pages', 'page/{alt}', '{title}', 'html,title'),
      ('posts', 'post/{alt}', '{title}', 'title,html_short,html_full');
    ");
      
    $connection->Close();
    echo $L['text_ok'];
    echo "<script type='text/javascript'>setTimeout('window.location = \"/\";', 5000);</script>";
    exit;
  }
}
///////////////////////////////////////////////////////
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo isset($L['ptitle']) ? $L['ptitle'] : 'Select language'; ?></title>
  <link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo "http://".$_SERVER["SERVER_NAME"]."/favicon.ico"; ?>">
</head>
<body style='background:#EEEEE0;padding:0;margin:0;'>
  <div style='width:1000px;margin:0 auto;'>
    <div style='width:100%;text-align:center;margin-top:10%;'>
      <center>
      <?php if (($_GET && isset($_GET['lang'])) || $_POST) { ?>
        <form method='post' style='width:100%;'>
          <table>
            <tr>
              <th colspan='2'><font color="#FF0000"><?php echo $error; ?></font></th>
            </tr>
            <tr>
              <th colspan='2'><u><?php echo $L['ptitle_sql']; ?></u></th>
            </tr>
            <tr>
              <th align='right'><?php echo $L['p_sql_server']; ?>:</th>
              <td align='left'><input type='text' value='<?php i('db_server', 'localhost'); ?>' name='db_server'/></td>
            </tr>
            <tr>
              <th align='right'><?php echo $L['p_sql_db']; ?>:</th>
              <td align='left'><input type='text' value='<?php i('db_name'); ?>' name='db_name'/></td>
            </tr>
            <tr>
              <th align='right'><?php echo $L['p_sql_dbu']; ?>:</th>
              <td align='left'><input type='text' value='<?php i('db_login', 'root'); ?>' name='db_login'/></td>
            </tr>
            <tr>
              <th align='right'><?php echo $L['p_sql_dbp']; ?>:</th>
              <td align='left'><input type='password' value='<?php i('db_password'); ?>' name='db_password'/></td>
            </tr>
            <tr>
              <th colspan='2'>&nbsp;</th>
            </tr>
            <tr>
              <th colspan='2'><u><?php echo $L['ptitle_cms']; ?></u></th>
            </tr>                  
            <tr>
              <th align='right'><b><?php echo $L['pcms_suffix']; ?>:</b></th>
              <td align='left'><select name='suffix' style='width:147px;'>
                <option value='' <?php echo i('suffix', '/', false) == '' ? 'selected' : ''; ?>><?php echo $L['text_no']; ?></option>
                <option value='/' <?php echo i('suffix', '/', false) == '/' ? 'selected' : ''; ?>>/</option>
                <option value='.html' <?php echo i('suffix', '/', false) == '.html' ? 'selected' : ''; ?>>.html</option>
                <option value='.htm' <?php echo i('suffix', '/', false) == '.htm' ? 'selected' : ''; ?>>.htm</option>
                <option value='.asp' <?php echo i('suffix', '/', false) == '.asp' ? 'selected' : ''; ?>>.asp</option>
                <option value='.jsp' <?php echo i('suffix', '/', false) == '.jsp' ? 'selected' : ''; ?>>.jsp</option>
              </select></td>
            </tr>
            <tr>
              <th align='right'><b><?php echo $L['pcms_multilang']; ?>:</b></th>
              <td align='left'><select name='multilang' style='width:147px;'><option value='true' <?php echo i('multilang', 'true', false) == 'true' ? 'selected' : ''; ?>><?php echo $L['text_yes']; ?></option>
              <option value='false' <?php echo i('multilang', 'true', false) == 'false' ? 'selected' : ''; ?>><?php echo $L['text_no']; ?></option></select></td>
            </tr>
            <tr>
              <th align='right'><?php echo $L['pcms_dbc']; ?>:</th>
              <?php if ($ALLOW_DBCHARSET) { ?>
                <td align='left'><select name='db_codepage' style='width:147px;'><option value='utf8' selected>utf8</option><option value='cp1251'>cp1251</option></select></td>
              <?php } else { ?>
                <td align='left'><input type='hidden' value='utf8' name='db_codepage' />Utf8</td>
              <?php } ?>
            </tr>
            <tr>
              <th align='right'><b><?php echo $L['pcms_lang']; ?>:</b></th>
              <td align='left'><select name='lang' style='width:147px;'><option value='<?php echo $_GET['lang']; ?>' selected><?php echo $_GET['lang']; ?></option></select></td>
            </tr>
            <tr>
              <th align='right'><b><?php echo $L['pcms_prefix']; ?>:</b></th>
              <td align='left'><input type='text' value='<?php i('db_prefix', 'opc_'); ?>' name='db_prefix'/></td>
            </tr>
            <tr>
              <th align='right'><?php echo $L['pcms_alogin']; ?>:</th>
              <td align='left'><input type='text' value='<?php i('admin_login', 'Admin'); ?>' name='admin_login'/></td>
            </tr>
            <tr>
              <th align='right'><?php echo $L['pcms_apassword']; ?>:</th>
              <td align='left'><input type='password' value='<?php i('admin_password'); ?>' name='admin_password'/></td>
            </tr>
            <tr>
              <th align='right'><?php echo $L['pcms_arepassword']; ?>:</th>
              <td align='left'><input type='password' value='<?php i('admin_rpassword'); ?>' name='admin_rpassword'/></td>
            </tr>
            <tr>
              <th align='right' colspan='2'><input type='submit' value='<?php echo $L['do']; ?>' /></th>
            </tr>
          </table>
        </form>
      <?php } else { ?>
        <form method="get">
          <select name="lang">
            <?php
              foreach ($L as $langIso => $arr) {
                echo '<option value="'.$langIso.'">'.$arr['text_lang_name'].'</option>';
              }
            ?>
          </select>   
          <input type='submit' value='OK' />
        </form>
      <?php } ?>
      </center>
    </div>
  </div>
</body>
</html>