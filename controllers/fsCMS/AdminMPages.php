<?php
class AdminMPages extends AdminPanel
{
  protected $_tableName = 'pages';

  private function _CheckParam(&$Param)
  {
    if ($Param->alt == '' || $Param->title == '') {
      $this->Message(T('XMLcms_text_need_all_data'));
      $this->_Referer();
      return false;
    }
    $Param->alt = fsFunctions::Chpu($Param->alt);
    $Param->in_menu = $Param->Exists('in_menu') ? 1 : 0;
    $Param->active = $Param->Exists('active') ? 1 : 0;
    return true;
  }
               
  public function Init($request)
  {
    $this->Tag('title', T('XMLcms_pages'));
    parent::Init($request);
  }

  public function actionDoAdd($Param)
  {
    if(!$this->_CheckParam($Param)) {
      return;
    }
    if (!$this->_CheckUnique($Param->alt, 'alt')) {
      return;
    }
    parent::actionDoAdd($Param);
  }
  
  public function actionAdd($Param)
  {
    $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPages/', true, false, false, array('php'));
    $this->Tag('templates', $templates['NAMES']);
  }

  public function actionIndex($Params)
  {
    $pages = new pages();
    $this->Tag('pages', $pages->GetAll(true, false, array('title')));
  }
  
  public function actionDoEdit($Param)
  {
  	if(!$this->_CheckParam($Param)) {
      return;
    }
  	if (!$this->_CheckUnique($Param->alt, 'alt', $Param->key, 'id')) {
      return;
    }
    parent::actionDoEdit($Param);
  }
  
  public function actionEdit($Param)
  {
    $this->_table->current = $Param->key;
    if ($this->_table->result->id != $Param->key) {
      $this->_Referer();
      return;
    }
    $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPages/', true, false, false, array('php'));
    $this->Tag('templates', $templates['NAMES']);
    $this->Tag('page', $this->_table->result);
  }

}

?>