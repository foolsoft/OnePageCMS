<?php
class user_fields extends fsDBTableExtension
{
    public function __destruct()
    {
        parent::__destruct();
    }
  
    public function GetSpecialField($specialId)
    {
        $this->Select()->Where('`special_type` = "'.$specialId.'"')->Execute();
    }
    
    private function _IsSelectType($type)
    {
        return in_array($type, array('selectlist', 'radiolist'));
    }
    
    public function GetAssocArray($arrayKey = 'name', $where = null)
    {
        $fields = array();
        $this->Select();
        if($where !== null) {
            $this->Where($where);
        }
        $this->Order(array('position', 'title'))->Execute('', false);
        while($this->Next()) {
            $values = $this->_IsSelectType($this->result->type) ? explode('|', $this->result->expression) : array();
            $fields[$this->result->$arrayKey] = array(
                'id' => $this->result->id,
                'name' => $this->result->name,
                'type' => $this->result->type,
                'duty' => $this->result->duty,
                'title' => $this->result->title,
                'position' => $this->result->position,
                'expression' => $this->result->expression, 
                'values' => $values, 
            );
        }
        return $fields;
    }
}