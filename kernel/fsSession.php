<?php
/**
* Session worker
* @package fsKernel
*/
class fsSession implements iSingleton 
{
    protected static $obj = null;
    private function __construct(){ }
    private function __clone()    { } 
    private function __wakeup()   { }
	
    /**
    * Get session object.    
    * @since 1.0.0
    * @api   
    * @param string $attr Session attribute name. If empty return session object. Default <b>empty string</b>.  
    * @return mixed Session object or session attribute.  
    */
    public static function GetInstance($attr = '') 
    {
      if (self::$obj === null) {
        self::$obj = new fsStruct($_SESSION, true);
      }
      return empty($attr) ? self::$obj
             : self::$obj->Exists($attr)
               ? self::$obj->$attr : null;
    }

    /**
    * Get session array value.    
    * @since 1.0.0
    * @api   
    * @param string $attr Session array attribute name.
    * @param string $field Array field name.
    * @return mixed Attribute value or null if not found.  
    */    
    public static function GetArrInstance($attr, $field) 
    {
      $attrObj = self::GetInstance($attr);
      if ($attrObj !== null && is_array($attrObj) && isset($attrObj[$field])) {
        return $attrObj[$field];
      } else {
        return null;
      }
    }
    
    /**
    * Create or rewrite session value.    
    * @since 1.0.0
    * @api   
    * @param string $attr Session attribute name.
    * @param string $value New value.
    * @return void  
    */
    public static function SetOrCreate($attr, $value)
    {
      $_SESSION[$attr] = $value;
      self::$obj = null;
    }

    /**
    * Set session value.    
    * @since 1.0.0
    * @api   
    * @param string $attr Session attribute name.
    * @param string $value Attribute value.
    * @return void  
    */
    public static function Set($attr, $value) 
    {
      if (self::GetInstance($attr) !== null) {
        $_SESSION[$attr] = $value;
        self::$obj = null;
      } else {
        throw new Exception('Invalid session field (Set): '.$attr);
      }
    }

    /**
    * Check existing of session attribute.    
    * @since 1.0.0
    * @api   
    * @param string $attr Session attribute name.
    * @return boolean Result of action  
    */
    public static function Exists($attr)
    {
      return self::GetInstance($attr) !== null;  
    }

    /**
    * Delete session value.    
    * @since 1.0.0
    * @api   
    * @param string $attr Session attribute name.
    * @return void  
    */
    public static function Delete($attr)
    {
      if (self::GetInstance($attr) !== null) {
        self::$obj->Delete($attr); 
        unset($_SESSION[$attr]);
      } else {
        throw new Exception('Invalid session field (Delete): '.$attr);
      }
    }

    /**
    * Create session value.    
    * @since 1.0.0
    * @api   
    * @param string $attr Session attribute name.
    * @param string $value New value.
    * @return void  
    */
    public static function Create($attr, $value) 
    {
      if (self::GetInstance($attr) === null) {
        $_SESSION[$attr] = $value;
        self::$obj = null;
      } else {
        throw new Exception('Session field already exists: '.$attr);
      }
    } 
}