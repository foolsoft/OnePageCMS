<?php
class AdminMUsers extends AdminPanel
{
    protected $_tableName = 'users';

    private function _CheckParamField($param) 
    {
        if ($param->name == '') {
            $this->Message(T('XMLcms_text_need_all_data'));
            $this->_Referer();
            return false;
        }
        $param->duty = $param->Exists('duty') ? 1 : 0;
        return true;
    }

    private function _CheckParamGroup($param)
    {
        $param->table = 'types_users';
        return $this->_CheckParamField($param);
    }

    private function _CheckParam($param, $checkPasswordEmpty = true)
    {
        if ($param->login == '') {
            $this->Message(T('XMLcms_text_need_all_data'));
        } else if ($param->password == '' && $checkPasswordEmpty) {
            $this->Message(T('XMLcms_text_empty_pwd'));
        } else if ($param->password != $param->rpassword) {
            $this->Message(T('XMLcms_text_bad_pwd_confirm'));
        } else if (true !== ($userId = $this->_table->IsUnique($param->login, 'login', 'id'))) {
            if (!$param->Exists('key', true) || $param->key != $userId) {
                $this->Message(T('XMLcms_text_login_not_unique'));
            }
        }
        if ($this->Message() != '') {
            $this->_Referer();
            return false;
        }
        return true;
    }

    public function actionConfig($param) 
    {
        $this->Tag('settings', $this->settings);
    }

    public function Init($request) 
    {
        $this->Tag('title', T('XMLcms_text_users'));
        parent::Init($request);
    }

    public function actionAddField($param) 
    {
        $this->Tag('types', fsFields::GetTypes());
    }

    public function actionAddGroup($param) 
    {
    }

    public function actionDoAddGroup($param) 
    {
        if (!$this->_CheckParamGroup($param)) {
            return;
        }
        parent::actionDoAdd($param);
    }

    public function actionEditField($param) 
    {
        $user_fields = new user_fields();
        $user_fields->current = $param->key;
        if ($user_fields->id != $param->key) {
            return $this->_Referer();
        }
        $this->Tag('field', $user_fields->result);
        $this->Tag('types', fsFields::GetTypes());
    }

    public function actionDoEditField($param) 
    {
        if (!$this->_CheckParamField($param)) {
            return;
        }
        if (!$this->_CheckUnique($param->name, 'name', $param->key, 'id', 'user_fields')) {
            return;
        }
        parent::actionDoEdit($param);
    }

    public function actionEditGroup($param) 
    {
        $types_users = new types_users();
        $type = $types_users->Get($param->key);
        if ($type == null) {
            return $this->_Referer();
        }
        $this->Tag('type', $type);
    }

    public function actionDoEditGroup($param) 
    {
        if (!$this->_CheckParamGroup($param)) {
            return;
        }
        parent::actionDoEdit($param);
    }

    public function actionDoAddField($param) 
    {
        if (!$this->_CheckParamField($param)) {
            return;
        }
        if (!$this->_CheckUnique($param->name, 'name', false, false, 'user_fields')) {
            return;
        }
        $fieldId = parent::actionDoAdd($param);
        if ($fieldId > 0) {
            $user_info = new user_info();
            $users = $this->_table->GetAll();
            foreach ($users as $user) {
                $user_info->Change($user['id'], $fieldId);
            }
        }
    }

    public function actionDoAdd($param) 
    {
        if (!$this->_CheckParam($param)) {
            return;
        }
        $param->active = $param->Exists('active') ? 1 : 0;
        $param->password = users::HashPassword($param->password);
        if (($param->key = parent::actionDoAdd($param)) > 0) {
            $this->_UpdateUserFields($param);
        }
    }

    public function actionFields($param) 
    {
        $user_fields = new user_fields();
        $this->Tag('fields', $user_fields->GetAll(true, false, array('position', 'title')));
    }

