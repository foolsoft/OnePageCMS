<?php
class AdminMConstants extends AdminPanel
{
    protected $_tableName = 'constants';

    public function Init($request)
    {
        $this->Tag('title', T('XMLcms_text_constants'));
        parent::Init($request);
    }

    public function actionDoEdit($param)
    {
        $param->name = fsFunctions::Chpu($param->name);
        if (!$this->_CheckUnique($param->name, 'name', $param->key, 'name') || !$this->_Check($param)) {
            return;
        }
        parent::actionDoEdit($param);
    }

    private function _Check($param) 
    {
        $param->name = fsValidator::TextOnly($param->name, array('_'));
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

    public function actionDoAdd($param)
    {
        $param->name = fsFunctions::Chpu($param->name);
        if (!$this->_CheckUnique($param->name, 'name') || !$this->_Check($param)) {
            return;
        }
        parent::actionDoAdd($param);
    }
  
    public function actionAdd($param)
    {                
    }

    public function actionIndex($param)
    {
        $this->Tag('consts', $this->Tag('constants')->ToArray());
    }
  
    public function actionEdit($param)
    {
        $this->_table->current = $param->key;
        if ($this->_table->result->name != $param->key) {
            return $this->_Referer();
        }
        $this->Tag('key', $param->key);
    }
}