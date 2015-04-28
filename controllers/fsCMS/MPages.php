<?php
class MPages extends cmsController
{
  protected $_tableName = 'pages';
  
  public function actionView($param)
  {
    if (!$param->Exists('page')) {
      return $this->HttpNotFound();
    }
    $pid = -1; $palt = $param->page;
    if(is_numeric($palt)) {
        $pid = $palt;
        $palt = '';
    } 
    $page = $this->_table->Load(fsSession::GetInstance('LanguageId'), $pid, $palt);
    $template = '';
    if ($page == null) {
      if(URL_ROOT_CLEAR.substr($_SERVER['REQUEST_URI'], 1) != fsConfig::GetInstance('url_404')) {
        return $this->HttpNotFound();
      }
      $page = array(
        'html' => CMSSettings::GetInstance('page_not_found'),
        'title' => T('XMLcms_text_page_not_found') 
      );
      $template = $this->_Template(CMSSettings::GetInstance('main_template'));  
    } else {
      if($page['auth'] == 1 && !AUTH) {
        return $this->Redirect(fsHtml::Url(CMSSettings::GetInstance('auth_need_page')));     
      }     
      $template = $this->_Template($page['tpl']);
    }      
    $page['meta_keywords'] = htmlspecialchars_decode($page['keywords'] == '' ? $page['keywords'] : CMSSettings::GetInstance('default_keywords')); 
    $page['meta_description'] = htmlspecialchars_decode($page['description'] == '' ? $page['description'] : CMSSettings::GetInstance('default_description')); 
    $page['html'] = htmlspecialchars_decode($page['html']);
    $page['title'] = htmlspecialchars_decode($page['title']);
    
    $html = $this->CreateView(array('page' => $page, 'htmlTags' => $param->Exists('data') ? $param->data : array()), $template);
    
    $this->Html($html);
  }
}