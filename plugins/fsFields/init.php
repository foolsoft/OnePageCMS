<?php
fsFunctions::IncludeFiles(array(
    PATH_PLUGINS.'fsFields/fields/base/fsFieldInterface.php',
    PATH_PLUGINS.'fsFields/fields/base/fsFieldExtensionInterface.php',
    PATH_PLUGINS.'fsFields/fields/base/fsFieldAbstract.php',
    PATH_PLUGINS.'fsFields/fields/base/fsFieldExtensionAbstract.php'
));                               
fsFunctions::IncludeFolder(PATH_PLUGINS.'fsFields/fields', array(), array('php'), array('fsFieldFileAjaxImage.php'));
fsFunctions::IncludeFolder(PATH_PLUGINS.'fsFields/controllers/');
fsFunctions::IncludeFiles(array(
    PATH_PLUGINS.'fsFields/fields/fsFieldFileAjaxImage.php',
    PATH_PLUGINS.'fsFields/fsFields.php',
    PATH_PLUGINS.'fsFields/fsFieldsExtensions.php',
    PATH_PLUGINS.'fsFields/fsSpecialFields.php'
));