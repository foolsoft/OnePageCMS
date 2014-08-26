<?php
//Представление глобального массива $_SESSION в виде fsStruct
class fsSession implements iSingleton 
{
	protected static $obj = null;
	private function __construct(){ }
	private function __clone()    { } 
	private function __wakeup()   { }
	
  ////$attr - название индекса массива в $_SESSION
  //если $attr не указан возвращается объект fsStruct 
  //если $attr указан, но не существует в fsSession, возвращается null
  public static function GetInstance($attr = false) 
  {
    if (self::$obj === null) {
      self::$obj = new fsStruct($_SESSION, true);
    }
    return $attr === false
           ? self::$obj
           : self::$obj->Exists($attr)
             ? self::$obj->$attr
             : null;
  }
	
	public static function GetArrInstance($attr, $field) 
  {
    $attrObj = self::GetInstance($attr);
    if ($attrObj !== null && isset($attrObj[$field])) {
      return $attrObj[$field];
    } else {
      return null;
    }
  }
  
  public static function SetOrCreate($attr, $value)
  {
    $_SESSION[$attr] = $value;
    self::$obj = null;
  }
  	
  //Установка значнеия $value в переменную сессии $_SESSION[$attr]
  //если переменная $_SESSION[$attr] не существует выбрасывается исключение
  public static function Set($attr, $value) 
  {
    if (self::GetInstance($attr) !== null) {
      $_SESSION[$attr] = $value;
      self::$obj = null;
    } else {
      throw new fsSessionException('Invalid session field (Set): '.$attr);
    }
  }
  
  //Проверка существования переменной $_SESSION[$attr]
  public static function Exists($attr)
  {
    return self::GetInstance($attr) !== null;  
  }
  
  //Удаление переменной $_SESSION[$attr]
  //если переменная $_SESSION[$attr] не существует выбрасывается исключение
  public static function Delete($attr)
  {
    if (self::GetInstance($attr) !== null) {
      self::$obj->Delete($attr); 
      unset($_SESSION[$attr]);
    } else {
      throw new fsSessionException('Invalid session field (Delete): '.$attr);
    }
  }
  
  //Создание переменной $_SESSION[$attr] со значение $value
  //если переменная $_SESSION[$attr] существует выбрасывается исключение
  public static function Create($attr, $value) 
  {
    if (self::GetInstance($attr) === null) {
      $_SESSION[$attr] = $value;
      self::$obj = null;
    } else {
      throw new fsSessionException('Session field already exists: '.$attr);
    }
  } 

}

//Определение соственного класса исключений для fsSession
class fsSessionException extends Exception
{
}
?>