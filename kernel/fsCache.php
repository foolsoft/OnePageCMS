<?php
/**
* Cache class
* @package fsKernel
*/
class fsCache
{
    /** @var string Path of cache folder */
    private static $_path = PATH_CACHE;
    
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
        $fullPath = self::$_path.$fileName;
        $directory = fsFunctions::GetDirectoryFromFullFilePath($fullPath);
        fsFunctions::CreateDirectory($directory);
        fsFileWorker::UpdateFile($fullPath, $content);
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