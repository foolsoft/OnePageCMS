<?php
class fsFileWorker
{
  private $_file    = '';
  private $_stream  = null;
  public  $eol      = PHP_EOL;
  public  $tab      = "    ";
  private $_tabCount = 0;
  
  private function _Tabs()
  {
    $string = '';
    for ($i = 0; $i < $this->_tabCount; ++$i) {
      $string .= $this->tab;
    }
    return $string;
  }
                             
  public static function UpdateFile($file, $newContent)
  {
    $f = new fsFileWorker($file, 'w+');
    $f->Write($newContent);
    $f->Close(); 
  }
  
  public function Write($string, $arrArgs = array())
  {
    fwrite($this->_stream, $this->_Tabs().fsFunctions::StringFormat($string, $arrArgs));   
  }
  public function WriteLine($string, $arrArgs = array())
  {
    $this->Write($string.$this->eol, $arrArgs);
  }
  public function WriteWithTabsAction($string, $arrArgs = array(), $tabCountAction = 0)
  {
    $this->Write($string, $arrArgs);
    $this->_tabCount += $tabCountAction;
    if ($this->_tabCount < 0) {
      $this->_tabCount = 0;
    }   
  }
  public function WriteLineWithTabsAction($string, $arrArgs = array(), $tabCountAction = 0)
  {
    $this->WriteWithTabsAction($string.$this->eol, $arrArgs, $tabCountAction);
  }
  public function WriteArray($arr)
  {
    if (!is_array($arr)) {
      return;
    }
    foreach ($arr as $value) {
      $this->WriteLine($value);
    }
  } 
  
  
  public function Close()
  {
    if ($this->_stream != null) {
      fclose($this->_stream);
      $this->_stream = null;  
    }
  }
  
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

?>