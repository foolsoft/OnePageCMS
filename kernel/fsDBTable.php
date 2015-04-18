<?php
/**
 * MySQL table object
 * @package fsKernel
 * @property string $current Get current row primary key value or execute select query for new value
 * @property-read string $key Table primary key
 * @property-read integer $insertedId Last inserted id 
 * @property-read integer $affectedRows Count of affected rows in last query
 * @property-read fsStruct $result Query result of SELECT operation
 * @property-read string $sql Current MySQL query
 * @property-read array $columns Table columns names
 * @property-read integer $columnsCount Count of table columns
 */  
class fsDBTable
{
  /** @var boolean Can not have primary key */
  protected $_noPrimaryKey = false;
  
  /** @var integer Table columns count */
  protected $_columnsCount = 0;
  
  /** @var fsDBsettings MySQL connection settings */
  protected $_config       = null;
  
  /** @var fsStruct Table structure */
  protected $_struct       = null;
  
  /** @var fsStruct Query structure */
  protected $_query        = null;
  
  /** @var fsStruct Query result structure */
  protected $_result       = null;
  
  /** @var array Table columns names */
  protected $_columns      = array();
  
  /** @var array Array of queries */
  protected $_queryStack  = array();
  
  /** @var array Table columns types */
  private   $_columnsType  = array();
  
  /** @var array Foreign keys */
  private   $_join         = array();
  
  
  /**
  * Init function for overriding.   
  * @api
  * @since 1.1.0
  * @return void.      
  */
  protected function _Init()
  {
  }
  
  /**
  * Get table columns
  * @api
  * @since 1.1.0
  * @return array Columns of table.      
  */
  public function GetColumns()
  {
    return $this->_columns;
  }
  
  /**
  * Create table row as array
  * @api
  * @since 1.1.0
  * @param $valuesArray (optional) Values of specific columns. Default <b>empty array</b>.
  * @return array Table row as array.      
  */
  public function CreateRow($valuesArray = array())
  {
    if(!is_array($valuesArray)) {
        $valuesArray = array();
    }
    $result = array();
    foreach($this->_columns as $column) {
        $result[$column] = isset($valuesArray[$column]) ? $valuesArray[$column] : '';
    }
    return $result;
  }
  
  /**
  * Add query inside stack
  * @api
  * @since 1.1.0
  * @return void.      
  */
  protected function AddStack($query)
  {
      if($query !== '' && isset($query[6]) && strtolower(substr($query, 0, 6)) != 'select') {
        $this->_queryStack[] = $query;
        return;
      }
      throw Exception('Cannot add query inside stack');
  }
  
  /**
  * Execute queries inside stack
  * @api
  * @since 1.1.0
  * @return void.      
  */
  protected function ExecuteStack()
  {
      if(count($this->_queryStack) > 0) {
        $this->Execute(implode(';', $this->_queryStack));
        $this->_queryStack = array();
      }
  }
  
  /**
  * Conver array of where clause to string.   
  * @api
  * @since 1.0.0
  * @param array $arr Where clause as array
  * @param string|boolean $prefix (optional) Table name for field. If <b>false</b> it is current table name. Default <b>false</b>.     
  * @return string Where clause.      
  */
  protected function _WhereArrayToString($arr, $prefix = false)
  {
    if ($prefix == false) {
      $prefix = $this->_struct->name;
    }
    $result = '';
    $logic = '';
    $count_arr = count($arr);
    for ($i = 0; $i < $count_arr; ++$i) {
      if (!isset($arr[$i])) {
        continue;
      }
      if (!empty($result)) {
          $result .= ' '.$logic.' ';
      }
      $str = ''; $logic = 'AND';
      if (isset($arr[$i]['logic']) && fsValidator::Match($arr[$i]['logic'], '(AND|OR)')) {
        $logic = $arr[$i]['logic'];
      }
      if (isset($arr[$i][0])) {
        $str = ' ('.$this->_WhereArrayToString($arr[$i]).') ';
      } else {
        $key = '=';
        if (isset($arr[$i]['key']) &&  fsValidator::Match($arr[$i]['key'], '(=|<|>|>=|<=|\!=)')) {
          $key = $arr[$i]['key'];
        }  
        foreach($arr[$i] as $field => $value) {
          if ($field != 'key' && $field != 'logic') {
            $str = '(`'.$prefix.'`.`'.$field.'` '.$key.' "'.$value.'")';
            break;
          }
        }
        if ('' == $str)
          continue;
      }
      $result .= $str;
    }
    return $result;
  }
  
