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
    
    public function Get($id = null)
    {
        $this->Select();
        $where = $id === GUEST_USER_TYPE ? array() : array(array('id' => GUEST_USER_TYPE, 'key' => '!='));
        if(is_numeric($id) && $id >= 0) {
            $where[] = array('id' => $id);       
        }
        $result = $this->Where($where)->Order(array('name'))->ExecuteToArray();
        if($id === null) {
            return $result; 
        }
        return count($result) == 1 ? $result[0] : null;
    }
}