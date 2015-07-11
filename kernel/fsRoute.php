<?php
/**
* Route class
* @package fsKernel
*/
class fsRoute
{
    private static $_requestField = 'route';
  
    /**
    * Generate request route.    
    * @since 1.0.0
    * @api    
    * @return void.  
    */
    public static function Request()
    {
        if (!isset($_REQUEST[self::$_requestField])) {
            return; 
        }
        $route = explode('/', $_REQUEST[self::$_requestField]);
        $routeLength = count($route) - 1;
        for ($i = 0; $i < $routeLength; $i += 2) {
            if (!isset($_REQUEST['controller'])) {
                $_REQUEST['controller'] = $route[$i];
                $_REQUEST['method'] = $route[$i + 1]; 
            } else {
                $_REQUEST[$route[$i]] = $route[$i + 1]; 
            }
        }
        unset($_REQUEST[self::$_requestField]);
    }
}