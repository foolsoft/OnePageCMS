<?php 
/*
 * Initialize plugins 
 */
fsFunctions::IncludeFile(PATH_PLUGINS.'fsCMS/init.php');
$folders = fsFunctions::DirectoryInfo(PATH_PLUGINS, false, true); 
foreach ($folders['NAMES'] as $folder) { 
  if (file_exists(PATH_PLUGINS.$folder.'/init.php') && $folder != 'fsCMS') {
    fsFunctions::IncludeFile(PATH_PLUGINS.$folder.'/init.php'); 
  }
}
unset($folders);