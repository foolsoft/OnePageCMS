<?php
class MPosts extends cmsController
{
  protected $_tableName = 'posts';
  
  public function actionCategory($param = false)
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
      return '-';
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
                $this->settings->page_count,
                $param->page
               )
    );
    $page = array();
    $page['title'] = $posts_category->result->name;
    $page['meta_keywords'] = $posts_category->result->meta_keywords;
    $page['meta_description'] = $posts_category->result->meta_description;
    
    $template = $param->Exists('in_template') && file_exists($this->_Template('Inline'.$posts_category->result->tpl))
            ? $this->_Template('Inline'.$posts_category->result->tpl)
            : $this->_Template($posts_category->result->tpl);
            
    $html = $this->CreateView(array('page' => $page), $template);
    if ($param->Exists('in_template')) {
        return $html;
    } else {
        $this->Html($html);
    }
  }
  
  public function actionPost($Param = false)
  {
    $this->_table->Get($Param->post);
    if ($this->_table->result->id == '') {
      $this->Redirect(URL_ROOT.'404');
      return;  
    }
    $page = array();
    $page['title'] = $this->_table->result->title;
    $page['meta_keywords'] = $this->_table->result->meta_keywords;
    $page['meta_description'] = $this->_table->result->meta_description;
    $this->Tag('post', $this->_table->result);
    $this->Html($this->CreateView(array('page' => $page),
                                  $this->_Template($this->_table->result->tpl))
    );
  } 

}
?>