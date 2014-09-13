<?php
class Response
{
  private $_struct;
  public  $tag;
  
  public function __construct()
  {
    $responseConfig = array();
    $responseConfig['template'] = array('Value' => '');
    $responseConfig['message'] = array('Value' => '');
    $responseConfig['html'] = array('Value' => '');
    $responseConfig['redirect'] = array('Value' => '');
    $responseConfig['empty'] = array('Value' => false);
    $this->tag = new fsStruct(array(), true);
    $this->_struct = new fsStruct($responseConfig);
  }
  
  public function __set($attr, $value)
  {
    switch ($attr) {
      case 'template':
      case 'message':
      case 'html':
      case 'redirect':
      case 'empty':
        return $this->_struct->$attr = $value;
        
      default:
        if (isset($this->$attr)) {
          return $this->$attr = $value;
        } else {
          throw new ResponseException('Response invalid field ('.$attr.')');
        }
    }
  }
  
  public function __get($attr)
  {
    switch ($attr) {
      case 'message':
      case 'empty':
      case 'template':
      case 'html':
      case 'redirect':
        return $this->_struct->$attr;
        
      default:
         if (isset($this->$attr)) {
          return $this->$attr;
         } else {
          user_error('Response invalid field ('.$attr.')');
        }
    }
  }
}

class ResponseException extends Exception {}
?>