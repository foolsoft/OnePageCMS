<?php
/*
 * Initialize controllers 
 */
fsFunctions::IncludeFolder(
    PATH_ROOT.'controllers/',
    IS_ADMIN_CONTROLLER ? array('Admin', 'Functions') : array('!Admin'),
    array('php'),
    array('init.php')
);