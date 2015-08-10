<?php
set_time_limit(0);
include('init.php');
$sitemap = new sitemap();
 
//Ignore links
$sitemap->set_ignore(array("javascript:", ".css", ".js", ".ico", ".jpg", ".png", ".jpeg", ".swf", ".gif"));
//Go
$sitemap->get_links('http://'.$_SERVER['SERVER_NAME']);
 
//Get array
//$arr = $sitemap->get_array();
//echo "<pre>";
//print_r($arr);
//echo "</pre>"; 
 
header ('Content-type: text/xml');
$map = $sitemap->generate_sitemap();
echo $map;