  /**
  * Change inner structure for new resul row.   
  * @api
  * @since 1.0.0
  * @param mixed $mysql_fetch_assoc_result Query result row
  * @return boolean Result of updating.      
  */
  protected function _UpdateResult($mysql_fetch_assoc_result)
  {
    if (!is_array($mysql_fetch_assoc_result)) {
      return false;
    }
    $this->_result->Clear(array('mysqlResult'));
    $this->_result->mysqlRow = $mysql_fetch_assoc_result;
    foreach  ($mysql_fetch_assoc_result as $f => $value) {
    	$f = strtolower($f);
    	if (isset($this->$f)) {
    	 $this->_result->$f = $value;
    	}
    }
    if (!empty($this->_struct->key)) {
      $kf = $this->_struct->key;
      $this->_struct->current = $this->$kf;
    }
    return true;
  }
  
  /**
  * Check if all columns values are correct.   
  * @api
  * @since 1.0.0
  * @param boolean $field Column name. If <b>false</b> check all columns. Default <b>false</b>.
  * @param boolean $value Column value. If <b>false</b> check all values. Default <b>false</b>.  
  * @return string Name of table column with wrong value.      
  */
  protected function _Validate($field = false, $value = false)
  {
    if ($field === false) {
      for($i = 0; $i < $this->_columnsCount; ++$i) {
        $f = $this->_columns[$i];
        $r = $this->_Validate($f, $this->$f);
        if ($r != '')
          return $f;
      }
      return '';  
    }
    if (!isset($this->_columnsType[$field])) {
      return $field;
    }
    if ($this->_columnsType[$field]['nocheck'] || $this->$field == false) {
      return '';
    }
    switch($this->_columnsType[$field]['type']) {
        case 'text':
          break;
        
        case 'varchar':
          if (!fsValidator::Check($value, 'LENGTH', array($this->_columnsType[$field]['attr']))) {
            return $field;  
          }
          break;
          
        case 'enum':
          if (!fsValidator::Check($value, 'ENUM', array($this->_columnsType[$field]['attr']))) {
            return $field;  
          }
          break;
          
        case 'float':
          if (!fsValidator::Check($value, 'FNUMERIC')) {
            return $field;
          }
          break;
          
        case 'tinyint':
        case 'int':
        case 'smallint':
        case 'bigint':
          if (!fsValidator::Check($value, 'NUMERIC')) {
            return $field;
          }
          break;
          
        case 'timestamp':
        case 'date':
        case 'datetime':
          if (!fsValidator::Check($value, 'TIMEDATE')) {
            return $field;
          }
          break;
          
        default:
          return $field;
    }
    return '';  
  }
  
  /**
  * Clear inner query structure.   
  * @api
  * @since 1.0.0
  * @return void     
  */
  protected function _ClearAfterQuery()
  {
    $this->_query->where = false;
    $this->_query->group = false;
    $this->_query->order = false;
    $this->_query->order_desc = false;
    $this->_query->limit = false;
    $this->_query->selectLinks = false;
    $this->_query->action = '';
  }
  
