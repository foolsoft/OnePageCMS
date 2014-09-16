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
    
    /**
    * Create cache file    
    * @since 1.0.0
    * @api    
    * @param string $fileName Name of file.
    * @param string $content Content of cache file.
    * @return void.  
    */
    public static function CreateOrUpdate($fileName, $content)
    {
        $fullPath = self::GetPath($fileName);
        $directory = fsFunctions::GetDirectoryFromFullFilePath($fullPath);
        fsFunctions::CreateDirectory($directory);
        fsFileWorker::UpdateFile($fullPath, $content);
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
        if(!file_exists($fullPath)) {
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
        if(!file_exists($fullPath)) {
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
    * @return void.  
    */
    public static function Clear()
    {
        fsFunctions::DeleteDirectory(self::$_path);
    }
}
?>