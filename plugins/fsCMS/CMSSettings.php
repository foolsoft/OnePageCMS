<?php
class CMSSettings implements iSingleton
{
	protected static $obj = null;
	private function __construct(){ }
	private function __clone()    { } 
	private function __wakeup()   { }
	
	public static function Init($data = null) 
  {
    if (self::$obj == null) {
      self::$obj = $data;
    }
    return self::$obj;
  }
  
  public static function GetInstance($attr = false, $data = null) 
  {
    if ($data !== null) {
      self::Init($data);
    }
    return $attr === false ?  self::$obj : self::$obj->$attr;
  }
}
?>