  /**
  * Generate MySQL query using inner query structure.   
  * @api
  * @since 1.0.0
  * @return string Query string.      
  */
  public function GenerateQuery()
  {
    $this->_query->sql = '';
    switch($this->_query->action) {
      case 'drop':
        $this->_query->sql = 'DROP TABLE `'.$this->_struct->name.'`';
        break;
      case 'delete':
        $this->_query->sql = 'DELETE FROM `'.$this->_struct->name.'`';
        break;
      case 'update':
        $this->_query->sql = 'UPDATE `'.$this->_struct->name.'` SET ';
        $this->_query->sql .= $this->_query->update;
        break;
      case 'insert':
        $values = '';
        $this->_query->sql = 'INSERT INTO `'.$this->_struct->name.'` (';
        for ($i = 0; $i < $this->_columnsCount; ++$i) {
          $field = $this->_columns[$i];
          if ($this->$field === false) {
            continue;
          }
          $suffix = ($i == $this->_columnsCount - 1 ? ' ' : ', ');
          $this->_query->sql .= '`'.$this->_columns[$i].'`'.$suffix;
          $values .= '"'.$this->_struct->db->Escape($this->$field).'"'.$suffix;
        }
        if (substr($values, -2) == ', ') {
          $this->_query->sql = substr($this->_query->sql, 0, strlen($this->_query->sql) - 2);
          $values = substr($values, 0, strlen($values) - 2);
        }
        $this->_query->sql .= ') VALUES ('.$values.')';
        break;
      case 'select':
        $f = '';
        if ($this->_query->select === '*') {
          for ($i = 0; $i < $this->_columnsCount; ++$i) {
            $f .= '`'.$this->_struct->name.'`.`'.$this->_columns[$i].'`'.
                  ($i == $this->_columnsCount - 1 ? ' ' : ', ');
          }
        } else {
          $cs = count($this->_query->select);
          for ($i = 0; $i < $cs; ++$i) {
            $f .= '`'.$this->_struct->name.'`.`'.$this->_query->select[$i].'`'.
                  ($i == $cs - 1 ? ' ' : ', ');
          }
        }
        $links = '';
        if ($this->_query->selectLinks) {
          $cj = count($this->_join);
          for ($i = 0; $i < $cj; ++$i) {
            $links .= ' LEFT JOIN `'.$this->_join[$i]['Table'].'` ON `'.$this->_struct->name.
                      "`.`".$this->_join[$i]['MyField'].'` = `'.$this->_join[$i]['Table'].'`.`'.
                      $this->_join[$i]['On'].'` '; 
            $f .= ', `'.$this->_join[$i]['Table'].'`.`'.$this->_join[$i]['View'].'` AS `link_'.
                  $this->_join[$i]['MyField'].'` ';
          }
        }
        $this->_query->sql = 'SELECT '.$f.' FROM `'.$this->_struct->name.'` '.$links;
        break;
      default:
        return '';
    }
    if ($this->_query->where != false || $this->_struct->current != false) {
      $this->_query->sql .= ' WHERE ';
      if ($this->_struct->current != false && $this->_query->action != 'select') {
      	$this->_query->sql .= '(`'.$this->_struct->name.'`.`'.$this->_struct->key.'` = "'.
                             $this->_struct->current.'") ';
      	$this->_query->sql .= ($this->_query->where == false ? '' : 'AND ');
      }
	    if ($this->_query->where != false) {
	      if (is_array($this->_query->where))
	        $this->_query->sql .= $this->_WhereArrayToString($this->_query->where);
	      else
	        $this->_query->sql .= $this->_query->where;
      }
    }
    if (is_array($this->_query->group)) {
      $this->_query->sql .= ' GROUP BY ';
      $temp = count($this->_query->group);
      for ($i = 0; $i < $temp; ++$i) {
        $this->_query->sql .= '`'.$this->_query->group[$i].'`'.($i == $temp - 1 ? ' ' : ', ');
      }
    }
    if (is_array($this->_query->order) && count($this->_query->order) > 0) {
      $this->_query->sql .= ' ORDER BY ';    
      $temp = count($this->_query->order);
      for ($i = 0; $i < $temp; ++$i) {
        $this->_query->sql .= '`'.$this->_query->order[$i].'` '.
                (isset($this->_query->order_desc[$i]) && $this->_query->order_desc[$i] === true ? 'DESC' : 'ASC').
                ($i == $temp - 1 ? ' ' : ', ');
      }
    }
    if ($this->_query->action == 'select' && $this->_query->limit != false) {
      $this->_query->sql .= ' LIMIT '. $this->_query->limit;
    }
    return $this->_query->sql;
  }

  /**
  * Generate SELECT query.   
  * @api
  * @since 1.0.0
  * @param string|array $fileds (optional) Columns for select. As string value understands only '*' value. Default <b>*</b>.
  * @param boolean $useLinks (optional) <b>True</b> will will join tables by existing foreign keys. Default <b>false</b>.  
  * @return fDBTable Current object.      
  */
  public function Select($fileds = '*', $useLinks = false)
  {
    if (!is_array($fileds) && $fileds !== '*') { 
      throw new Exception('Bad parameter: select');
    }
    $this->_query->select = $fileds;
    $this->_query->selectLinks = $useLinks;
    $this->_query->action = 'select';
    return $this;
  }
  
  /**
  * Generate INSERT query.   
  * @api
  * @since 1.0.0
  * @return fDBTable Current object.      
  */
  public function Insert()
  {
    $this->_query->action = 'insert';
    return $this;
  }
  
