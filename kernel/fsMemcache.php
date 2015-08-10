<?php
/**
* Memcache helper
* @package fsKernel
*/
class fsMemcache
{
    private $_connection = null;
    
    public function __construct($host = 'localhost', $port = 11211) 
    {
        $this->_connection = new Memcache();
        $this->_connection->connect($host, $port) or die('Could not connect to memcahce');  
    }
    
    public function __destruct()
    {
        $this->_connection->close();
    }
    
    /**
      * Get variable from memcache.   
      * @api
      * @since 1.1.0
      * @param string|array $attr String names of cached value. 
      * @return mixed Cached value.     
      */
    public function Get($attr) 
    {
        return $this->_connection->get($attr);
    }
    
    /**
      * Put value into memcache.   
      * @api
      * @since 1.1.0
      * @param string $attr String name of cached value. 
      * @param mixed $value Value for cache. 
      * @param integer $lifeTime (optional) Lifetime of cache in seconds. If zero unlimited time. Default <b>0</b>. 
      * @param boolean $zip (optional) Zip cache value. Default <b>false</b>. 
      */
    public function Set($attr, $value, $lifeTime = 0, $zip = false) 
    {
        return $this->_connection->set($attr, $value, $zip, $lifeTime);
    }
    
    /**
      * Replace value in memcache.   
      * @api
      * @since 1.1.0
      * @param string $attr String name of cached value. 
      * @param mixed $value New value for cache. 
      * @param integer $lifeTime (optional) Lifetime of cache in seconds. If zero unlimited time. Default <b>0</b>. 
      * @param boolean $zip (optional) Zip cache value. Default <b>false</b>. 
      */
    public function Replace($attr, $value, $lifeTime = 0, $zip = false) 
    {
        return $this->_connection->replace($attr, $value, $zip, $lifeTime);
    }
    
    /**
      * Delete value in memcache.   
      * @api
      * @since 1.1.0
      * @param string $attr String name of cached value. 
      */
    public function Delete($attr) 
    {
        return $this->_connection->delete($attr);
    }
    
}  