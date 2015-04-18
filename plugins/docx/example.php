<?php
$phpdocx = new phpdocx('file.docx');
foreach(array('one', 'two') as $data) {
	$phpdocx->assign("#".$data."#", $data);
}
$phpdocx->download($downloadFileName);