  /**
  * Generate and execute UPDATE query for current row if it was selected before.   
  * @api
  * @since 1.0.0
  * @return fDBTable Current object.      
  */
  public function Save()
  {
    if ($this->_struct->current == false) {
      return false;
    }
    $values = array();
    for ($i = 0; $i < $this->_columnsCount; ++$i) {
    	$field = $this->_columns[$i];
        $values[] = $this->$field;
    }
    $this->Update($this->_columns, $values);
    return $this->Execute();
  }
  
  /**
  * Generate UPDATE query.   
  * @api
  * @since 1.0.0
  * @param array $fields Columns for update.
  * @param array $values New values for columns.  
  * @return fDBTable Current object.      
  */
  public function Update($fields, $values)
  {
    $arr_len = count($fields);
    if (!is_array($fields) || !is_array($values) || $arr_len != count($values)) { 
      throw new Exception('Bad parameter: update');
    }
    $this->_query->action = 'update';
    $this->_query->update = '';
    for ($i = 0; $i < $arr_len; ++$i) {
      $vr = $this->_Validate($fields[$i], $values[$i]);
      if (!empty($vr)) {
        throw new Exception('Update error: '.$vr);
      }
      $this->_query->update .= '`'.$this->_struct->name.'`.`'.$fields[$i].'` = "'.
                              $this->_struct->db->Escape($values[$i]).'"'.
                              ($i == $arr_len - 1 ? ' ' : ', ');
    }
    return $this;
  }
  
  /**
  * Generate DELETE query.   
  * @api
  * @since 1.0.0
  * @return fDBTable Current object.      
  */
  public function Delete()
  {
    $this->_query->action = 'delete';
    return $this;
  }

  /**
  * Generate DROP query.   
  * @api
  * @since 1.0.0
  * @return fDBTable Current object.      
  */ 
  public function Drop()
  {
    $this->_query->action = 'drop';
    return $this;
  }

  /**
  * Generate WHERE clause.   
  * @api
  * @since 1.0.0
  * @param string|array $clause Query clause.
  * @example If string: standart of MySQL. If array:
    $array (field => value, //requared
       key => =|<|>|<=|>>|!=, //default =
       logic => OR|AND //default AND
    )  
    $clause = array (
          array(field1 => value1),
          array(array(field2 => value2, logic => OR, key => >),
                array(field3 => value3)
            ),
          $array
        ) 
    result: clause = (field1 = value1) AND ((field2 > value2) OR (field3 = value3))    
  * @return fDBTable Current object.      
  */
  public function Where($clause)
  {
    if ($this->_query->action == 'insert') {
      throw new Exception('"WHERE" clause impossible for "Insert" command');
    }
    $this->_query->where = $clause;
    return $this;
  }
  
  /**
  * Generate GROUP clause.   
  * @api
  * @since 1.0.0
  * @param array $fields Columns for grouping.
  * @return fDBTable Current object.      
  */
  public function Group($fields)
  {
    if (!is_array($fields)) { 
      throw new Exception('Bad parameter: group');
    }
    if ($this->_query->action != 'select') {
      throw new Exception('"Select" command is not found');
    }
    $this->_query->group = $fields;
    return $this;
  }
  
  /**
  * Generate ORDER clause.   
  * @api
  * @since 1.0.0
  * @param array $fields Columns for order.
  * @param array $desc (optional) Order type for each column. Default <b>array()</b>.
  * @return fDBTable Current object.      
  */
  public function Order($fields, $desc = array())
  {
    if (!is_array($fields) || !is_array($desc)) { 
      throw new Exception('Bad parameter: order');
    }
    if ($this->_query->action != 'select') {
      throw new Exception('"Select" command is not found');
    }
    $this->_query->order = $fields;
    $this->_query->order_desc = $desc;
    return $this;
  }
  
  /**
  * Generate LIMIT clause.   
  * @api
  * @since 1.0.0
  * @param integer $count Rows count for select.
  * @param integer $start (optional) Start row number. Default <b>0</b>.
  * @return fDBTable Current object.      
  */
  public function Limit($count, $start = 0)
  {
    if (!is_numeric($count) || !is_numeric($start) || $count < 0) { 
      throw new Exception('Bad parameter: limit');
    }
    $this->_query->limit = $start.', '.$count;
    return $this;
  }

