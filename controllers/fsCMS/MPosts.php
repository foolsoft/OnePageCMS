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
    $posts_category = new posts_category();
    $posts_category->Get($param->category);
    if ($posts_category->result->id == '') {
      $this->HttpNotFound();
      return '';
    }
    if($posts_category->result->auth == 1 && !AUTH) {
      $this->Redirect(fsHtml::Url(CMSSettings::GetInstance('auth_need_page')));
      return '';     
    }
    $param->page = $param->Exists('page', true) ? $param->page : 1;
    $pcount = $param->Exists('count', true) && $param->count > 0 ? $param->count : $this->settings->page_count;  
    $this->Tag('posts',
               $this->_table->GetByCategory($posts_category->result->id,
                                            ($pcount * ($param->page - 1)),
                                            $pcount));  
    $count = $this->_table->GetCountByCategory($posts_category->result->id); 
    $this->Tag('pages', 
               Paginator::Get(
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
        'title' => $posts_category->result->name,
        'meta_keywords' => $posts_category->result->meta_keywords,
        'meta_description' => $posts_category->result->meta_description
    );
    $template = $param->Exists('in_template') && file_exists($this->_Template('Inline'.$posts_category->result->tpl))
            ? $this->_Template('Inline'.$posts_category->result->tpl)
            : $this->_Template($posts_category->result->tpl);
    $this->Tag('childs', $posts_category->GetByParent($posts_category->result->id));
    $html = $this->CreateView(array('page' => $page), $template);
    if($param->Exists('ajax_pages')) {
        $html = '<div id="category-ajax-'.$param->category.'">'.$html.'</div>';
        $this->_AddMyScriptsAndStyles(true, false, URL_THEME_JS);
    }
    return $this->Html($html);
  }
  
  public function actionPost($param)
  {
    $this->_table->Get($param->post);
    if ($this->_table->result->id == '') {
      return $this->HttpNotFound();
    }
    if($this->_table->result->auth == 1 && !AUTH) {
      return $this->Redirect(fsHtml::Url(CMSSettings::GetInstance('auth_need_page')));     
    }
    $page = array(
        'title' => $this->_table->result->title,
        'meta_keywords' => $this->_table->result->meta_keywords,
        'meta_description' => $this->_table->result->meta_description
    );
    $post_category = new post_category();
    $this->Tag('post', $this->_table->result);
    $this->Tag('categories', $post_category->GetByPostId($this->_table->result->id));
    $this->Html($this->CreateView(array('page' => $page), $this->_Template($this->_table->result->tpl)));
  } 
}