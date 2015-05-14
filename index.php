<?php
/* OnePageCMS
2011-2015 (c) Mamatov Andrey
url: http://onepagecms.net, http://onepagecms.ru, http://foolsoft.ru
*/
include './kernel/init.php';
//fsFunctions::BasicAuth('admin', '12345');
$fsKernel = new fsKernel();
//fsFunctions::FormatPrint($_REQUEST); //Debug query
$fsKernel->DoMethod(); 