  /**
  * Check is some value unique in table.   
  * @api
  * @since 1.0.0
  * @param string $value Value for checking.
  * @param string $key (optional) Table key for SELECT query. If empty uses table primary key column. Default empty string.
  * @param string $findedFieldValue (optional) Field value for returning if $value not unique. If <b>empty string</b> and $value not unique function will return find row. Default <b>empty string</b>
  * @return mixed Result of checking.      
  */
  public function IsUnique($value, $key = '', $findedFieldValue = '')
  {
    $return = false;
    if ($key == '') {
      $key = $this->_struct->key;
    }
    if ($key == '') {
      return $return;
    }
    $this->Select()->Where('CAST(`'.$key.'` as CHAR) = "'.$value.'"')->Execute();
    if ($findedFieldValue !== '' && $this->_result->$key != '') {
      $return = $this->_result->$findedFieldValue;
    }
    return ($this->_result->$key == '') ? true : $return;
  }
  
  /**
  * Get value of foreign key after Select function with $useLinks = true.   
  * @api
  * @since 1.0.0
  * @param string $value Value for checking.
  * @param string $key (optional) Table key for SELECT query. If empty uses table primary key column. Default empty string.
  * @param string|boolean $findedFieldValue (optional) Field value for returning if $value not unique. If <b>false</b> and $value not unique function will return find row. Default <b>false</b> 
  * @return string Value of foreign key.      
  */
  public function Link($field)
  {
    if (!in_array($field, $this->_columns)) {
      throw new Exception('Ivalid field: '.$field);
    }
    if (!isset($this->_result->mysqlRow['link_'.$field])) {
      throw new Exception('No link found for field: '.$field);
    }
    return $this->_result->mysqlRow['link_'.$field];
  }
  
  /**
  * Get next row in query result list   
  * @api
  * @since 1.0.0
  * @param integer $step (optional) Сount of steps. Default <b>1</b>.
  * @return boolean Result of updating.      
  */ 
  public function Next($step = 1)
  {
    $counter = 0;
    while ($counter < $step && 
           $row = $this->_result->mysqlResult->fetch_assoc()) {
      ++$counter;
    }
    return $this->_UpdateResult($row);
  }

  /**
  * Execute query   
  * @api
  * @since 1.0.0
  * @param string $query (optional) MySQL query string. If empty will generate query from inner structure. Default empty string.
  * @param string $arrayKeyField (optional) Result array index field. If empty will generate standart array indexes from zero.  Default empty string. 
  * @return array Result of query.      
  */ 
  public function ExecuteToArray($query = '', $arrayKeyField = '')
  {
    $res = array();
    $query = trim($query);
    if ($this->_query->action != 'select' && ($query != '' && substr(strtolower($query), 0, 6) != 'select')) {
      return $res;
    }
    $this->Execute($query, false);
    while ($this->Next()) {
      $row = array();
      foreach ($this->_result->mysqlRow as $name => $value) {
        $row[$name] = $value;
      }
      if(false == $arrayKeyField) {
        $res[] = $row;
      } else {
        $res[$row[$arrayKeyField]] = $row;
        unset($res[$row[$arrayKeyField]][$arrayKeyField]);
      } 
    }
    return $res;
  }

  /**
  * Execute query   
  * @api
  * @since 1.0.0
  * @param string $query (optional) MySQL query string. If empty will generate query from inner structure. Default empty string.
  * @param boolean $next (optional) If <b>true</b> and query type is SELECT will automatically get first row of result. Default <b>true</b>.
  * @return boolean Result of query.      
  */
  public function Execute($query = '', $next = true)
  {
    if ($this->_query->action == 'insert') { 
      $res = $this->_Validate();
      if (!empty($res)) {
        throw new Exception('Invalid query arguments: '.$res.'!');
      }
    }
    $this->GenerateQuery();
    if ($query === '') {
      $query = $this->_query->sql;
    } else { 
      $this->_query->sql = $query;
    }
    $this->_result->Clear();
    $this->_result->mysqlResult = $this->_struct->db->Query($query);
    if (!$this->_result->mysqlResult) { 
        $this->_struct->db->Close();
        die('Error in query: '.$this->_query->sql);
    }
    if ($next && 
        ($this->_query->action == 'select' || 
         strtolower(substr($this->_query->sql, 0, 6)) == 'select')) {
      $row = $this->_result->mysqlResult->fetch_assoc();
      $this->_UpdateResult($row);
    }
    $this->_ClearAfterQuery();
    return true;
  }


  /**
  * Get type of table column   
  * @api
  * @since 1.0.0
  * @param string $field Column name.
  * @return string Column type.      
  */
  public function GetType($field)
  {
    if (isset($this->_columnsType[$field])) {
      return $this->_columnsType[$field]['type'];
    }
    return '';
  }
  
