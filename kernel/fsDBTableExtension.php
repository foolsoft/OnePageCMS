<?php
/**
 * MySQL table object with extensions
 * @package fsKernel
 */  
class fsDBTableExtension extends fsDBTable
{
  /**
  * Select all record from table.   
  * @api
  * @since 1.0.0
  * @param boolean $asArr (optional) Flag for getting rows as array. Default <b>true</b>.
  * @param boolean $useLinks (optional) Flag for selecting foreign key values. Default <b>false</b>.
  * @param array $order (optional) Fields for order clause.
  * @return array|fsDBTableExtension Result of query.      
  */
  public function GetAll($asArr = true, $useLinks = false, $order = array())
  {
    $this->Select('*', $useLinks)->Order($order);
    if (!$asArr) {
      return $this->Execute('', false);
    } else {
      return $this->ExecuteToArray();
    }
  }
  
  /**
  * Select one record from table.   
  * @api
  * @since 1.0.0
  * @param string $keyValue Value for selecting.
  * @param boolean $asArr (optional) Flag for getting result as array. Default <b>true</b>.
  * @param string $keyField (optional) Column name for $keyValue. If empty uses table primary key. Default empty string.
  * @return array|fsDBTableExtension Result of query.      
  */
  public function GetOne($keyValue, $asArr = true, $keyField = '')
  {
    if (empty($keyField)) {
      $keyField = $this->_struct->key;
    }
    $this->Select()->Where('`'.$keyField.'` = "'.$keyValue.'"')->Limit(1);
    if (!$asArr) {
      return $this->Execute();
    } else {
      return $this->ExecuteToArray();
    }
  }
  
  /**
  * Select COUNT of rows.   
  * @api
  * @since 1.0.0
  * @param string|boolean $where (optional) Where clause as string. If <b>false</b> get total row count. Default <b>false</b>.
  * @return integer Count of rows.      
  */
  public function GetCount($where = false)
  {
    $this->Execute('SELECT COUNT(*) as `c` FROM `'.$this->_struct->name.'`'.
                  ($where === false
                   ? ''
                   : ' WHERE '.$where));
    return $this->_result->mysqlRow['c'];
  }
  
  /**
  * Select values of some fields.   
  * @api
  * @since 1.0.0
  * @param string|array $field Table column name for value selecting.
  * @param string $keyValue Value for filtering.
  * @param string $key (optional) Column name for $keyValue. If empty uses table primary key. Default empty string.
  * @return string|array Count of rows.      
  */
  public function GetField($field, $keyValue, $key = '')
  {
    if (empty($key)) {
      $key = $this->_struct->key;
    }
    if (empty($key)) {
      return false;
    }
    $fields = is_array($field) ? $field : array($field); 
    $result = $this->Select($fields)->Where('`'.$key.'` = "'.$keyValue.'"')->Execute();
    return is_array($field) ? $this : $this->_result->$field;
  }
  
  /**
  * Delete rows.   
  * @api
  * @since 1.0.0
  * @param string $value Value to be delete.
  * @param string $key (optional) Column name for $value. If empty uses table primary key. Default empty string.
  * @return boolean Result of deleting.      
  */
  public function DeleteBy($value, $key = '')
  {
    if (empty($key)) {
      $key = $this->_struct->key;
    }
    if (empty($key)) {
      return false;
    }
    return $this->Delete()->Where('`'.$key.'` = "'.$value.'"')->Execute();
  }
  
  public function __destruct()
  {
    parent::__destruct();
  }
}
?>