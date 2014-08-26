<?php
class MPosts extends cmsController
{
  protected $_tableName = 'posts';
  
  public function actionCategory($Param = false)
  {
    if (!$Param->Exists('category')) {
      $this->Redirect(URL_ROOT.'404');
      return;
    }
    if ($Param->category == '') {
      $Param->category = ALL_TYPES;
    }
    $posts_category = new posts_category();
    $posts_category->Get($Param->category);
    if ($posts_category->result->id == '') {
      $this->Redirect(URL_ROOT.'404');
      return '-';
    }
    $Param->page = $Param->Exists('page', true) ? $Param->page : 1;
    $pcount = $Param->Exists('count', true) && $Param->count > 0 ? $Param->count : $this->settings->page_count;  
    $this->Tag('posts',
               $this->_table->GetByCategory($posts_category->result->id,
                                            ($pcount * ($Param->page - 1)),
                                            $pcount));  
    $count = $this->_table->GetCountByCategory($posts_category->result->id); 
    $this->Tag('pages', 
               Paginator::Get(
                URL_ROOT.'posts/'.$Param->category.'/',
                'page',
                $count,
                $this->settings->page_count,
                $Param->page
               )
    );
    $page = array();
    $page['title'] = $posts_category->result->name;
    $page['meta_keywords'] = $posts_category->result->meta_keywords;
    $page['meta_description'] = $posts_category->result->meta_description;
    $html = $this->CreateView(array('page' => $page),
                                  $this->_Template($posts_category->result->tpl));
    if ($Param->Exists('in_template')) {
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