  public function __get($field)
  {
    switch($field) {
      case 'key': 
        return $this->_struct->key;
      
      case 'insertedId': 
        return $this->_struct->db->InsertedId();
        
      case 'affectedRows': 
        return $this->_struct->db->AffectedRows();
        
      case 'result': 
        return $this->_result;
      
      case 'sql':
        return $this->_query->sql;
      
      case 'current': 
        return $this->_struct->current;
      
      case 'columns': 
        return $this->_columns;
      
      case 'columnsCount':
        return $this->_columnsCount;
        
      default: 
        throw new Exception('Can\'t get property: '.$field);
    }
  }

  public function __set($field, $value)
  {
    if (in_array($field, $this->_columns)) {
      $this->$field = $value;
      return;
    }
    switch($field) {
      case 'current':
        if ($value != false || $value !== $this->_struct->current) {
          $this->Select('*', true)
            ->Where('`'.$this->_struct->name.'`.`'.$this->_struct->key.'` = "'.$value.'"')
            ->Execute();
        }
        for ($i = 0; $i < $this->_columnsCount; ++$i) {
          $field = $this->_columns[$i];
          $this->$field = $value === false ? false : $this->_result->$field;
        }
        $this->_struct->current = $value;
      	break;
      	
      default: 
        throw new Exception('Can\'t set property: '.$field);
    }
  }
  
  public function __destruct() 
  { 
  }
  
  /**
  * fsDBTable constructor.   
  * @api
  * @since 1.0.0
  * @param string|boolean $tableName (optional) Table name for instance creating. If <b>false</b> will use class  name (for child classes). Default <b>false</b>.
  * @param boolean $fromCache (optional) Flag for creating instance from cache file. Default <b>true</b>.
  * @param boolean $createCache (optional) Flag for creating cache of table. Default <b>true</b>.
  * @return void      
  */
  public function __construct($tableName = false, $fromCache = true, $createCache = true)
  {
    $this->_Init();
    $className = fsConfig::GetInstance('db_prefix').(!$tableName ? get_class($this) : $tableName);
    if ($fromCache && class_exists('_struct_'.$className)) {
      $className_ceche = '_struct_'.$className; 
      $this->_query = call_user_func(array($className_ceche, 'GetInstance'))->Get('query');
      $this->_result = call_user_func(array($className_ceche, 'GetInstance'))->Get('result');
      $this->_struct = call_user_func(array($className_ceche, 'GetInstance'))->Get('struct');
      $this->_columns = call_user_func(array($className_ceche, 'GetInstance'))->Get('columns');
      $this->_columnsType = call_user_func(array($className_ceche, 'GetInstance'))->Get('columnsType');
      $this->_columnsCount = call_user_func(array($className_ceche, 'GetInstance'))->Get('columnsCount');
      $this->_join = call_user_func(array($className_ceche, 'GetInstance'))->Get('join');
      foreach($this->_columns as $c) {
        $this->$c = call_user_func(array($className_ceche, 'GetInstance'))->Get($c);
        $this->_result->$c = '';
        $this->$c = false;
      }
      $this->_struct->current = false;
      return;
    } 
    $resultConfig = array();
    $structConfig = array();
    $queryConfig = array();
    $db = new fsDBconnection();

    $queryConfig['sql'] = array('Value' => '');
    $queryConfig['action'] = array('Value' => '');
    $queryConfig['select'] = array('Value' => '');
    $queryConfig['selectLinks'] = array('Value' => false);
    $queryConfig['update'] = array('Value' => '');
    $queryConfig['group'] = array('Value' => false);
    $queryConfig['order'] = array('Value' => false);
    $queryConfig['where'] = array('Value' => false);
    $queryConfig['order_desc'] = array('Value' => false);
    $queryConfig['limit'] = array('Value' => '');

    $structConfig['current'] = array('Value' => false, 'ReadOnly' => false);
    $structConfig['name'] = array('Value' => $className, 'ReadOnly' => true);
    $structConfig['db'] = array('Value' => new fsDBconnection(), 'ReadOnly' => true);

    $result = $db->Query('SHOW FULL COLUMNS FROM `'.$className.'`');
    if (!$result) {
      $result = $db->Query('SHOW FULL COLUMNS FROM `'.fsConfig::GetInstance('db_prefix').$className.'`');
      if (!$result) { 
        throw new Exception('Command field: "SHOW COLUMNS" ('.$className.')');
      }
    }
    $resultConfig['mysqlResult'] = array('Value' => null);
    $resultConfig['mysqlRow'] = array('Value' => null);
    while ($row = $result->fetch_assoc()) {
      $inlowercase = strtolower($row['Field']);
      if(!empty($row['Comment'])) {
        $conf = explode('#', $row['Comment']);
        $conf = explode(':', $conf[0]);
        $conf_len = count($conf);
        if($conf_len > 1) {
          $key_f = 'id';
          if ($conf_len >= 3) {
            $key_f = $conf[2];
          }
          $this->_join[] = array('Table' => $conf[0],
                                'On' => $key_f,
                                'View' => $conf[1],
                                'MyField' => $row['Field']
                              );
          
        }
      }
      $type = explode('(', str_replace(array("'", ')', ','), array('', '', '|'), strtolower($row['Type'])));
      $this->_columnsType[$inlowercase] = array(
          'type' => $type[0],
          'attr' => isset($type[1]) ? $type[1] : '',
          'nocheck' => $row['Extra'] == 'auto_increment' ? true : false
      );
      if ($row['Key'] == 'PRI') {
        $structConfig['key'] = array('Value' => $inlowercase, 'ReadOnly' => true);
      }
      $this->_columns[] = $inlowercase;
      $resultConfig[$inlowercase] = array('Value' => '');
      if (!empty($row['Default']) && 'CURRENT_TIMESTAMP' != $row['Default'])
        $this->$inlowercase = $row['Default'];  
      else
        $this->$inlowercase = false;
    }
    if (!isset($structConfig['key'])) {
      if(!$this->_noPrimaryKey) {
        user_error('Table "PRI" key not found: '.get_class($this));
      }
      $structConfig['key'] = array('Value' => '', 'ReadOnly' => true);
    }
    $this->_columnsCount = count($this->_columns);
    $this->_struct = new fsStruct($structConfig);
    $this->_result = new fsStruct($resultConfig);
    $this->_query = new fsStruct($queryConfig);
    $db->Close();
    if ($createCache) {
      $this->_Cache();
    }
  }
  
