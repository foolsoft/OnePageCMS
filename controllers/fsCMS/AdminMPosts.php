<?php
class AdminMPosts extends AdminPanel 
{
  protected $_tableName = 'posts';
  
  private function _CheckCategoryData(&$Param)
  {
    if ($Param->name == '' || $Param->alt == '') {
      $this->Message(T('XMLcms_text_need_all_data'));
      $this->_Referer();
      return false;
    }
    if ($Param->Exists('call')) {
      $Param->Delete('call');
    }
    return true;
  }
  
  private function _CheckPostData(&$Param)
  {
    $id_category = $Param->id_category;
    if ($Param->title == '' ||
        $Param->alt == '' ||
        !is_array($id_category) ||
        count($id_category) == 0) {
      $this->Message(T('XMLcms_text_need_all_data'));
      $this->_Referer();
      return false;
    }
    $Param->alt = fsFunctions::Chpu($Param->alt);
    $Param->active = $Param->Exists('active') ? 1 : 0;
    if ($Param->time == '') {
      $Param->time = date('H:i:s');
    }
    $Param->date = $Param->date.' '.$Param->time;
    if (!fsValidator::Check($Param->date, 'TIMEDATE')) {
      $Param->date = date('Y-m-d H:i:s');
    }
    if (in_array(ALL_TYPES, $id_category)) {
      $Param->id_category = array(ALL_TYPES);
    }
    if ($Param->Exists('call')) {
      $Param->Delete('call');
    }
    return true;
  }
  
  private function _PostsTable($Param) 
  {
    $ppage = 15;
    $category = $Param->Exists('category') ? $Param->category : ALL_TYPES;  
    $page = $Param->Exists('page', true) ? $Param->page : 1; 
    $inCategory = $this->_table->GetByCategory($category, ($page - 1) * $ppage, $ppage, false);
    $count = $this->_table->GetCountByCategory($category, false); 
    $this->Tag('posts', $inCategory);
    $this->Tag('pages',
               Paginator::Get(
                $this->_My('Index/category/'.$category.'/'),
                'page',
                $count,
                $ppage,
                $page
               )
    );
    return $this->CreateView(array(), $this->_Template('Table'), $Param->Exists('show'));
  }
  
  public function Init($request)
  {
    $this->Tag('title', T('XMLcms_text_posts'));
    parent::Init($request);
  }

  public function actionAddPost($Param)
  {
    $posts_category = new posts_category();
    $posts_category = $posts_category->GetAll(true, false, array('name'));
    if (count($posts_category) < 2) {
      $this->_Referer();
      $this->Message(T('XMLcms_text_no_category_found'));
      return;
    }                       
    $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'Post', array('php'));
    $templates_short = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'ShortPost', array('php'));
    $this->Tag('templates', $templates['NAMES']);
    $this->Tag('templates_short', $templates_short['NAMES']);
    $this->Tag('categories', $posts_category);
  }
  
  public function actionEditPost($Param)
  {
    $this->_table->current = $Param->key;
    if ($this->_table->id != $Param->key) {
      $this->_Referer();
      return;
    }
    $posts_category = new posts_category();
    $post_category = new post_category();
    $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'Post', array('php'));
    $templates_short = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'ShortPost', array('php'));
    $this->Tag('templates', $templates['NAMES']);
    $this->Tag('templates_short', $templates_short['NAMES']);
    $this->Tag('categories', $posts_category->GetAll(true, false, array('name')));
    $this->Tag('post', $this->_table->result);
    $this->Tag('post_categories', $post_category->GetByPostId($Param->key));
  }

  public function actionAjaxPostsTable($Param)
  {
    $Param->show = true;
    $this->_response->empty = true;
    $this->_PostsTable($Param);
  } 

  public function actionIndex($Param)
  {
    $posts_category = new posts_category();
    $this->Tag('table', $this->_PostsTable($Param));
    $this->Tag('categories', $posts_category->GetAll(true, false, array('name')));
  }
  
  public function actionCategories($Param)
  {
    $posts_category = new posts_category();
    $this->Tag('categories', $posts_category->GetAll(true, false, array('name')));
  }
  
  public function actionAjaxCategoryTemplate($Param)
  {
    if (!$Param->Exists('category', true)) {
        $this->Html('{}');
        return;
    }
    $posts_category = new posts_category();
    $posts_category->Get($Param->category);
    $this->Html('{"short":"'.$posts_category->result->tpl_short.'","full":"'.$posts_category->result->tpl_full.'"}');
  }
  
  public function actionAddCategory($Param)
  {
    $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'Index', array('php'));
    $templates_sp = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'ShortPost', array('php'));
    $templates_fp = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'Post', array('php'));
    $this->Tag('templates', $templates['NAMES']);
    $this->Tag('templates_ps', $templates_sp['NAMES']);
    $this->Tag('templates_pf', $templates_fp['NAMES']);
  }
  
  public function actionEditCategory($Param)
  {
    $posts_category = new posts_category();
    $posts_category->current = $Param->key;
    if ($Param->key != $posts_category->id) {
      $this->_Referer();
      return;
    }   
    $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'Index', array('php'));
    $templates_sp = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'ShortPost', array('php'));
    $templates_fp = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MPosts/', true, false, 'Post', array('php'));
    $this->Tag('templates', $templates['NAMES']); 
    $this->Tag('category', $posts_category->result);
    $this->Tag('templates_ps', $templates_sp['NAMES']);
    $this->Tag('templates_pf', $templates_fp['NAMES']);
  }
  
  public function actionDoAddPost($Param)
  {
    if (!$this->_CheckPostData($Param) ||
        !$this->_CheckUnique($Param->alt, 'alt')) {
      return -1;
    }
    $postId = parent::actionDoAdd($Param);
    if ($postId > 0) {
      $pc = new post_category();
      $pc->Add($postId, $Param->id_category);
      fsFunctions::DeleteDirectory(PATH_CACHE);
    }
  }
  
  public function actionDoEditPost($Param)
  {
    if (!$this->_CheckPostData($Param) ||
        !$this->_CheckUnique($Param->alt, 'alt', $Param->key, 'id')) {
      return -1;
    }
    $pc = new post_category();
    $pc->DeleteBy($Param->key, 'id_post');
    $pc->Add($Param->key, $Param->id_category);
    fsFunctions::DeleteDirectory(PATH_CACHE);
    return parent::actionDoEdit($Param);
  }
  
  public function actionDoAddCategory($Param)
  {
    if (!$this->_CheckCategoryData($Param) ||
        !$this->_CheckUnique($Param->alt, 'alt', false, false, 'posts_category')) {
      return -1;
    }
    return parent::actionDoAdd($Param);
  }
  
  public function actionDoEditCategory($Param)
  {
    if (!$this->_CheckCategoryData($Param) ||
        !$this->_CheckUnique($Param->alt, 'alt', $Param->key, 'id', 'posts_category')) {
      return -1;
    }
    return parent::actionDoEdit($Param);
  }

  public function actionConfig($Param)
  {
    $this->Tag('settings', $this->settings);
  }
  
  public function actionDelete($Param)
  {
    if (parent::actionDelete($Param) == 0)
    {
      $pc = new post_category();
      if ($Param->Exists('table')) {
        switch ($Param->table) {
          case 'posts_category':
            $pc->DeleteBy($Param->key, 'id_category');
            break;
        }  
      } else {
        $pc->DeleteBy($Param->key, 'id_post');
      }
      fsFunctions::DeleteDirectory(PATH_CACHE);
    }
  }

}

?>