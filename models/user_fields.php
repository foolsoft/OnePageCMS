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
    
    public function GetAssocArray($arrayKey = 'name', $where = null)
    {
        $fields = array();
        $this->Select();
        if($where !== null) {
            $this->Where($where);
        }
        $this->Order(array('position', 'title'))->Execute('', false);
        while($this->Next()) {
            $isSelect = strpos($this->result->regexp, '||');
            $values = json_encode($isSelect === false ? array() : fsFunctions::Explode('||', $this->result->regexp));
            $fields[$this->result->$arrayKey] = array(
                'id' => $this->result->id,
                'name' => $this->result->name,
                'type' => $this->result->type,
                'duty' => $this->result->duty,
                'title' => $this->result->title,
                'position' => $this->result->position,
                'expression' => $this->result->regexp, 
                'values' => $values, 
            );
        }
        return $fields;
    }
  
}