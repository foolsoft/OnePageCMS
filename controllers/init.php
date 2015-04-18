<?php
/*
 * Initialize controllers 
 */
fsFunctions::IncludeFolder(PATH_ROOT.'controllers/', array((IS_ADMIN_CONTROLLER ? '' : '!').'Admin'), array('php'), array('init.php')); 