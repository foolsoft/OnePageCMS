<?php
class AdminMPages extends AdminPanel
{
  protected $_tableName = 'pages';

  private function _CheckParam(&$param)
  {
    if ($param->alt == '' || $param->title == '') {
      $this->Message(T('XMLcms_text_need_all_data'));
      $this->_Referer();
      return false;
    }
    $param->alt = strtolower(fsFunctions::Chpu($param->alt));
    $param->in_menu = $param->Exists('in_menu') ? 1 : 0;
    $param->active = $param->Exists('active') ? 1 : 0;
    return true;
  }
               
  public function Init($request)
  {
    $this->Tag('title', T('XMLcms_pages'));
    parent::Init($request);
  }

  public function actionDoAdd($param)
  {
    if(!$this->_CheckParam($param)) {
      return;
    }
    if (!$this->_CheckUnique($param->alt, 'alt')) {
      return;
    }
    parent::actionDoAdd($param);
  }
  
  public function actionAdd($param)
  {
    $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPages/', true, false, false, array('php'));
    $this->Tag('templates', $templates['NAMES']);
  }

  public function actionIndex($param)
  {
    $pages = new pages();
    $this->Tag('pages', $pages->GetAll(true, false, array('title')));
  }
  
  public function actionDoEdit($param)
  {
  	if(!$this->_CheckParam($param)) {
      return;
    }
  	if (!$this->_CheckUnique($param->alt, 'alt', $param->key, 'id')) {
      return;
    }
    parent::actionDoEdit($param);
  }
  
  public function actionEdit($param)
  {
    $this->_table->current = $param->key;
    if ($this->_table->result->id != $param->key) {
      $this->_Referer();
      return;
    }
    $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPages/', true, false, false, array('php'));
    $this->Tag('templates', $templates['NAMES']);
    $this->Tag('page', $this->_table->result);
  }

}