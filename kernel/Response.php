<?php
/**
* Cache class
* @package fsKernel
* @property string $template Template for page generation
* @property string $message Message for controller
* @property string $html Response answer as HTML code
* @property string $redirect Response answer as redirect 
* @property string $empty Flag for skip page generation in controller  
*/
class Response
{
    /** @var fsStruct Data for controoler */
    protected $_struct;
    /** @var fsStruct Response data */
    public  $tag;
  
    /**
    * Response constructor  
    * @api
    * @since 1.0.0
    * @return void      
    */
    public function __construct()
    {
        $this->tag = new fsStruct(array(), true);
        $this->_struct = new fsStruct(array(
            'template' => array('Value' => ''),
            'message' => array('Value' => ''),
            'html' => array('Value' => ''),
            'redirect' => array('Value' => ''),
            'code' => array('Value' => 200),
            'empty' => array('Value' => false),
        ));
    }
  
    public function __set($attr, $value)
    {
        switch ($attr) {
            case 'template':
            case 'message':
            case 'html':
            case 'redirect':
            case 'code':
            case 'empty':
                return $this->_struct->$attr = $value;

            default:
                if (isset($this->$attr)) {
                    return $this->$attr = $value;
                } else {
                    throw new Exception('Response invalid field ('.$attr.')');
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
            case 'code':
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