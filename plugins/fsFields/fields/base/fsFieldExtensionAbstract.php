<?php
abstract class fsFieldExtension implements iFsFieldExtension
{
  protected static $_extensionFor = null;
  
  public static function GetFor()
  {
      if(self::$_extensionFor === null) {
        die('fsFieldExtension have NULL variable');
      }
      return self::$_extensionFor;
  }
}