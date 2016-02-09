<?php
/**
 * Session worker
 * @package fsKernel
 */
class fsSession implements iSingleton 
{
    protected static $_sessionIdField = '_fs_session_id';
    protected static $obj = null;

    private function __construct() { }
    private function __clone() { }
    private function __wakeup() { }

    /**
     * Session protection key.    
     * @since 1.1.0
     * @api 
     * @return string Key for current session.  
     */
    private static function _GenerateSessionKey() 
    {
        $string = fsConfig::GetInstance('secret');
        ksort($_SESSION);
        foreach($_SESSION as $key => $value) {
            if($key != self::$_sessionIdField) {
                $string .= $key.(is_array($value) ? fsFunctions::ArrayToString($value) : $value);
            }
        }
        $string = sha1($string);
        return $string;
    }

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
            $sessionKey = self::_GenerateSessionKey();
            if(!isset($_SESSION[self::$_sessionIdField])) {
                $_SESSION[self::$_sessionIdField] = $sessionKey;
            }
            self::$obj = new fsStruct($_SESSION, true);
            if(self::GetInstance(self::$_sessionIdField) != $sessionKey) {
                //print_r($_SESSION);
                self::Destroy();
                echo '<script>setTimeout(function() { window.location = "/"; }, 5000);</script>';
                die('Invalid session key! Redirect in progress...');
            }
        }
        return empty($attr) ? self::$obj : self::$obj->Exists($attr) ? self::$obj->$attr : null;
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
        $_SESSION[self::$_sessionIdField] = self::_GenerateSessionKey();
        self::$obj = null;
    }

    /**
     * Create or rewrite session array value.
     * @since 1.1.0
     * @api
     * @param string $key Session array name.
     * @param string $attr Session attribute name.
     * @param string $value New value.
     * @return void
     */
    public static function ArrSetOrCreate($key, $attr, $value)
    {
        if(!isset($_SESSION[$key]) || !is_array($_SESSION[$key])) {
            $_SESSION[$key] = array();
        }
        $_SESSION[$key][$attr] = $value;
        $_SESSION[self::$_sessionIdField] = self::_GenerateSessionKey();
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
            self::SetOrCreate($attr, $value);
        } else {
            throw new Exception('Invalid session field (Set): ' . $attr);
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
            $_SESSION[self::$_sessionIdField] = self::_GenerateSessionKey();
        } else {
            throw new Exception('Invalid session field (Delete): ' . $attr);
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
            self::SetOrCreate($attr, $value);
        } else {
            throw new Exception('Session field already exists: ' . $attr);
        }
    }
    
    /**
     * Destroy session.    
     * @since 1.0.0
     * @api   
     * @param string $attr Session attribute name.
     * @param string $value New value.
     * @return void  
     */
    public static function Destroy() 
    {
        session_destroy(); 
        self::$obj = null;
    }

}
