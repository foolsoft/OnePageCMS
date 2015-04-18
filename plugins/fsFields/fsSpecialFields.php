<?php
class fsSpecialFields extends SplEnum 
{
    const __default = self::Email;
    
    const Email = 1;
    const MobilePhone = 2;
    const Balance = 3;
    
    static function hasKey($key) 
    {
        $foundKey = false;
        try {
            $enumClassName = get_called_class();
            new $enumClassName($key);
            $foundKey = true;
        } finally {
            return $foundKey;
        }
    }
}