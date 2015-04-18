<?php 
/*
 * Initialize plugins 
 */
fsFunctions::IncludeFile(PATH_PLUGINS.'fsCMS/init.php');
$folders = fsFunctions::DirectoryInfo(PATH_PLUGINS, false, true, array('!fsCMS'));
foreach ($folders['NAMES'] as $folder) { 
    $path = PATH_PLUGINS.$folder.'/init.php';
    if(file_exists($path)) {
        fsFunctions::IncludeFile($path);
    }
}
unset($folders);