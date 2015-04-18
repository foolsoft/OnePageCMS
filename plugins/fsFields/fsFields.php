<?php
class fsFields implements iSingleton 
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
    $fields = fsFunctions::DirectoryInfo(__DIR__.'/fields', true, false, 'fsField', array('php'));
    for($i = 0; $i < $fields['LENGTH']; ++$i) {
      $class = explode('.', $fields['NAMES'][$i]); 
      $class = $class[0];
      if(!class_exists($class) || strpos($class, 'fsFieldExtension') !== false) {
        continue;
      }
      $object = new $class(); 
      self::$obj[$object->name] = $object; 
      unset($object);
    }
  }
  
  public static function GetInstance($attr = false) 
  {
    if (self::$obj == null) {
      self::_Init();  
    }           
    return $attr === false ?  self::$obj : (self::$obj != null ? self::$obj[$attr] : null);
  }
                 
  public static function Create($fieldsArray, $fieldName, $fieldValue = '', $htmlAttributes = array(), $arrayName = 'fields') 
  {
    if(!array_key_exists($fieldName, $fieldsArray)) {
      return '';
    }
    $obj = self::GetInstance($fieldsArray[$fieldName]['type']);
    if($obj == null) {
      return '';
    }
    return $obj->Input($fieldsArray[$fieldName]['name'], $fieldValue, $htmlAttributes, json_decode($fieldsArray[$fieldName]['values']), $arrayName);
  }
  
  public static function GetTypes() 
  {
    if (self::$obj == null) {
      self::_Init();  
    }
    $result = array();
    foreach(self::$obj as $name => $o) {
      $result[$name] = $o->title;
    }
    return $result;
  }
  
  public static function GenerateFilter($request, $fields = array()) 
  {
    $filter = array();
    $requestFields = $request->Exists('fields') && is_array($request->fields) ? $request->fields : array();
    foreach($fields as $name => $field) {
      $value = $request->Exists($name) 
        ? $request->$name 
        : (array_key_exists($name, $requestFields) ? $requestFields[$name] : null);
      switch($field['search_type']) {
        case 'indexof':
          if($value !== null) {
            $filter[$name] = array(array('value' => $value, 'key' => '><'));
          }
          break;
        case 'interval':
          $fFrom = $name.'_from'; $fTo = $name.'_to';
          if($request->Exists($fFrom, true) || $request->Exists($fTo, true)) {
            $filter[$name] = array();
            if($request->Exists($fFrom, true)) {
              $filter[$name][] = array('value' => $request->$fFrom, 'key' => '>='); 
            }
            if($request->Exists($fTo, true)) {
              $filter[$name][] = array('value' => $request->$fTo, 'key' => '<='); 
            }
          }
          break;
        case 'equals':
        default:
          if($value !== null) {
            $filter[$name] = array(array('value' => $value, 'key' => '='));
          }
          break;
      }
    }     
    return $filter;
  }
}