<?php
class fsFieldExtensionNumber extends fsFieldExtension 
{
    protected static $_extensionFor = 'number';
    
    public static function Run(&$fieldsArray, $name)
    {
        if(!isset($fieldsArray[$name])) {
            return;
        }
        $value = $fieldsArray[$name];
        $fieldsArray[$name.'_s'] = fsFunctions::StringFromNumber($value);
    }
}