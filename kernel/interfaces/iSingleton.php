<?php
/**
* Interface for singleton pattern
* @package fsKernel
*/
interface iSingleton
{
    /**
    * Return object instance  
    * @api
    * @since 1.0.0
    * @return mixed Object.      
    */
    public static function GetInstance();
}