<?php
/**
* Cache class
* @package fsKernel
*/
class fsCache        
{
    /** @var string Path of cache folder */
    private static $_path = PATH_CACHE;
    /** @var string Url of cache folder */
    private static $_url = URL_CACHE;
    /** @var string Suffix for cache life time file */
    private static $_timeSuffix = '.time';
    
    /**
    * Create cache file    
    * @since 1.0.0
    * @api    
    * @param string $fileName Name of file.
    * @param string $content Content of cache file.
    * @since 1.1.0
    * @param integer $lifeTimeInSeconds (optional) Cache life time in seconds. If zero life time unlimited. Default value <b>0</b>.
    * @return void.  
    */
    public static function CreateOrUpdate($fileName, $content, $lifeTimeInSeconds = 0)
    {
        $fullPath = self::GetPath($fileName);
        $directory = fsFunctions::GetDirectoryFromFullFilePath($fullPath);
        fsFunctions::CreateDirectory($directory);
        fsFileWorker::UpdateFile($fullPath, $content);
        if($lifeTimeInSeconds > 0) {
            fsFileWorker::UpdateFile($fullPath.self::$_timeSuffix, strtotime('+'.$lifeTimeInSeconds.' seconds'));
        }
    }
    
    /**
    * Check cache file life time status   
    * @since 1.1.0
    * @api    
    * @param string $fullPath Cache file full path.
    * @return boolean Result of checking.  
    */
    private static function _CheckLifeTime($fullPath)
    {
        $timeFile = $fullPath.self::$_timeSuffix;
        if(file_exists($timeFile)) {
            $validTill = (int)file_get_contents($timeFile);
            return $validTill == 0 || $validTill > time();
        }
        return true;
    }
    
    /**
    * Get text content of cache file   
    * @since 1.0.0
    * @api    
    * @param string $fileName Name of file.
    * @return string|null Cache file content or null if file not found.  
    */
    public static function GetText($fileName)
    {
        $fullPath = self::GetPath($fileName);
        if(!file_exists($fullPath) || !self::_CheckLifeTime($fullPath)) {
            return null;
        }
        return file_get_contents($fullPath);
    }
    
    /**
    * Get xml content of cache file   
    * @since 1.0.0
    * @api    
    * @param string $fileName Name of file.
    * @return SimpleXMLElement|null Cache file content or null if file not found.  
    */
    public static function GetXml($fileName)
    {
        $fullPath = self::GetPath($fileName);
        if(!file_exists($fullPath)|| !self::_CheckLifeTime($fullPath)) {
            return null;
        }
        return simplexml_load_file($fullPath);
    }
    
    /**
    * Get full path of cache file   
    * @since 1.0.0
    * @api    
    * @param string $fileName Name of file.
    * @return string Full file path.  
    */
    public static function GetPath($fileName)
    {
        return self::$_path.$fileName;
    }
    
    /**
    * Get full url of cache file   
    * @since 1.0.0
    * @api    
    * @param string $fileName Name of file.
    * @return string Full url of file.  
    */
    public static function GetUrl($fileName)
    {
        return self::$_url.$fileName;
    }
    
    /**
    * Delete all cache files  
    * @since 1.0.0
    * @api
    * @param string $prefix (optional) Delete file with this prefix. Default <b>empty string</b>.        
    * @return void.  
    */
    public static function Clear($prefix = '')
    {
        if($prefix === '') {
            fsFunctions::DeleteDirectory(self::$_path);
        } else {
            $files = fsFunctions::DirectoryInfo(self::$_path, true, true, array($prefix));
            foreach($files['NAMES'] as $name) {
                fsFunctions::DeleteFile($name);
            }
        }
    }
}