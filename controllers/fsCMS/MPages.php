<?php
class MPages extends cmsController
{
  protected $_tableName = 'pages';
  
  public function actionView($param)
  {
    $key = '='; 
    if (!$param->Exists('page') ||
         $param->page == '' ||
         (is_numeric($param->page) && ($param->page < -1 || $param->page == 0))) {
      $param->page = 0;
      $key = '>';
    }
    $page = $this->_table->Load($param->page, $key);
    $template = '';
    if (count($page) != 1) {
      $page[0] = array();
      $page[0]['html'] = CMSSettings::GetInstance('page_not_found');
      $page[0]['title'] = T('XMLcms_text_page_not_found');
      $template = $this->_Template(CMSSettings::GetInstance('main_template'));  
    } else {
      $template = $this->_Template($page[0]['tpl']);
    }                     
    $page[0]['meta_keywords'] = fsFunctions::NotEmpty($page[0]['keywords'])
                            ? $page[0]['keywords'] 
                            : CMSSettings::GetInstance('default_keywords'); 
    $page[0]['meta_description'] = fsFunctions::NotEmpty($page[0]['description'])
                            ? $page[0]['description'] 
                            : CMSSettings::GetInstance('default_description'); 
    $this->Html($this->CreateView(array('page' => $page[0], 'htmlTags' => $param->Exists('data') ? $param->data : array()), $template));
  }
}