<?php
class types_users extends fsDBTableExtension
{
    public function __destruct()
    {
        parent::__destruct();
    }
    
    public function Remove($id)
    {
        if(!is_numeric($id) || $id <= $this->_minId) {
            return false;
        }  
        if($this->DeleteBy($id)) {
            $user_info = new user_info();
            $users = new users();
            $usersArray = $users->Select()->Where('`type` = "'.$id."'")->ExecuteToArray();
            foreach($usersArray as $user) {
                $user_info->DeleteBy($user['id'], 'id_user');
            }
            return $users->DeleteBy($id, 'type');
        }
        return false;
    }
    
    public function Get($id = false)
    {
      $this->Select();
      if($id !== false) {
          $this->Where("`id` = '".$id."'");
      }
      $this->Order(array('name'));
      $result = $this->ExecuteToArray();
      if($id === false) {
        return $result; 
      }
      return count($result) == 1 ? $result[0] : null;
    }
}