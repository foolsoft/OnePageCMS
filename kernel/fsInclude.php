<?php
/**
* Site js and css include links generator
* @package fsKernel
*/
class fsInclude implements iSingleton 
{
    /** @var object Instance of object */
    protected static $obj = null;
    
    private function __construct(){ }
    private function __clone()    { } 
    private function __wakeup()   { }
    
    /**
    * Get instance of object.   
    * @api
    * @since 1.0.0
    * @return array List of css and js files to be included.     
    */
    public static function GetInstance() 
    {
        if (self::$obj == null) {
          self::$obj = array('js' => array(), 'css' => array(), 'ico' => array());
        }
        return self::$obj;
    }
    
    /**
    * Add new file to inner structure.   
    * @api
    * @since 1.0.0
    * @param string $file Path to file to be included.
    * @param string $type Type of include file.
    * @return boolean Result of action.     
    */
    protected static function _Add($files, $type) 
    {
        self::GetInstance();
        if(!isset(self::$obj[$type])) {
            return false;
        }
        if(!is_array($files)) {
            $files = array($files);
        }
        foreach($files as $file) {
            $result += self::$obj[$type][] = $file;
        }
        return true;
    }
    
    /**
    * Add new javascript file to inner structure.   
    * @api
    * @since 1.0.0
    * @param string|array $files Path to files to be included.
    * @return boolean Result of action.     
    */
    public static function AddJs($files) 
    {
        self::GetInstance();
        return self::_Add($files, 'js');
    }
    
    /**
    * Add new style file to inner structure.   
    * @api
    * @since 1.0.0
    * @param string|array $files Path to file to be included.
    * @return boolean Result of action.     
    */
    public static function AddCss($files) 
    {
        self::GetInstance();
        return self::_Add($files, 'css');
    }
    
    /**
    * Add new icon file to inner structure.   
    * @api
    * @since 1.0.0
    * @param string $file Path to file to be included.
    * @return boolean Result of action.     
    */
    public static function AddIco($file) 
    {
        self::GetInstance();
        return self::_Add($file, 'ico');
    }
    
    /**
    * Generate HTML code for file including.   
    * @api
    * @since 1.0.0
    * @param array $types Types of files for code generating.
    * @return string HTML files include code.     
    */
    public static function Generate($types = array())
    {
        self::GetInstance();
        $string = '';
        if(count($types) == 0) {
            $types = array_keys(self::$obj);
        }
        foreach ($types as $type) {
            if(!isset(self::$obj[$type])) {
                continue;
            }
            foreach (self::$obj[$type] as $file) {
                $template = '';
                switch($type) {
                    case 'ico':
                        $template = '<link rel="icon" type="image/vnd.microsoft.icon" href="{0}" />';
                        break;
                    case 'js':
                        $template = '<script type="text/javascript" src="{0}"></script>';
                        break;
                    case 'css':
                        $template = '<link rel="stylesheet" type="text/css" href="{0}" />';
                        break;
                    default:
                        break;
                }
                if(!empty($template)) {
                    $string .= fsFunctions::StringFormat($template, array($file));
                }
            }
        }
        return $string;
    }
}
?>