<?php
include_once 'init.php';

$word = 'ВАСЯ'; //Should be in uppercase!

$dictBundle = new phpMorphy_FilesBundle(PHPMORPHY_DIC_DIR, 'rus');
$morphy = null;
try {
	$morphy = new phpMorphy($dictBundle, array(
    	'storage' => PHPMORPHY_STORAGE_FILE,
    	'with_gramtab' => false,
    	'predict_by_suffix' => true, 
    	'predict_by_db' => true
    ));
} catch(phpMorphy_Exception $e) {
	die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
}
$allForms = $morphy->getAllForms($word);
if($allForms) {
    print_r($allForms);
} else {
    die('Not found :(');
}