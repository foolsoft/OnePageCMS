<?php
class AdminMPosts extends AdminPanel 
{
    protected $_tableName = 'posts';
    protected $_languages = array();
    private $_imagePath = 'uploads/posts/';
    
    private function _CheckParamAltTitle(&$param)
    {
        $alts = $param->alt;
        $titles = $param->title;
        $metaKw = $param->meta_keywords;
        $metaDescr = $param->meta_description;
        foreach($this->_languages as $languageId => $languageName) {
            if(!isset($titles[$languageId]) || !isset($alts[$languageId])) {
                return false;
            }
            $titles[$languageId] = trim(strip_tags($titles[$languageId]));
            $alts[$languageId] = strtolower(fsFunctions::Chpu(trim(strip_tags($alts[$languageId]))));
            if($titles[$languageId] == '' || $alts[$languageId] == '') {
                return false;
            }
            $metaKw[$languageId] = trim(strip_tags($metaKw[$languageId]));
            $metaDescr[$languageId] = trim(strip_tags($metaDescr[$languageId]));
        }
        $param->meta_keywords = $metaKw;
        $param->meta_description = $metaDescr;
        $param->alt = $alts;
        $param->title = $titles;
        return true;
    }
  
    private function _CheckCategoryData(&$param)
    {
        $this->_Referer();
        $this->Message(T('XMLcms_text_need_all_data'));
        if (!is_array($param->alt) || !is_array($param->title)
            || count($param->title) == 0 || count($param->alt) == 0
            || !$this->_CheckParamAltTitle($param)) {
            return false;
        }
        
        $param->auth = $param->Exists('auth') ? 1 : 0;
        $this->Message('');
        $this->Redirect('');
        return true;
    }
  
    private function _CheckPostData(&$param)
    {
        $this->_Referer();
        $this->Message(T('XMLcms_text_need_all_data'));
        if (!is_array($param->alt) || !is_array($param->title)
            || count($param->title) == 0 || count($param->alt) == 0
            || !is_array($param->id_category) || count($param->id_category) == 0
            || !$this->_CheckParamAltTitle($param)) {
            return false;
        }
        $param->auth = $param->Exists('auth') ? 1 : 0;
        $param->active = $param->Exists('active') ? 1 : 0;
        $param->id_user = $param->Exists('id_user', true) ? $param->id_user : fsSession::GetArrInstance('AUTH', 'id');
        if ($param->time == '') {
            $param->time = date('H:i:s');
        }
        $param->date = $param->date.' '.$param->time;
        if (!fsValidator::Check($param->date, 'TIMEDATE')) {
            $param->date = date('Y-m-d H:i:s');
        }
        if (in_array(ALL_TYPES, $param->id_category)) {
            $param->id_category = array(ALL_TYPES);
        }
        $this->Message('');
        $this->Redirect('');
        return true;  
    }   
  
   private function _PostsTable($param) 
   {
        $ppage = 20;
        $category = $param->Exists('category') ? $param->category : ALL_TYPES;  
        $page = $param->Exists('page', true) ? $param->page : 1; 
        $inCategory = $this->_table->GetByCategory(fsSession::GetInstance('LanguageId'), $category, $page, $ppage, false);
        $count = $this->_table->GetCountByCategory($category, false); 
        $this->Tag('posts', $inCategory);
        $this->Tag('pages', fsPaginator::Get($this->_My('Index/category/'.$category.'/'), 'page', $count, $ppage, $page));
        return $this->CreateView(array(), $this->_Template('Table'), $param->Exists('show'));
   }
  
    public function Init($request)
    {
        $this->_imagePath = PATH_ROOT.$this->_imagePath;
        $this->Tag('title', T('XMLcms_text_posts'));
        $languages = new languages();
        $this->_languages = $languages->Get();
        unset($languages);
        fsFunctions::CreateDirectory($this->_imagePath);
        parent::Init($request);
    }

    public function actionAddPost($param)
    {
        $this->_InitAddEditPost();
    }
    
