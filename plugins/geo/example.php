<?php
$geo = new Geo(array());     
$city = $geo->GetValue('city', true);
if(empty($city)) {
    $city = $geo->GetValue('country', true); 
}   
echo $city;