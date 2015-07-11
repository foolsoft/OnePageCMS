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
    * @return array List of ico, css and js files to be included.     
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
    * @param string $files Path to file to be included.
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
            if(!empty($file)) { 
                self::$obj[$type][] = $file;
            }
        }
        return true;
    }
    
    /**
    * Generate HTML code for Js or Css file include.
    * @api
    * @since 1.0.0
    * @param string $type Type of include file.
    * @param string|array $files Url's of files for including.
    * @param boolean $autoShow (optional) Flag for result auto 'echo'. Default <b>true</b>.
    * @since 1.1.0
    * @param array $async (optional). Only for js type. Flags true or false for each files in $files parameter. Default value <b>false</b>. 
    * @return string Html code.     
    */
    protected static function _Attach($type, $files, $autoShow = true, $async = array()) 
    {
        $string = ''; $result = '';
        switch ($type) {
            case 'ico':
                $string = '<link rel="icon" type="image/vnd.microsoft.icon" href="{0}" />';
                break;
            case 'css':
                $string = '<link rel="stylesheet" type="text/css" href="{0}" />';
                break;
            case 'js':
                if(is_array($files) && count($files) != count($async)) {
                    $async = array();
                }
                $string = '<script{1}src="{0}"></script>';
                break;
            default:
                return $string;
        }
        if(!is_array($files)) {
            $files = array($files);
        }
        $noAsync = count($async) == 0;
        foreach($files as $idx => $file) {
            $result .= fsFunctions::StringFormat($string, array($file, $noAsync ? ' ' : ($async[$idx] === true ? ' async ' : ' ')));
        }
        if($autoShow) {
            echo $result;
        }
        return $result;
    }
    
    /**
    * Attach a css file.   
    * @api
    * @since 1.0.0
    * @param string|array $files Url's of files for including.
    * @param boolean $autoShow (optional) Flag for result auto 'echo'. Default <b>true</b>.
    * @return string Html code.     
    */
    public static function AttachCss($files, $autoShow = true) 
    {
        return self::_Attach('css', $files, $autoShow);
    }
    
    /**
    * Attach a js file.   
    * @api
    * @since 1.0.0
    * @param string|array $files Url's of files for including.
    * @param boolean $autoShow (optional) Flag for result auto 'echo'. Default <b>true</b>.
    * @since 1.1.0
    * @param array $async (optional). Flags true or false for each files in $files parameter. Default value <b>false</b>. 
    * @return string Html code.     
    */
    public static function AttachJs($files, $autoShow = true, $async = array()) 
    {
        return self::_Attach('js', $files, $autoShow, $async);
    }
    
    /**
    * Attach a ico file.   
    * @api
    * @since 1.0.0
    * @param string $file Url of ico file for including.
    * @param boolean $autoShow (optional) Flag for result auto 'echo'. Default <b>true</b>.
    * @return string Html code.     
    */
    public static function AttachIco($file, $autoShow = true) 
    {
        return self::_Attach('ico', $file, $autoShow);
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
        return self::_Add($files, 'js');
    }
    
    /**
    * Add new style file to inner structure.   
    * @api
    * @since 1.0.0
    * @param string|array $files Path to files to be included.
    * @return boolean Result of action.     
    */
    public static function AddCss($files) 
    {
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
        return self::_Add($file, 'ico');
    }
    
    /**
    * Generate HTML code for file including as one minify file.   
    * @api
    * @since 1.0.0
    * @param array $types Types of files for code generating.
    * @return string HTML files include code.     
    */
    public static function GenerateCache($types = array(), $fileSuffix = '')
    {
        self::GetInstance();
        $js = ''; $css = ''; $string = ''; 
        $minCssFile = '_minify'.$fileSuffix.'.css'; $minJsFile = '_minify'.$fileSuffix.'.js'; 
        if(count($types) == 0) {
            $types = array_keys(self::$obj);
        }
        foreach ($types as $type) {
            if(!isset(self::$obj[$type])) {
                continue;
            }
            foreach (self::$obj[$type] as $file) {
                switch($type) {
                    case 'js':
                        if(file_exists(fsCache::GetPath($minJsFile))) {
                            $string .= self::AttachJs(fsCache::GetUrl($minJsFile), false);
                            break 2;
                        }
                        $js .= file_get_contents($file)."\n";
                        break;
                    case 'css':
                        if(file_exists(fsCache::GetPath($minCssFile))) {
                            $string .= self::AttachCss(fsCache::GetUrl($minCssFile), false);
                            break 2;
                        }
                        $css .= file_get_contents($file)."\n";
                        break;
                    default:
                        break;
                }
            }
        }
        if(!empty($js)) {
            fsCache::CreateOrUpdate($minJsFile, JsMinifier::minify($js));    
            $string .= self::AttachJs(fsCache::GetUrl($minJsFile), false);
        }
        if(!empty($css)) {
            fsCache::CreateOrUpdate($minCssFile, CssMin::minify($css));
            $string .= self::AttachCss(fsCache::GetUrl($minCssFile), false);
        }
        return $string;
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
                switch($type) {
                    case 'ico':
                        $string .= self::AttachIco($file, false);
                        break;
                    case 'js':
                        $string .= self::AttachJs($file, false);
                        break;
                    case 'css':
                        $string .= self::AttachCss($file, false);
                        break;
                    default:
                        break;
                }
            }
        }
        return $string;
    }
}