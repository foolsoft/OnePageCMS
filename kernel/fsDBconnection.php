<?php
/**
* Settings for fsDBconnection class
* @package fsKernel
*/
class fsDBsettings extends fsStruct
{
  function __construct($сonfig = null)
  {
    if (!is_array($сonfig) || count($сonfig) == 0) {
      $сonfig = array();
      if (class_exists('DBsettings')) {
	      $сonfig['server'] = DBsettings::$server;
	      $сonfig['base'] = DBsettings::$base;
	      $сonfig['user'] = DBsettings::$user;
	      $сonfig['password'] = DBsettings::$password;
      }
    }
    if (!isset($сonfig['server']) || !isset($сonfig['base']) || 
        !isset($сonfig['user']) || !isset($сonfig['password'])) {
        return;
    }
    $classConfig = array();
    $filter = array('server', 'user', 'password', 'base');
    foreach ($сonfig as $field => $value) {
      if (!in_array($field, $filter)) {
        continue;
      }
      $classConfig[$field] = array();
      $classConfig[$field]['ReadOnly'] = true;
      $classConfig[$field]['Value'] = $value;
    }
    parent::__construct($classConfig);
  }
  
  public function __destruct()
  { 
    parent::__destruct();
  }
}
/**
* Connection with MySQL database
* @package fsKernel
*/
class fsDBconnection
{
  /** @var fsDBsettings MySQL connection settings */
  protected $_settings = null;
  
  /** @var mysqli Mysqli connection object */
  protected $_connection = null;
  
  /** @var integer Id of last inserted row */
  protected $_last = -1;
  
  /** @var integer Count of affected rows in last query */
  protected $_affectedRows = 0;
  
  /**
  * Connect to database.   
  * @api
  * @since 1.0.0
  * @return boolean Connection result.      
  */
  protected function Connect()
  {
    $this->_connection = mysqli_connect($this->_settings->server, 
                                        $this->_settings->user,
                                        $this->_settings->password);
    if ($this->_connection) {
      if ($this->_connection->select_db($this->_settings->base)) {
        $this->Query("SET NAMES ".fsConfig::GetInstance('db_codepage'));
        return true;
      }
    }
    return false;
  }
  
  /**
  * Protect input string for executing.    
  * @api
  * @since 1.0.0
  * @param string $string String for protection.
  * @return string Safe string.      
  */
  public function Escape($string)
  {
    if ($this->_connection == null && !$this->Connect()) {
      return '';
    }
    return $this->_connection->real_escape_string($string);
  }

  /**
  * Execute query.   
  * @api
  * @since 1.0.0
  * @param string $sql Query string.
  * @param boolean $closeConnection (optional) Flag for closing mysqli coneection after query executing.
  * @return mixed Result of executing MySQL query.      
  */
  public function Query($sql, $closeConnection = false)
  {
    if(empty($sql) || ($this->_connection == null && !$this->Connect())) {
      return null;
    } 
    $result = $this->_connection->query($sql);
    $q = substr($sql, 0, 6);
    $q = strtoupper($q);
    if ($result) {
      if ($q == 'INSERT')
      	$this->_last = $this->_connection->insert_id;
      if ($q == 'INSERT' || $q == 'DELETE' || $q == 'UPDATE' || $q == 'REPLACE')
      	$this->_affectedRows = $this->_connection->affected_rows;
    }
    if ($closeConnection === true) {
      $this->Close();
    }
    return $result;
  }

  /**
  * Close connection with database.    
  * @api
  * @since 1.0.0
  * @return void     
  */
  public function Close()
  { 
    if ($this->_connection != null) { 
      $this->_connection->close();
      $this->_connection = null;
    }
  }
  
  /**
  * Get id of last inserted row.    
  * @api
  * @since 1.0.0
  * @return integer Id of last inserted row.      
  */
  public function InsertedId()
  {
    return $this->_last;
  }
  
  /**
  * Get count of last affected rows.    
  * @api
  * @since 1.0.0
  * @return integer Count of last affected rows.      
  */
  public function AffectedRows()
  {
    return $this->_affectedRows;
  }
  
  /**
  * Constructor of fsDBconnection.    
  * @api
  * @since 1.0.0
  * @param array $config (optional) Connection settings      
  * @return void      
  */
  public function __construct($config = array())
  {
    $this->_settings = new fsDBsettings($config);
  }
  
  public function __destruct()
  {
    $this->Close();
    $this->_settings->Delete();
  }
}