<?php
class AdminMConstants extends AdminPanel
{
  protected $_tableName = 'constants';

  public function Init($request)
  {
    $this->Tag('title', T('XMLcms_text_constants'));
    parent::Init($request);
  }

  public function actionDoEdit($Param)
  {
    $Param->name = fsFunctions::Chpu($Param->name);
    if (!$this->_CheckUnique($Param->name, 'name', $Param->key, 'name')
        || !$this->_Check($Param)) {
      return;
    }
    parent::actionDoEdit($Param);
  }

  private function _Check($param) 
  {
    $return = $param->name != '';
    if(!$return) {
        $this->Message(T('XMLcms_text_need_all_data'));
    } else {
      $return = !is_numeric($param->name[0]);
      if(!$return) {
          $this->Message(T('XMLcms_invalid_data'));
      }
    }
    if(!$return) {
      $this->_Referer();
    }
    return $return; 
  }

  public function actionDoAdd($Param)
  {
    $Param->name = fsFunctions::Chpu($Param->name);
    if (!$this->_CheckUnique($Param->name, 'name') 
        || !$this->_Check($Param)) {
      return;
    }
    parent::actionDoAdd($Param);
  }
  
  public function actionAdd($Param)
  {                             
  }

  public function actionIndex($Params)
  {
    $this->Tag('consts', $this->Tag('constants')->ToArray());
  }
  
  public function actionEdit($Param)
  {
    $this->_table->current = $Param->key;
    if ($this->_table->result->name != $Param->key) {
      $this->_Referer();
      return;
    }
    $this->Tag('key', $Param->key);
  }
}
?>