    private function _InitAddEditPost()
    {
        $posts_category = new posts_category();
        $posts_category = $posts_category->GetCategories(fsSession::GetInstance('LanguageId'));
        if (count($posts_category) < 2) {
            $this->Message(T('XMLcms_text_no_category_found'));
            $this->_Referer();
            return false;
        }
        $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, array('Post'), array('php'));
        $templates_short = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, array('ShortPost'), array('php'));
        $this->Tag('templates', $templates['NAMES']);
        $this->Tag('templates_short', $templates_short['NAMES']);
        $this->Tag('categories', $posts_category);
        $this->Tag('languages', $this->_languages);
        return true;
    }
  
    public function actionEditPost($param)
    {
        $post = $this->_table->GetFullInfo($param->key);
        if ($post === null || !$this->_InitAddEditPost()) {
            return $this->_Referer();
        }
        $post_category = new post_category();
        $this->Tag('post_categories', $post_category->GetByPostId($param->key));
        $this->Html($this->CreateView(array('post' => $post)));
    }

    public function actionAjaxPostsTable($param)
    {
        $param->show = true;
        $this->EmptyResponse(true);
        $this->_PostsTable($param);
    } 

    public function actionIndex($param)
    {
        $posts_category = new posts_category();
        $this->Tag('table', $this->_PostsTable($param));
        $this->Tag('categories', $posts_category->GetCategories(fsSession::GetInstance('LanguageId')));
    }
  
    public function actionCategories($param)
    {
        $posts_category = new posts_category();
        $this->Tag('categories', $posts_category->GetCategories(fsSession::GetInstance('LanguageId')));
    }
  
    public function actionAjaxCategoryTemplate($param)
    {
        $posts_category = new posts_category();
        $posts_category = $posts_category->Get(fsSession::GetInstance('LanguageId'), $param->category);
        if ($posts_category === null) {
            return $this->Json(array());
        }
        $this->Json(array(
            'short' => $posts_category['tpl_short'], 
            'full' => $posts_category['tpl_full'], 
            'auth' => $posts_category['auth'], 
        ));
    }
  
    private function _GetCategoriesAsArray($excludeIds)
    {
        $c = new posts_category();
        $all = $c->GetCategories(fsSession::GetInstance('LanguageId'), array('id_parent', 'title'), $excludeIds);
        $result = array();
        foreach($all as $id => $c) {
            $result[$id] = FunctionsPosts::GetFullCategoryName($all, $c);
        }
        return $result;
    }
  
    private function _InitAddEditCategory($parentsToExclude = array())
    {
        $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, array('Index'), array('php'));
        $templates_sp = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, array('ShortPost'), array('php'));
        $templates_fp = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, array('Post'), array('php'));
        $this->Tag('templates', $templates['NAMES']);
        $this->Tag('templates_ps', $templates_sp['NAMES']);
        $this->Tag('templates_pf', $templates_fp['NAMES']);
        $this->Tag('parents', $this->_GetCategoriesAsArray($parentsToExclude));
        $this->Tag('languages', $this->_languages);
    }
  
    public function actionAddCategory($param)
    {
        $this->_InitAddEditCategory();
    }
  
    public function actionEditCategory($param)
    {
        $posts_category = new posts_category();
        $posts_category = $posts_category->GetFullInfo($param->key);
        if (null == $posts_category) {
            return $this->_Referer();
        }                             
        $this->_InitAddEditCategory(array($param->key));                                                                
        $this->Html($this->CreateView(array('category' => $posts_category)));
    }
  
    public function actionDoAddPost($param)
    {
        if (!$this->_CheckPostData($param) || !$this->_CheckAlt($this->_tableName, $param->alt)
            || !$this->_UploadImage($param)) {
            return -1;
        }
        if (($postId = parent::actionDoAdd($param)) > 0) {
            fsCache::Clear('posts');
            $pc = new post_category();
            $pc->Add($postId, $param->id_category);
            $this->_UpdatePostInfo($postId, $param);
            return $postId;
        }
        return -1;
    }
    
    private function _UploadImage(&$param)
    {
        $param->image = empty($_FILES['image'][0]['tmp_name']) && $param->Exists('image') ? $param->image : '';
        $error = fsFunctions::CheckUploadFiles('image',
            array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'),
            false, true
        );
        if($error) {
            $this->_Referer();
            $this->Message(T('XMLcms_text_bad_image'));
            return false;
        }
        $newFile = '';
        if (!fsFunctions::UploadFiles('image', $this->_imagePath, $newFile)) {
            $this->_Referer();
            $this->Message(T('XMLcms_text_file_upload_error'));
            return false;
        }
        if($newFile != '') {
            $param->image = str_replace(PATH_ROOT, URL_ROOT_CLEAR, $this->_imagePath).$newFile;
        }
        return true;
    }
    
    public function actionDoEditPost($param)
    {
        if (!$this->_CheckPostData($param) || !$this->_CheckAlt($this->_tableName, $param->alt, $param->key)
            || !$this->_UploadImage($param) || parent::actionDoEdit($param) !== 0) {
            return;
        }
        $pc = new post_category();
        $pc->DeleteBy($param->key, 'id_post');
        $pc->Add($param->key, $param->id_category);
        $this->_UpdatePostInfo($param->key, $param);
    }
  
    private function _CheckAlt($table, $alts, $id = -1)
    {
        if(!class_exists($table)) {
            return false;
        }
        $tableObj = new $table();
        if(!$tableObj->IsUniqueAlt($alts, $id)) {
            $this->Message(T('XMLcms_text_unique_link'));
            $this->_Referer();
            return false;
        }
        return true;
    }
    
    private function _UpdateCategoryInfo($categoryId, $param)
    {
        $posts_category = new posts_category();
        $alts = $param->alt;
        $titles = $param->title;
        $keywords = $param->meta_keywords;
        $descriptions = $param->meta_description;
        foreach($this->_languages as $languageId => $languageName) {
            $posts_category->UpdateInfo($categoryId, $languageId, $titles[$languageId], $alts[$languageId], $keywords[$languageId], $descriptions[$languageId]);
        }
    }
    
    private function _UpdatePostInfo($postId, $param)
    {
        $alts = $param->alt;
        $titles = $param->title;
        $keywords = $param->meta_keywords;
        $descriptions = $param->meta_description;
        $html_short = $param->html_short;
        $html_full = $param->html_full;
        foreach($this->_languages as $languageId => $languageName) {
            $this->_table->UpdateInfo($postId, $languageId, $titles[$languageId], $alts[$languageId], $html_short[$languageId], $html_full[$languageId], $keywords[$languageId], $descriptions[$languageId]);
        }
    }
    
    public function actionDoAddCategory($param)
    {
        if(!$this->_CheckCategoryData($param) || !$this->_CheckAlt('posts_category', $param->alt)) {
            return -1;
        }
        if(($categoryId = parent::actionDoAdd($param)) > 0) {
            fsCache::Clear('posts_category');
            $this->_UpdateCategoryInfo($categoryId, $param);    
        }
        return $categoryId;
    }
  
    public function actionDoEditCategory($param)
    {
        if (!$this->_CheckCategoryData($param) || !$this->_CheckAlt('posts_category', $param->alt, $param->key) 
            || 0 !== parent::actionDoEdit($param)) {
            return -1;
        }
        $this->_UpdateCategoryInfo($param->key, $param);
        return $param->key;
    }

    public function actionConfig($param)
    {
        $this->Tag('settings', $this->settings);
    }
  
    public function actionDelete($param)
    {
        if(!$param->Exists('key', true) || $param->key < 1) {
            return $this->HttpNotFound();
        }
        if (parent::actionDelete($param) == 0) {
            $pc = new post_category();
            if ($param->table == 'posts_category') {
                $pcs = new posts_category();
                $pc->DeleteBy($param->key, 'id_category');
                $pcs->DeleteInfo($param->key);
                fsCache::Clear('posts_category');
            } else {
                $pc->DeleteBy($param->key, 'id_post');
                $this->_table->DeleteInfo($param->key);
                fsCache::Clear('posts');
            }
        }
    }
}