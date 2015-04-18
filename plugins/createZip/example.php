<?php
$folder = 'Your folder path';
if(is_dir($folder)) {
  $fileName = $folder.'.zip';
  $createZip = new createDirZip();
  $createZip->getFilesFromFolder($folder);
  $fd = fopen(PATH_ROOT.'temp/'.$fileName, 'wb');
  $out = fwrite($fd, $createZip->getZippedfile());
  fclose($fd);
  $createZip->forceDownload(PATH_ROOT.'temp/'.$fileName); 
}

$zip = new ZipArchive();
if ($zip->open(PATH_ROOT.'temp/'.$fileName)) {
  $zip->extractTo($path_to_extract);
  $zip->close();
}