<?php
class fsStruct
{
  protected $fields = array();
  protected $allowNull;
  
  //Выводим на экран содержимое структуры
  public function ToScreen($formated = true) {
    if($formated !== true) {
      print_r($this->fields);
    } else {
      fsFunctions::FormatPrint($this->fields);
    }
  }
  
  //Получение массива всех полей структуры за исключением значений из $filter  
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
  
  //Очистка всех значнией структуры, которые не указаны в $filter 
  //и которые имеют атрибут ReadOnly = false
  public function Clear($filter = array())
  {
    foreach ($this->fields as $field => $struct) {
      if ($struct['ReadOnly'] === false && !in_array($field, $filter)) {
        $this->fields[$field]['Value'] = '';
      }
    }
  }
  
  //Возвращает структуру в виде ассоциативного массива
  public function ToArray()
  {
    $arr = array();
    foreach ($this->fields as $field => $struct) {
      $arr[$field] = $struct['Value'];
    }
    return $arr;
  }
  
  //Проверка существования в структуре элемента с именем $field 
  //$check_numeric = true дополнительно провреят является ли значение элемента
  //$field числом
  public function Exists($field, $check_numeric = false)
  {
    return isset($this->fields[$field]) && 
           (!$check_numeric || is_numeric($this->fields[$field]['Value'])); 
  }
  
  //Удаление из струтуры элемента с именем $field (или значениями массива $field),
  //если $field не указано удаляются все элементы структуры
  public function Delete($field = false)
  {
    if ($field === false) {
      $this->fields = Array();
    } else if (is_array($field)) { 
      foreach ($field as $f) {
        $this->Delete($f);
      }
    } else if ($this->Exists($field)) {
      unset($this->fields[$field]);
    }
  }
  
  
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
      return '';
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
      throw new fsStructException("Can't set property: " . __CLASS__ . "->".$field);
    }
  }
}

class fsStructException extends Exception 
{
}
?>