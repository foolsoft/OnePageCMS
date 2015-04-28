<?php
class MPosts extends cmsController
{
    protected $_tableName = 'posts';
  
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
        if ($posts_category === null) {
            $this->HttpNotFound();
            return '';
        }
        if($posts_category['auth'] == 1 && !AUTH) {
            $this->Redirect(fsHtml::Url(CMSSettings::GetInstance('auth_need_page')));
            return '';     
        }
        
        $param->page = $param->Exists('page', true) ? $param->page : 1;
        $pcount = $param->Exists('count', true) && $param->count > 0 ? $param->count : $this->settings->page_count;  
        $count = $this->_table->GetCountByCategory($posts_category['id']); 
        
        $this->Tag('posts', $this->_table->GetByCategory(fsSession::GetInstance('LanguageId'), $posts_category['id'], $param->page, $pcount));  
        $this->Tag('childs', $pc->GetByParent(fsSession::GetInstance('LanguageId'), $posts_category['id']));
        $this->Tag('pages', 
               fsPaginator::Get(
                URL_ROOT.'posts/'.$param->category,
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
        $page = array(
            'title' => htmlspecialchars_decode($posts_category['title']),
            'meta_keywords' => htmlspecialchars_decode($posts_category['meta_keywords']),
            'meta_description' => htmlspecialchars_decode($posts_category['meta_description'])
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
        if ($page == null) {
            return $this->HttpNotFound();
        }
        if($page['auth'] == 1 && !AUTH) {
            return $this->Redirect(fsHtml::Url(CMSSettings::GetInstance('auth_need_page')));     
        }
        
        $page['title'] = htmlspecialchars_decode($page['title']);
        $page['html_short'] = htmlspecialchars_decode($page['html_short']);
        $page['html_full'] = htmlspecialchars_decode($page['html_full']);
        $page['meta_description'] = htmlspecialchars_decode($page['meta_description']);
        $page['meta_keywords'] = htmlspecialchars_decode($page['meta_keywords']);
        
        $post_category = new post_category();
        $this->Tag('categories', $post_category->GetByPostId($page['id']));
        $this->Html($this->CreateView(array('page' => $page), $this->_Template($page['tpl'])));
    } 
}