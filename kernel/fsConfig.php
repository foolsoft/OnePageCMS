<?php
/**
* Kernel config class
* @package fsKernel
*/
class fsConfig implements iSingleton 
{
  /** @var fsStruct Instance of $GLOBALS['CONFIG'] as fsStruct */
  protected static $obj = null;
	
  private function __construct(){ }
  private function __clone()    { } 
  private function __wakeup()   { }
	
  /**
    * Get kernel config value.   
    * @api
    * @since 1.0.0
    * @param mixed $attr (optional) $attr String name of config filed or <b>false</b> for instance return. Default <b>false</b>. 
    * @return string|object Config value or config instance as fsStruct if $attr is <b>false</b>.     
    */
  public static function GetInstance($attr = false) 
  {
    if (self::$obj == null && isset($GLOBALS['CONFIG'])) {
      self::$obj = new fsStruct($GLOBALS['CONFIG']);
      unset($GLOBALS['CONFIG']);
    }
    return $attr === false 
      ?  self::$obj 
      : self::$obj != null
        ? self::$obj->$attr
        : null;
  }
}
?>