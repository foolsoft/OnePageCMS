<?php
/*
 * fsCMS Administrator panel base class for list items (like id/title)
 */

/*Sample class
class SampleAdminPanelList extends AdminPanelList
{
    protected $_title = 'Page title';
    protected $_tableName = 'table1';
    protected $_uploadPath = 'uploads';
    protected $_columns = array(
        'id' => array('title' => 'Id', 'type' => 'hidden'),
        'id_table2' => array('title' => 'Table 2 Field', 'type' => 'select',
            'filter' => 'tracklibrary', 'filter-data' => 'id', 'filter-display' => 'title'),
        'type' => array('title' => 'Table 2 Field', 'type' => 'select',
            'filter' => array(1 => 'Type 1', 2 => 'Type 2')),
        'title' => array('required' => true, 'title' => 'Title', 'type' => 'text'),
        'cover' => array('title' => 'Cover', 'type' => 'file', 'filter' =>  array('image/gif', 'image/jpg', 'image/jpeg', 'image/png')),
    );
} */

class AdminPanelList extends AdminPanel
{
    protected $_title = '';
    protected $_uploadPath = 'uploads/';
    protected $_tableName = '';
    protected $_columns = array();

    public function Init($request)
    {
        parent::Init($request);

        if ($this->_tableName == '') {
            throw new Exception('_tableName is empty');
        }
        if(!fsFunctions::IsArrayAssoc($this->_columns) || count($this->_columns) == 0) {
            throw new Exception('_columns is empty');
        }

        $this->_uploadPath = PATH_ROOT.$this->_uploadPath;
        $this->Tag('title', $this->_title);
        $this->Tag('columns', $this->_columns);
        $this->Tag('key', $this->_table->key);

        fsFunctions::CreateDirectory($this->_uploadPath);
    }

    public function actionIndex($param)
    {
        $listValues = $this->_GetFilters($param);
        $html = $this->CreateView(array(
            'listValues' => $listValues,
            'rows' => $this->_table->GetAll()
        ), $this->_Template('Index', 'AdminPanelList'));
        $this->Html($html);
    }

    private function _GetFilters($param)
    {
        $listValues = array();
        foreach($this->_columns as $name => $c) {
            if(!empty($c['type']) && ($c['type'] == 'select' || $c['type'] == 'radio')
                && !empty($c['filter'])) {
                $listValues[$name] = array();
                if(is_array($c['filter'])) {
                  $isAssoc = fsFunctions::IsArrayAssoc($c['filter']);
                  foreach($c['filter'] as $key => $value) {
                    $listValues[$name][$isAssoc ? $key : $value] = $value;
                  }
                } else {
                  $db = new fsDBTableExtension($c['filter']);
                  $values = $db->GetAll();
                  $keyIndex = !empty($c['filter-data']) ? $c['filter-data'] : $db->key;
                  $viewIndex = !empty($c['filter-display']) ? $c['filter-display'] : $db->key;
                  foreach($values as $value) {
                      $listValues[$name][$value[$keyIndex]] = $value[$viewIndex];
                  }
                }
            }
        }
        return $listValues;
    }

    public function actionAddEdit($param)
    {
        $listValues = $this->_GetFilters($param);
        $html = $this->CreateView(array(
            'item' => $param->Exists('key') ? $this->_table->GetOne($param->key) : null,
            'listValues' => $listValues,
        ), $this->_Template('AddEdit', 'AdminPanelList'));
        $this->Html($html);
    }

    private function _RequestParamSet(&$param)
    {
        foreach($this->_columns as $name => $c) {
            $type = empty($c['type']) ? 'text' : $c['type'];
            switch($type) {
                case 'file':
                    $param->$name = '';
                    $error = fsFunctions::CheckUploadFiles($name,
                        is_array($c['filter']) ? $c['filter'] : array(),
                        false, true
                    );
                    if(!$error) {
                      $newFile = '';
                      if (fsFunctions::UploadFiles($name, $this->_uploadPath, $newFile) && $newFile != '') {
                        $param->$name = str_replace(PATH_ROOT, '/', $this->_uploadPath).$newFile;
                      }
                    }
                    break;
                case 'checkbox':
                    $param->$name = $param->Exists($name) ? 1 : 0;
                    break;
                default:
                    break;
            }
            if(isset($c['required']) && $param->$name == '') {
                $this->_Referer();
                $this->Message(T('XMLcms_text_need_all_data'));
                return false;
            }
        }
        return true;
    }

    public function actionDoAdd($param)
    {
        if($this->_RequestParamSet($param)) {
            parent::actionDoAdd($param);
        }
    }

    public function actionDoEdit($param)
    {
        if($this->_RequestParamSet($param)) {
            parent::actionDoEdit($param);
        }
    }

}