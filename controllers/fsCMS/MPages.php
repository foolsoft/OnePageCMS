<?php
class MPages extends cmsController
{
  protected $_tableName = 'pages';
  
  public function actionView($param)
  {
    if (!$param->Exists('page')) {
      return $this->HttpNotFound();
    }
    $pid = false; $palt = $param->page;
    if(is_numeric($param->page)) {
        $pid = $param->page;
        $palt = false;
    } 
    $page = $this->_table->Load($pid, $palt);
    $template = '';
    if (count($page) != 1) {
      if(URL_ROOT_CLEAR.substr($_SERVER['REQUEST_URI'], 1) != fsConfig::GetInstance('url_404')) {
        return $this->HttpNotFound();
      }
      $page[0] = array();
      $page[0]['html'] = CMSSettings::GetInstance('page_not_found');
      $page[0]['title'] = T('XMLcms_text_page_not_found');
      $template = $this->_Template(CMSSettings::GetInstance('main_template'));  
    } else {
      if($page[0]['auth'] == 1 && !AUTH) {
        return $this->Redirect(fsHtml::Url(CMSSettings::GetInstance('auth_need_page')));     
      } else {    
        $template = $this->_Template($page[0]['tpl']);
      }
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