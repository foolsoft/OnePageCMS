<?php
/**
* Base data structure
* @package fsKernel
*/
class fsStruct
{
    /** @var array Data lisst */   
    protected $fields = array();
    /** @var bollean Flag for allow inline data generation */ 
    protected $allowNull;
    /** @var string Value for not existing data */ 
    protected $_undefinedValue = '';

    /**
      * Print inner structure.    
      * @since 1.0.0
      * @api  
      * @param boolean $formated (optional) Flag for 'pre' tag using. Default <b>true</b>.   
      * @return void.  
      */
    public function ToScreen($formated = true) {
      if($formated !== true) {
        print_r($this->fields);
      } else {
        fsFunctions::FormatPrint($this->fields);
      }
    }

     /**
      * Get data fields names.    
      * @since 1.0.0
      * @api  
      * @param array $filter (optional) Data for exclude from result.   
      * @return array Fields names.  
      */
    public function GetStruct($filter = array())
    {
      $result = array();
      foreach ($this->fields as $field => $struct) {
        if (!in_array($field, $filter)) {
          $result[] = $field;
        }
      }
      return $result; 
    }

    /**
      * Get field value if exists or return default value.
      * @since 1.1.0
      * @api
      * @param string $field Field name.
      * @param mixed $default (optional) Return value if field not exist. Default <b>empty string</b>.
      * @return mixed Data.
      */
    public function TryGet($field, $default = '')
    {
        return $this->Exists($field) ? $this->fields[$field]['Value'] : $default;
    }

    /**
      * Get field number value if exists or return default value.
      * @since 1.1.0
      * @api
      * @param string $field Field name.
      * @param mixed $default (optional) Return value if field not exist or not number. Default <b>0</b>.
      * @return mixed Data.
      */
    public function TryGetNumber($field, $default = 0)
    {
        return $this->Exists($field, true) ? $this->fields[$field]['Value'] : $default;
    }

    /**
      * Get field number value if exists or return default value.
      * @since 1.1.0
      * @api
      * @param string $field Field name.
      * @param mixed $default (optional) Return value if field not exist or not positive number. Default <b>1</b>.
      * @return mixed Data.
      */
    public function TryGetPositiveNumber($field, $default = 1)
    {
        return $this->Exists($field, true) && $this->fields[$field]['Value'] > 0 ? $this->fields[$field]['Value'] : $default;
    }

    /**
      * Get field not empty value if exists or return default value.
      * @since 1.1.0
      * @api
      * @param string $field Field name.
      * @param mixed $default eturn value if field not exist or empty string.
      * @return mixed Data.
      */
    public function TryGetNotEmpty($field, $default)
    {
        return $this->$field != $this->_undefinedValue ? $this->fields[$field]['Value'] : $default;
    }
    
    /**
      * Clear not 'ReadOnly' inner data.    
      * @since 1.0.0
      * @api  
      * @param array $filter (optional) Data for exclude from result. Default <b>empty array</b>   
      * @return void.  
      */
    public function Clear($filter = array())
    {
      foreach ($this->fields as $field => $struct) {
        if ($struct['ReadOnly'] === false && !in_array($field, $filter)) {
          $this->fields[$field]['Value'] = '';
        }
      }
    }

    /**
      * Get inner data as associative array.    
      * @since 1.0.0
      * @api  
      * @return array Inner data.  
      */
    public function ToArray()
    {
      $arr = array();
      foreach ($this->fields as $field => $struct) {
        $arr[$field] = $struct['Value'];
      }
      return $arr;
    }

     /**
      * Check data existing.    
      * @since 1.0.0
      * @api  
      * @param string $field Name of data to be check.
      * @param boolean $checkNumeric Flag for additional check for numeric type. Default <b>false</b>.
      * @since 1.1.0
      * @param string $checkNumber Regex for additional check number value. Default <b>null</b>.
      * @return boolean Result of checking.  
      */
    public function Exists($field, $checkNumeric = false, $checkNumber = '\-?\d+(\.\d+)?')
    {
        if(isset($this->fields[$field]) &&  (!$checkNumeric || is_numeric($this->fields[$field]['Value']))) {
            return $checkNumeric ? preg_match('/^'.$checkNumber.'$/', $this->fields[$field]['Value']) : true;
        }
        return false;
    }

    /**
      * Delete inner data.    
      * @since 1.0.0
      * @api  
      * @param string|array $field (optional) Name (array of names) of data to be delete. If <b>empty string</b> delete all data. Default <b>empty string</b>.
      * @return void.  
      */
    public function Delete($field = '')
    {
      if ($field === '') {
        $this->fields = array();
      } else if (is_array($field)) { 
        foreach ($field as $f) {
          $this->Delete($f);
        }
      } else if ($this->Exists($field)) {
        unset($this->fields[$field]);
      }
    }

    /**
      * Constructor for fsStruct.    
      * @since 1.0.0
      * @api  
      * @param array $fields (optional) Inner data for structure.
      * @param boolean $allowNull (optional) Flag for dynamic structure. Default <b>false</b>.
      * @return void.  
      */
    function __construct($fields = null, $allowNull = false)
    {
      $this->allowNull = $allowNull;
      if (!is_array($fields)) {
        return;
      }
      foreach ($fields as $field => $struct) {
        $value = is_array($struct) && isset($struct['Value']) ? $struct['Value'] : $struct;
        $readOnly = is_array($struct) && 
                    isset($struct['ReadOnly']) &&
                    is_bool($struct['ReadOnly'])
                      ? $struct['ReadOnly']
                      : false;
        $this->fields[$field] = array('Value' => $value, 'ReadOnly' => $readOnly);
      }
    }

    public function __destruct()
    {
      $this->Delete();
    }

    public function __get($field)
    {
      if (isset($this->fields[$field])) {
        return $this->fields[$field]['Value'];
      } else {
        return $this->_undefinedValue;
      }
    }

    public function __set($field, $value)
    {
      $exists = $this->Exists($field);
      if ($exists && !$this->fields[$field]['ReadOnly']) {
        $this->fields[$field]['Value'] = $value;
      } else if (!$exists && $this->allowNull) {
        $this->fields[$field] = array('Value' => $value, 'ReadOnly' => false);
      } else {
        throw new Exception("Can't set property: " . __CLASS__ . "->".$field);
      }
    }
}