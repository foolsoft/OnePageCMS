<?php
/**
* Class for working with files
* @package fsKernel
*/
class fsFileWorker
{
  /** @var string Work file path */  
  private $_file    = '';
  /** @var stream|boolean Work file stream */  
  private $_stream  = null;
  /** @var string New line char */  
  public  $eol      = PHP_EOL;
  /** @var string Tabulation string */  
  public  $tab      = "    ";
  /** @var integer Current tabulation */  
  private $_tabCount = 0;
  
  /**
    * Generate tabulation string for current state.    
    * @since 1.0.0
    * @api    
    * @return string Tabulation string.  
    */
  private function _Tabs()
  {
    $string = '';
    for ($i = 0; $i < $this->_tabCount; ++$i) {
      $string .= $this->tab;
    }
    return $string;
  }
  
  /**
    * Create new file or update old file with full rewrite.    
    * @since 1.0.0
    * @api    
    * @param string $file File path.
    * @param string $newContent File content.  
    * @return void.  
    */
  public static function UpdateFile($file, $newContent)
  {
    $f = new fsFileWorker($file, 'w+');
    $f->Write($newContent);
    $f->Close(); 
  }
  
  /**
    * Create new file or append new data in existing file.    
    * @since 1.0.0
    * @api    
    * @param string $file File path.
    * @param string $newContent String to be append.  
    * @return void.  
    */
  public static function AppendFile($file, $newContent)
  {
    $f = new fsFileWorker($file, 'a+');
    $f->Write($newContent);
    $f->Close(); 
  }
  
  /**
    * Write string to file.    
    * @since 1.0.0
    * @api    
    * @param string $string String template for writing.
    * @param array $arrArgs (optional) Data for $string.  
    * @return void.  
    */
  public function Write($string, $arrArgs = array())
  {
    fwrite($this->_stream, $this->_Tabs().fsFunctions::StringFormat($string, $arrArgs));   
  }
  /**
    * Write string to file with new line character in end.    
    * @since 1.0.0
    * @api    
    * @param string $string String template for writing.
    * @param array $arrArgs (optional) Data for $string.  
    * @return void.  
    */
  public function WriteLine($string, $arrArgs = array())
  {
    $this->Write($string.$this->eol, $arrArgs);
  }
  /**
    * Write string to file with tabulation .    
    * @since 1.0.0
    * @api    
    * @param string $string String template for writing.
    * @param array $arrArgs (optional) Data for $string.  
    * @param integer $tabCountAction (optional) Tabulation count. Default <b>0</b>. 
    * @return void.  
    */
  public function WriteWithTabsAction($string, $arrArgs = array(), $tabCountAction = 0)
  {
    $this->Write($string, $arrArgs);
    $this->_tabCount += $tabCountAction;
    if ($this->_tabCount < 0) {
      $this->_tabCount = 0;
    }   
  }
  /**
    * Write string to file with tabulation and new line character in end.    
    * @since 1.0.0
    * @api    
    * @param string $string String template for writing.
    * @param array $arrArgs (optional) Data for $string.  
    * @param integer $tabCountAction (optional) Tabulation count. Default <b>0</b>. 
    * @return void.  
    */
  public function WriteLineWithTabsAction($string, $arrArgs = array(), $tabCountAction = 0)
  {
    $this->WriteWithTabsAction($string.$this->eol, $arrArgs, $tabCountAction);
  }
  /**
    * Write array data one by line.    
    * @since 1.0.0
    * @api    
    * @param array $arr String templates for writing.
    * @param array $arrArgs (optional) Data for strings.  
    * @return void.  
    */
  public function WriteArray($arr, $arrArgs = array())
  {
    if (!is_array($arr)) {
      return;
    }
    foreach ($arr as $value) {
      $this->WriteLine($value, $arrArgs);
    }
  } 
  
  /**
    * Close file stream.    
    * @since 1.0.0
    * @api    
    * @return void.  
    */
  public function Close()
  {
    if ($this->_stream != null) {
      fclose($this->_stream);
      $this->_stream = null;  
    }
  }
  
  /**
  * fsFileWorker constructor  
  * @api
  * @since 1.0.0
  * @param string $file File path.
  * @param string $mode Work mode. 
  * @return void      
  */
  public function __construct($file, $mode)
  {
    $this->_file = $file;
    $this->_stream = fopen($file, $mode);
  }
  
  public function __destruct()
  {
    $this->Close();
  }
}