<?php
class AdminMPages extends AdminPanel
{
    protected $_tableName = 'pages';
    protected $_languages = array();
    
    private function _CheckParam(&$param)
    {
        $this->_Referer();
        $this->Message(T('XMLcms_text_need_all_data'));
        if (!is_array($param->alt) || !is_array($param->title)
            || count($param->title) == 0 || count($param->alt) == 0) {
            return false;
        }
        $alts = $param->alt;
        $titles = $param->title;
        foreach($this->_languages as $languageId => $languageName) {
            if(!isset($titles[$languageId]) || !isset($alts[$languageId])) {
                return false;
            }
            $titles[$languageId] = trim(strip_tags($titles[$languageId]));
            $alts[$languageId] = strtolower(fsFunctions::Chpu(trim(strip_tags($alts[$languageId]))));
            if($titles[$languageId] == '' || $alts[$languageId] == '') {
                return false;
            }
        }
        $param->alt = $alts;
        $param->title = $titles;
        $param->in_menu = $param->Exists('in_menu') ? 1 : 0;
        $param->active = $param->Exists('active') ? 1 : 0;
        $param->auth = $param->Exists('auth') ? 1 : 0;
        $this->Message('');
        $this->Redirect('');
        return true;
    }
               
    public function Init($request)
    {
        $this->Tag('title', T('XMLcms_pages'));
        $languages = new languages();
        $this->_languages = $languages->Get();
        unset($languages);
        parent::Init($request);
    }
    
    private function _CheckAlt($alts, $id = -1)
    {
        if(!$this->_table->IsUniqueAlt($alts, $id)) {
            $this->Message(T('XMLcms_text_unique_link'));
            $this->_Referer();
            return false;
        }
        return true;
    }
    
    public function _UpdatePageInfo($pageId, $param)
    {
        $alts = $param->alt;
        $titles = $param->title;
        $htmls = $param->html;
        $keywords = $param->keywords;
        $descriptions = $param->description;
        foreach($this->_languages as $languageId => $languageName) {
            $this->_table->UpdateInfo($pageId, $languageId, $titles[$languageId], $alts[$languageId], $htmls[$languageId], $keywords[$languageId], $descriptions[$languageId]);
        }
    }
    
    public function actionDoAdd($param)
    {
        if(!$this->_CheckParam($param) || !$this->_CheckAlt($param->alt)) {
            return -1;
        }
        if(($pageId = parent::actionDoAdd($param)) > 0) {
            $this->_UpdatePageInfo($pageId, $param);    
        }
        return $pageId;
    }
  
    public function actionAdd($param)
    {
        $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPages/', true, false, array(), array('php'));
        $this->Tag('templates', $templates['NAMES']);
        $this->Tag('languages', $this->_languages);
    }

    public function actionIndex($param)
    {
        $pp = $param->title == '' ? 20 : 0;
        $page = $param->Exists('page', true) ? $param->page : 1;
        $pages = $pp === 0 ? '' : fsPaginator::Get($this->_My('Index'), 'page', $this->_table->GetCount(), $pp, $page);
        $this->Tag('pages', $this->_table->GetPages(fsSession::GetInstance('LanguageId'), $page, $pp, $param->title));
        $this->Tag('pagesNavigation', $pages);
        $this->Tag('search', $param->title);
    }
  
    public function actionDoEdit($param)
    {
        if(!$this->_CheckParam($param) || !$this->_CheckAlt($param->alt, $param->key) || 0 !== parent::actionDoEdit($param)) {
            return -1;
        }
        $this->_UpdatePageInfo($param->key, $param);
  	    return $param->key;
    }
  
    public function actionEdit($param)
    {
        $page =  $this->_table->GetFullInfo($param->key);
        if ($page === null) {
            $this->_Referer();
            return;
        }
        $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPages/', true, false, array(), array('php'));
        $this->Tag('templates', $templates['NAMES']);
        $this->Tag('languages', $this->_languages);
        $this->Html($this->CreateView(array('page' => $page)));
    }

    public function actionDelete($param)
    {
        if(!$param->Exists('key', true) || $param->key < 1) {
            return $this->HttpNotFound();
        }
        if(parent::actionDelete($param) == 0) {
            $this->_table->DeleteInfo($param->key);
        }
    }
}