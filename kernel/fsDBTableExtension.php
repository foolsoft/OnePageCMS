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
  * @param array $arrIndexKey (optional) Fields for array key if result as array. If empty string generate standart numeric array index. Default <b>empty string</b>.  
  * @return array|boolean Result of query.      
  */
  public function GetAll($asArr = true, $useLinks = false, $order = array(), $arrIndexKey = '')
  {
    $this->Select('*', $useLinks)->Order($order);
    if (!$asArr) {
      return $this->Execute('', false);
    } else {
      return $this->ExecuteToArray('', $arrIndexKey);
    }
  }
  
  /**
  * Select rows with inline connections.   
  * @api
  * @since 1.1.0
  * @param array $result Array for result.
  * @param string $cacheFile Name of cache file.
  * @param string $fieldGet Name of field for select.
  * @param string $fieldSearch Name of field for search.
  * @param string $keySearch First value for search.
  * @param boolean $onlyFirstLevel (optional) Flag for search first table connection only. Default <b>false</b>.  
  * @return integer Count of elements in $result      
  */
  public function GetTree(&$result, $cacheFile, $fieldGet, $fieldSearch, $keySearch, $onlyFirstLevel = false)
  {
    $separator = ',';
    $cache = get_class().'_'.$cacheFile.'_'.$keySearch.'_'.($onlyFirstLevel ? '1' : '0');
    $cacheContent = fsCache::GetText($cache);
    if($cacheContent !== null) {
        $result = explode($separator, $cacheContent);
        if(time() < $result[0]) {
            array_shift($result);
            return count($result);
        }
    }
    $this->Select(array($fieldGet))->Where('`'.$fieldSearch.'` = "'.$keySearch.'"')->Execute('', false);
    $level = array();
    while($this->Next()) {
        $id = $this->result->$fieldGet;
        if(!in_array($id, $result)) {
            $level[] = $id;
            $result[] = $id;
        }
    }
    if($onlyFirstLevel !== true) {
        foreach($level as $id) {
            $this->GetTree($result, $cacheFile, $fieldGet, $fieldSearch, $id, false);
        }
    }
    fsCache::CreateOrUpdate($cache, strtotime('+1 day').$separator.implode($separator, $result));
    return count($result);
  }
  
  /**
  * Select one record from table.   
  * @api
  * @since 1.0.0
  * @param string $keyValue Value for selecting.
  * @param boolean $asArr (optional) Flag for getting result as array. Default <b>true</b>.
  * @param string $keyField (optional) Column name for $keyValue. If empty uses table primary key. Default empty string.
  * @return array Result of query.      
  */
  public function GetOne($keyValue, $asArr = true, $keyField = '')
  {
    if ('' == $keyField) {
      $keyField = $this->_struct->key;
    }
    $this->Select()->Where('`'.$keyField.'` = "'.$keyValue.'"')->Limit(1);
    if (!$asArr) {
      return $this->Execute();
    }
    $record = $this->ExecuteToArray();
    return count($record) == 1 ? $record[0] : null;
  }
  
  /**
  * Select COUNT of rows.   
  * @api
  * @since 1.0.0
  * @param string $where (optional) Where clause as string. If <b>empty string</b> get total row count. Default <b>empty string</b>.
  * @return integer Count of rows.      
  */
  public function GetCount($where = '')
  {
    $this->Execute('SELECT COUNT(*) as `c` FROM `'.$this->_struct->name.'`'.
        ($where === '' ? '' : ' WHERE '.$where));
    return $this->_result->mysqlRow['c'];
  }
  
  /**
  * Select values of some fields.   
  * @api
  * @since 1.0.0
  * @param string|array $field Table column name for value selecting.
  * @param string $keyValue Value for filtering.
  * @param string $key (optional) Column name for $keyValue. If <b>empty string</b> uses table primary key. Default <b>empty string</b>.
  * @return string|fsDBTableExtension Count of rows.      
  */
  public function GetField($field, $keyValue, $key = '')
  {
    if (empty($key)) {
      $key = $this->_struct->key;
    }
    if (empty($key)) {
      return null;
    }
    $this->Select(is_array($field) ? $field : array($field))
        ->Where('`'.$key.'` = "'.$keyValue.'"')->Execute();
    return is_array($field) ? $this : $this->_result->$field;
  }
  
    /**
  * Update one field in row.   
  * @api
  * @since 1.1.0
  * @param string $keyValue Value for selecting.
  * @param boolean $fieldName Name of column to change.
  * @param boolean $fieldValue New column value.
  * @param string $keyField (optional) Column name for $keyValue. If empty uses table primary key. Default empty string.
  * @return boolean Result of query.      
  */
  public function UpdateField($keyValue, $fieldName, $fieldValue, $keyField = '')
  {
    if ('' == $keyField) {
      $keyField = $this->_struct->key;
    }
    return $this->Update(array($fieldName), array($fieldValue))->Where('`'.$keyField.'` = "'.$keyValue.'"')->Execute();
  }
  
  /**
  * Delete rows.   
  * @api
  * @since 1.0.0
  * @param string $value Value to be delete.
  * @param string $key (optional) Column name for $value. If <b>empty string</b> uses table primary key. Default <b>empty string</b>.
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