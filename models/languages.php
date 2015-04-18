<?php
class languages extends fsDBTableExtension
{
    public function __destruct()
    {
        parent::__destruct();
    }
    
    public function Add($name, $active = 0)
    {
        $this->name = $name;
        $this->active = $active;
        $this->Insert()->Execute();
        return $this->insertedId;
    }
    
    public function Edit($id, $newName)
    {
        return $this->Update(array('name'), array($newName))->Where('`id` = "'.$id.'"')->Execute();
    }
    
    public function GetActiveByName($name)
    {
        $this->Select(array('id'))->Where('`active` = "1" AND `name` = "'.$name.'"')->Execute();
        return $this->result->id;
    }
    
    public function Get($activeOnly = true)
    {
        $this->Select();
        if($activeOnly) {
            $this->Where('`active` = "1"');
        }
        return $this->ExecuteToArray('', 'id');
    }
}
