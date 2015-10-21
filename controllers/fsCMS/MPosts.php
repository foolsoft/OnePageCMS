<?php
class MPosts extends cmsController
{
    protected $_tableName = 'posts';
  
    public static function Sitemap($param) 
    {
        $result = array();
        $db = new posts();
        $db->GetAllPosts($param->languageId);
        while($db->Next()) {
            $result[] = array(
                'loc' => fsHtml::Url(URL_ROOT.'post/'.$db->result->mysqlRow['alt']),
                'changefreq' => 'monthly',
                'priority' => '1.0',
                'lastmod' => $db->result->mysqlRow['date_modify']
            );
        }
        $db = new posts_category();
        $db->GetAllCategories($param->languageId);
        while($db->Next()) {
            $result[] = array(
                'loc' => fsHtml::Url(URL_ROOT.'posts/'.$db->result->mysqlRow['alt']),
                'changefreq' => 'monthly',
                'priority' => '1.0',
            );
        }
        return $result;
    }
    
    public function PostsCount($param)
    {
        if(!$param->Exists('category', true) || $param->category < 0) {
            return -1;
        }
        $ids = array();
        return $this->_table->GetPostCountInCategory($param->category, $param->Exists('childs'), $ids);
    }
    
    public function Random($param)
    {
        if(!$param->Exists('category', true) || $param->category < 0) {
            return '';
        }
        $limit = $param->TryGetPositiveNumber('limit', 3);
        $template = $param->TryGetNotEmpty('template', 'Random');
        $posts_category = new posts_category();
        $categoies = array($param->category);
        $posts_category->GetChilds(&$categoies, $param->category);
        $posts = $this->_table->GetRandom(fsSession::GetInstance('LanguageId'), $limit, $categoies);
        return $this->CreateView(array('posts' => $posts), $this->_Template($template));
    }
    
    public function actionCategory($param)
    {
        if (!$param->Exists('category')) {
            return $this->HttpNotFound();
        }
        if ($param->category == '') {
            $param->category = ALL_TYPES;
        }
        $pc = new posts_category();
        $posts_category = $pc->Get(fsSession::GetInstance('LanguageId'), $param->category);
        $param->exclude = $param->Exists('exclude') ? explode('|', $param->exclude) : array();
        if ($posts_category === null || in_array($posts_category['id'], $param->exclude)) {
            $this->HttpNotFound();
            return '';
        }
        if($posts_category['auth'] == 1 && !AUTH) {
            $this->Redirect(fsHtml::Url(CMSSettings::GetInstance('auth_need_page')), 401);
            return '';     
        }
        
        $param->page = $param->Exists('page', true) ? $param->page : 1;
        $pcount = $param->Exists('count', true) && $param->count > 0 ? $param->count : $this->settings->page_count;  
        $count = $this->_table->GetCountByCategory($posts_category['id']); 
        
        $this->Tag('posts', $this->_table->GetByCategory(fsSession::GetInstance('LanguageId'), $posts_category['id'], $param->page, $pcount, true, $param->exclude));  
        $this->Tag('childs', $pc->GetByParent(fsSession::GetInstance('LanguageId'), $posts_category['id']));
        $this->Tag('pages', $param->Exists('pages') && $param->pages == 'false' ? '' : 
               fsPaginator::Get(
                fsHtml::Url(URL_ROOT.'posts/'.$param->category),
                'page',
                $count,
                $pcount,
                $param->page,
                $param->Exists('ajax_pages') 
                       ? array(
                           'data-category' => $param->category, 
                           'class' => 'posts-page-ajax',
                           'data-count' => $pcount,
                           'onclick' => 'return ajaxChangeCategoryPage(this);'
                        ) 
                       : array()
               )
        );
        $this->Tag('next_page', fsPaginator::NextPage(
            fsHtml::Url(URL_ROOT.'posts/'.$param->category),
            'page',
            $count,
            $pcount,
            $param->page
        ));
        
        $page = array(
            'title' => $posts_category['title'],
            'meta_keywords' => $posts_category['meta_keywords'],
            'meta_description' => $posts_category['meta_description']
        );
        $template = $param->Exists('in_template') && file_exists($this->_Template('Inline'.$posts_category['tpl']))
            ? $this->_Template('Inline'.$posts_category['tpl'])
            : $this->_Template($posts_category['tpl']);
        
        $html = $this->CreateView(array('page' => $page), $template);
        
        if($param->Exists('ajax_pages')) {
            $html = '<div id="category-ajax-'.$param->category.'">'.$html.'</div>';
            $this->_AddMyScriptsAndStyles(true, false, URL_THEME_JS);
        }
        
        return $this->Html($html);
    }
  
    public function actionPost($param)
    {
        $page = $this->_table->Get(fsSession::GetInstance('LanguageId'), $param->post);
        if ($page == null || (!AUTH_ADMIN && $page['active'] == '0')) {
            return $this->HttpNotFound();
        }
        if($page['auth'] == 1 && !AUTH) {
            return $this->Redirect(fsHtml::Url(CMSSettings::GetInstance('auth_need_page')), 401);     
        }
        
        $post_category = new post_category();
        $posts_category = new posts_category();
        $categories = $post_category->GetByPostId($page['id']);
        $categoriesInfo = array();
        foreach($categories as $category) {
            $categoriesInfo[] = $posts_category->Get(fsSession::GetInstance('LanguageId'), $category);
        }
        
        $page['meta_keywords'] = $page['meta_keywords'] != '' ? $page['meta_keywords'] : ($categoryInfo['meta_keywords'] != '' ? $categoryInfo['meta_keywords'] : CMSSettings::GetInstance('default_keywords')); 
        $page['meta_description'] = $page['meta_description'] != '' ? $page['meta_description'] : ($categoryInfo['meta_description'] != '' ? $categoryInfo['meta_description'] : CMSSettings::GetInstance('default_description')); 
        
        $this->Tag('categories', $categories);
        $this->Html($this->CreateView(array('page' => $page), $this->_Template($page['tpl'])));
    } 
}