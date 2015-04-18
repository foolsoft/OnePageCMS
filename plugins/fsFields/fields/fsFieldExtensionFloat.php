<?php
class fsFieldExtensionFloat extends fsFieldExtension 
{
    protected static $_extensionFor = 'numberfloat';
    
    public static function Run(&$fieldsArray, $name)
    {
        if(!isset($fieldsArray[$name])) {
            return;
        }
        $value = $fieldsArray[$name];
        $fieldsArray[$name.'_s'] = fsFunctions::StringFromNumber($value);
    }
}