  /**
  * Create cache of table.   
  * @api
  * @since 1.0.0
  * @return void      
  */
  private function _Cache()
  {
    if (!is_dir(PATH_CACHE_DB)) {
      mkdir(PATH_CACHE_DB, 0755);
    }
    $new_file = '_struct_'.$this->_struct->name;
    $file = new fsFileWorker(PATH_CACHE_DB.$new_file.'.php', 'w+');
    $file->WriteLine('<'.'?php');
    $file->WriteLineWithTabsAction("class {0} implements iSingleton \n{",
                     array($new_file),
                     1);
    $vars = array();
    $file->WriteLine('pro'."tected static \$obj = null;"); 
    $file->WriteLine('priv'."ate function __construct(){ }");
    $file->WriteLine('priv'."ate function __clone(){ }");
    $file->WriteLine('priv'."ate function __wakeup(){ }");
    $file->WriteLineWithTabsAction("public static func"."tion GetInstance() \n {",
                                    array(),
                                    1
                                  );
    $file->WriteLineWithTabsAction("if (is_null(self::\$obj)) {", array(), 1);
    $file->WriteLine("self::\$obj = new {0};", array($new_file));    
    $file->WriteLine("\$ResultConfig = array();");    
    $file->WriteLine("\$StructConfig = array();");
    $file->WriteLine("\$QueryConfig = array();");
    $file->WriteLine("\$QueryConfig['sql'] = array('Value' => '');");
    $file->WriteLine("\$QueryConfig['action'] = array('Value' => '');");
    $file->WriteLine("\$QueryConfig['select'] = array('Value' => '');");
    $file->WriteLine("\$QueryConfig['selectLinks'] = array('Value' => false);");
    $file->WriteLine("\$QueryConfig['update'] = array('Value' => '');");
    $file->WriteLine("\$QueryConfig['group'] = array('Value' => false);");
    $file->WriteLine("\$QueryConfig['order'] = array('Value' => false);");
    $file->WriteLine("\$QueryConfig['where'] = array('Value' => false);");
    $file->WriteLine("\$QueryConfig['order_desc'] = array('Value' => false);");
    $file->WriteLine("\$QueryConfig['limit'] = array('Value' => '');");
    $file->WriteLine("\$StructConfig['current'] = array('Value' => false, 'ReadOnly' => false);");
    $file->WriteLine("\$StructConfig['name'] = array('Value' => '{0}', 'ReadOnly' => true);",
                     array($this->_struct->name) 
                    );
    $file->WriteLine("\$StructConfig['db'] = array('Value' => new fsDBconnection(), 'ReadOnly' => true);");
    $file->WriteLine("\$StructConfig['key'] = array('Value' => '{0}', 'ReadOnly' => true);",
                     array($this->_struct->key) 
                    );
    $file->WriteLine("\$ResultConfig['mysqlResult'] = Array('Value' => null);");
    $file->WriteLine("\$ResultConfig['mysqlRow'] = Array('Value' => null);");
    
    for ($i = 0; $i < $this->_columnsCount; ++$i) {
      $file->WriteLine("\$ResultConfig['{0}'] = Array('Value' => '');",
                       array($this->_columns[$i]));
      
    }
    $file->WriteLine("self::\$obj->Set('struct', new fsStruct(\$StructConfig));");
    $file->WriteLine("self::\$obj->Set('result', new fsStruct(\$ResultConfig));");
    $file->WriteLine("self::\$obj->Set('query', new fsStruct(\$QueryConfig));");
    $file->WriteLineWithTabsAction("}", array(), -1);
    $file->WriteLineWithTabsAction("return self::\$obj;", array(), -1);
    $file->WriteLine("}");
    
    $file->WriteLine("private \$struct = false;");
    $file->WriteLine("private \$result = false;");
    $file->WriteLine("private \$query = false;");
    $file->WriteLine("private \$columnsCount = {0};", array($this->_columnsCount));
    $file->Write("private \$join = array(");
     
    $temp = count($this->_join);
    for ($i = 0; $i < $temp; ++$i) {
     $file->Write("array(\"Table\" => '{0}', \"On\" => '{1}', \"View\" => '{2}', \"MyField\" => '{3}'){4}",
                  array(
                  $this->_join[$i]["Table"],
                  $this->_join[$i]["On"],
                  $this->_join[$i]["View"],
                  $this->_join[$i]["MyField"],
                  ($i == $temp - 1 ? '' : ', ')
                  )
                 );
    }
    $file->WriteLine(');');
    $file->Write("private \$columns = array(");
    for ($i = 0; $i < $this->_columnsCount; ++$i) {
      $f = $this->_columns[$i];
      $vars[] = "public \$".$f." = '".$this->$f."';";
      $file->Write("'{0}'{1}", array($f,
                                     $i == $this->_columnsCount - 1 ? '' : ',' 
                               ));  
    }
    $file->WriteLine(');');
    $file-> WriteArray($vars);
     
    $file->WriteLine("private \$columnsType = array(");
    for ($i = 0; $i < $this->_columnsCount; ++$i) { 
      $file->WriteLine("'{0}' => array('type' => '{1}',
                                   'attr' => '{2}',
                                   'nocheck' => '{3}'){4}",
                    array($this->_columns[$i],
                          $this->_columnsType[$this->_columns[$i]]['type'],
                          $this->_columnsType[$this->_columns[$i]]['attr'],    
                          ($this->_columnsType[$this->_columns[$i]]['nocheck'] 
                           ? 'true'
                           : 'false'
                          ),
                          $i == $this->_columnsCount - 1 ? '' : ', '
                    ));
      
    }
    $file->WriteLine(');');
    $file->WriteLineWithTabsAction("publ"."ic function Get(\$what) {", array(), 1); 
    $file->WriteLineWithTabsAction("if (isset(\$this->\$what)) {", array(), 1);
    $file->WriteLineWithTabsAction("return \$this->\$what;", array(), -1);
    $file->WriteLineWithTabsAction('}', array(), -1);
    $file->WriteLineWithTabsAction('}');
    $file->WriteLineWithTabsAction("pub"."lic function Set(\$what, \$value) \n {", array(), 1); 
    $file->WriteLineWithTabsAction("if (isset(\$this->\$what)) {", array(), 1);
    $file->WriteLineWithTabsAction("\$this->\$what = \$value;", array(), -1);
    $file->WriteLineWithTabsAction('}', array(), -1);
    $file->WriteLineWithTabsAction('}', array(), -1);
    $file->WriteLineWithTabsAction('}', array(), -1);
    $file->Close();
  }
}