<?php
class fsFieldsExtensions implements iSingleton                  
{                
  protected static $obj = null;
	
  private function __construct(){ }
  private function __clone()    { } 
  private function __wakeup()   { }
	
  private static function _Init()
  {
    if (self::$obj != null) {
      return;
    }
    self::$obj = array();            
    $fields = fsFunctions::DirectoryInfo(__DIR__.'/fields', true, false, 'fsFieldExtensions', array('php'));
    for($i = 0; $i < $fields['LENGTH']; ++$i) {
      $class = explode('.', $fields['NAMES'][$i]);
      $class = $class[0];
      if(!class_exists($class) || !method_exists($class, 'Run') || !method_exists($class, 'GetFor')) {
        continue;
      }
      $object = new $class(); 
      self::$obj[$object->GetFor()] = $object; 
      unset($object);
    }
  }
  
  public static function GetInstance($attr = false) 
  {
    if (self::$obj == null) {
      self::_Init();  
    }           
    return $attr === false ?  self::$obj : (self::$obj != null && isset(self::$obj[$attr]) ? self::$obj[$attr] : null);
  }
}