<?php
class fsValidator
{
  //переопределение preg_match для класса
  public static function Match($what, $pattern)
  {
    return  preg_match($pattern, $what);
  }
  
  //очистка строки $data от "плохих" символов
  public static function ClearData($data)
  {
    $data = strip_tags($data);
    $data = str_replace('"', '&#34;', $data);
    $data = str_replace("'", "&#39;", $data);
    $data = str_replace("%", '&#37;', $data);
    $data = str_replace(";", '&#59;', $data);
    $data = str_replace("--", "-", $data);
    $data = str_replace("`", "``", $data);
    $data = str_replace("_", "&#95;", $data);
    return $data;
  }
  
  //проверка строки $what по правилу $how с вохможными парамтерми $attr
  //$how строка с названиями проверок с разделителем |
  public static function Check($what, $how, $attr = array())
  {
    $how = explode('|', $how);
    $howLength = count($how);
    for ($i = 0; $i < $howLength; ++$i)
    {
      switch (strtoupper($how[$i]))
      {
        case 'NUMERIC': //число 
          if (!is_numeric($what)) {
           return false;
          }
          break;
          
        case 'FNUMERIC': //float 
          if(!self::Match($what, "/^\d+([\.|\,]\d+)?$/s")) {
            return false;
          }
          break;
          
        case 'POSITIVE': //положительное число 
          if (!is_numeric($what) || $what <= 0) { 
            return false;
          }
           break;
           
        case 'NEGATIVE': //отричательное число
          if (!is_numeric($what) || $what >= 0) {
            return false;
          }
          break;
          
        case 'EMPTY': //пустая строка
          if (!empty($what)) {
            return false;
          }
          break;
          
        case 'NOTEMPTY': //не пустая строка
          if (empty($what)) {
            return false;
          }
          break;
        
        case 'LENGTH': //проверка длины строки
          if (!isset($attr[$i]) || strlen($what) > $attr[$i]) {
            return false;
          }
          break;
          
        case 'ENUM': //проверка на определенное значение
          if (!isset($attr[$i]) || !self::Match($what, "/^(".$attr[$i].")$/s")) {
            return false;
          }
          break;
        
        case 'TIMEDATE': //формат даты и времени
          if(!self::Match($what, "/^\d{4}-\d{1,2}-\d{1,2}(\s\d{1,2}:\d{1,2}:\d{1,2})?$/s")) {
            return false;
          }
          break;
        
        case 'EMAIL': //email
          if(!self::Match($what, "/^[a-zA-Z0-9\-\_\.]+@[a-zA-Z0-9\-]+\.[a-z]{2,}$/s")) {
            return false;
          }
          break;
        
        default:
          return false;
      }
    }
    return true;
  }
}
?>