    public function actionIndex($param) 
    {
        $this->Tag('search', $param->search);
        $this->Tag('loginOrId', $param->loginOrId);
        $this->Tag('onpage', $param->Exists('onpage', true, '[1-9]\d*') ? $param->onpage : 10);
        $param->page = $param->Exists('page', true, '[1-9]\d*') ? $param->page : 1;
        $types_users = new types_users();
        $users = $filterWhere = $types = array();
        $pageParams = 'onpage='.$this->Tag('onpage').'&page';
        $filterHaving = '';
        
        if($param->Exists('type', true, '[1-9]\d*')) {
            $filterWhere[] = array(
                'type' => $param->type
            );
            $this->Tag('type', $param->type);
        }
        if($param->loginOrId != '') {
            $filterWhere[] = array(
                array(
                    'login' => '%'.$param->loginOrId.'%',
                    'logic' => 'OR',
                    'key' => 'LIKE'
                ), 
                array(
                    array('id' => (int)$param->loginOrId),
                    array('id' => 0, 'key' => '!='),
                )
            );
        }
        if($param->search != '') {
            $filterHaving = 'info LIKE "%'.$param->search.'%"';
            $pageParams = 'search='.$param->search.'&'.$pageParams;
        }
        
        $count = $this->_table->GetList($users, $filterWhere, $filterHaving, $this->Tag('onpage'), $this->Tag('onpage') * ($param->page - 1));
        $pages = fsPaginator::Get($this->_My('Index'), $pageParams, $count,  $this->Tag('onpage'), $param->page);
        $types_users = $types_users->Get();
        $types['-1'] = T('XMLcms_she_any');
        foreach ($types_users as $type) {
            $types[$type['id']] = $type['name'];
        }
        
        $this->Tag('users', $users);
        $this->Tag('pages', $pages);
        $this->Tag('types', $types);
    }

    public function actionGroups($param) 
    {
        $types_users = new types_users();
        $this->Tag('types', $types_users->Get());
    }

    public function actionDoEdit($param) 
    {
        if (!$this->_CheckParam($param, false)) {
            return;
        }
        if (!$this->_CheckUnique($param->login, 'login', $param->key, 'id')) {
            return;
        }
        if ($param->login != fsConfig::GetInstance('main_admin')) {
            $param->active = $param->Exists('active') ? 1 : 0;
        } else {
            $param->active = 1;
        }
        if ($param->password != '') {
            $param->password = users::HashPassword($param->password);
        } else {
            $param->Delete('password');
        }
        if (parent::actionDoEdit($param) == 0) {
            $this->_UpdateUserFields($param);
        }
    }

    private function _UpdateUserFields($param) 
    {
        if (!$param->Exists('user_field') || !is_array($param->user_field)) {
            return;
        }
        $user_info = new user_info();
        $user_fields = new user_fields();
        $user_fields = $user_fields->GetAssocArray();
        foreach ($param->user_field as $fieldName => $value) {
            if (isset($user_fields[$fieldName]) && ($user_fields[$fieldName]['expression'] == '' || preg_match('/^' . $user_fields[$fieldName]['expression'] . '$/u', $value))) {
                $user_info->Change($param->key, $fieldName, $value);
            }
        }
    }

    private function _GetUserFields($userId) 
    {        
        $user_info = new user_info();
        return $user_info->GetInfo($userId, true);
    }

    private function _InitFieldsArray() 
    {
        $user_fields = new user_fields();
        $this->Tag('fields', $user_fields->GetAssocArray());
    }

    public function actionAdd($param) 
    {
        $types_users = new types_users();
        $this->Tag('types', $types_users->Get());
        $this->_InitFieldsArray();
    }

    public function actionEdit($param) 
    {
        $this->_table->current = $param->key;
        if ($this->_table->result->id != $param->key) {
            return $this->_Referer();
        }
        $types_users = new types_users();
        $this->Tag('info', $this->_GetUserFields($param->key));
        $this->Tag('types', $types_users->Get());
        $this->Tag('user', $this->_table->result);
        $this->_InitFieldsArray();
    }

    public function actionDelete($param) 
    {
        if ($param->table == 'types_users' && $param->key < USER_TYPE) {
            return $this->HttpNotFound();
        }
        if (parent::actionDelete($param) == 0) {
            $user_info = new user_info();
            switch ($param->table) {
                case 'user_fields':
                    $user_info->DeleteBy($param->key, 'id_field');
                    break;
                case 'types_users':
                    $users = new users();
                    $users->Update(array('type'), array(USER_TYPE))->Where('`type` = "' . $param->key . '"')->Execute();
                    break;
                case 'users':
                default:
                    $user_info->DeleteBy($param->key, 'id_user');
                    break;
            }
        }
    }
}