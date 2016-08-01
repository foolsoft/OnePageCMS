<?php
/**
* Validator class
* @package fsKernel
*/
class fsValidator
{
    /**
    * Check regular expression.    
    * @since 1.0.0
    * @api   
    * @param string $what Source text.  
    * @param string $pattern Regular expression.  
    * @return boolean|integer Count of matches.  
    */
    public static function Match($what, $pattern)
    {
        return preg_match($pattern, $what);
    }
  
    /**
    * Clean string.    
    * @since 1.0.0
    * @api   
    * @param string $data Source text.  
    * @return string Safe string.  
    */
    public static function ClearData($data)
    {
        $data = strip_tags($data);
        $data = str_replace(
          array('"', "'", '%', ';', '--', '`', '#', '$'), 
          array('&#34;', "&#39;", '&#37;', '&#59;', '-', '``', '&#35;', '&#36;'),
          $data
        );
        return $data;
    }
  
    /**
    * Delete special chars from string.    
    * @since 1.0.0
    * @api   
    * @param string $data Source text.  
    * @param array $allow (optional) Allowed symbols. Default <b>empty array</b>.
    * @return string Clear text.  
    */              
    public static function TextOnly($data, $allow = array())
    {
        $disallow = array(
          ' ', '-', '#', '$', '%', '"', "'", '@', '*', '/', '\\', '|',
          '!', '_', '^', '&', '(', ')', '=', '+', '?', '~', '>', '<',
          "\t", "\r", "\n", '`', '[', ']', '{', '}', ':', ';'       
        );
        $chars = array_diff($disallow, $allow);
        return str_replace($chars, '', $data);
    }
  
    /**
    * Check string.    
    * @since 1.0.0
    * @api   
    * @param string $what Source text.  
    * @param string $how Method of checking.  
    * @param array $attr (optional) Additional checking parameters.  
    * @return boolean Result of checking.  
    */
    public static function Check($what, $how, $attr = array())
    {
        $how = explode('|', $how);
        $howLength = count($how);
        for ($i = 0; $i < $howLength; ++$i)
        {
            switch (strtoupper($how[$i]))
            {
                case 'NUMERIC': //number 
                    if (!is_numeric($what)) {
                        return false;
                    }
                    break;

                case 'FNUMERIC': //float 
                    if(!self::Match($what, "/^\d+([\.|\,]\d+)?$/s")) {
                        return false;
                    }
                  break;

                case 'POSITIVE': //number > 0 
                    if (!is_numeric($what) || $what <= 0) { 
                        return false;
                    }
                    break;

                case 'NEGATIVE': //number < 0
                    if (!is_numeric($what) || $what >= 0) {
                        return false;
                    }
                    break;

                case 'EMPTY': //empty string
                    if (!empty($what)) {
                        return false;
                    }
                    break;

                case 'NOTEMPTY': //not empty string
                    if (empty($what)) {
                    return false;
                    }
                    break;

                case 'LENGTH': //string length
                    if (!isset($attr[$i]) || mb_strlen($what) > $attr[$i]) {
                        return false;
                    }
                    break;

                case 'ENUM': //enum
                    if (!isset($attr[$i]) || !self::Match($what, '/^('.$attr[$i].')$/s')) {
                        return false;
                    }
                    break;

                case 'TIMEDATE': //timedate
                    if(!self::Match($what, "/^\d{4}-\d{1,2}-\d{1,2}(\s\d{1,2}:\d{1,2}:\d{1,2})?$/s")) {
                        return false;
                    }
                    break;

                case 'EMAIL': //email
                    if(!self::Match($what, "/^[a-zA-Z0-9\-\_\.]+@[a-zA-Z0-9\-\.]+\.[a-z]{2,}$/s")) {
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