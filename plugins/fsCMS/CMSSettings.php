<?php
/**
 * fsCMS settings structure
 */
class CMSSettings implements iSingleton
{
    /** @var fsStruct Class instance */ 
    protected static $obj = null;
    private function __construct(){ }
    private function __clone()    { } 
    private function __wakeup()   { }
	
    /**
    * Initialize object   
    * @api
    * @since 1.0.0
    * @param fsStruct $data Data for initialize. Default <b>null</b>. 
    * @return object Config instance as fsStruct.     
    */
    public static function Init($data) 
    {
      if (self::$obj == null) {
        self::$obj = $data;
      }
      return self::$obj;
    }

    /**
    * Get cms config values.   
    * @api
    * @since 1.0.0
    * @param mixed $attr (optional) String name of config filed or <b>false</b> for instance return. Default <b>false</b>. 
    * @param fsStruct $data (optional) Data for initialize. Default <b>null</b>. 
    * @return string|object Config value or config instance as fsStruct if $attr is <b>false</b>.     
    */
    public static function GetInstance($attr = false, $data = null) 
    {
      if ($data !== null) {
        self::Init($data);
      }
      return $attr === false ?  self::$obj : self::$obj->$attr;
    }
}