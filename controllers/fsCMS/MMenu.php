<?php
class MMenu extends cmsController
{
  private $_cache_file = '_menu.html'; 
  protected $_tableName = 'menu';

  private function _CacheFile($name, $level = 1, $parent = 0)
  {
    return '_'.fsSession::GetInstance('Language').'_'.$name.'_'.$parent.$level.$this->_cache_file;
  }

  private function _TagInit($param)
  {
    $this->Tag('level', $param->Exists('level', true) ? $param->level : 1);
    $this->Tag('parent', $param->Exists('parent') ? $param->parent : 0);    
    $this->Tag('class', $param->Exists('class') ? $param->class : 'menu');
    $this->Tag('menu', $param->name);
    $this->Tag('category', $param->category);
  }
  
  //Получение меню из новостей в указанной категории
  public function PostsMenu($param)
  {
    if (!$param->Exists('category', true)) {
      return '';
    }
    $this->_TagInit($param);
    $cache = $this->_CacheFile($param->name, $this->Tag('level'), $this->Tag('parent'));
    if (file_exists($cache)) {
       return file_get_contents($cache); 
    }
    $template = $this->_table->GetMenuTemplate($param->name);
    if ($template == '') {
      $template = $this->settings->default_template;
    }
    $posts = new posts();
    $posts = $posts->GetByCategory($param->category, 0, 100, true);
    if (count($posts) == 0) {
      return '';
    }
    $arr = array();
    foreach($posts as $post) {
      $arr['p'.$post['id']] = array(
        'href' => URL_ROOT.'post/'.$post['alt'],
        'title' => $post['title'],
        'child' => array()
      );
    }
    $this->Tag('items', $arr);
    $this->Html($this->CreateView(array(), $this->_Template($template)));
    fsCache::CreateOrUpdate($cache, $this->Html());
    return $this->Html(); 
  }
  
  //Получение меню
  public function Menu($param)
  {
    if (!$param->Exists('name')) {
      return '';
    }
    $this->_TagInit($param);
    $cache = $this->_CacheFile($param->name, $this->Tag('level'), $this->Tag('parent'));
    if (file_exists($cache)) {
       return file_get_contents($cache); 
    }
    if(!is_numeric($this->Tag('parent'))) {
      return '';
    }
    $template = $this->_table->GetMenuTemplate($param->name);
    if ($template == '') {
      $template = $this->settings->default_template;
    }
    $arr = MenuGenerator::GetArray('',
                                   'menu_items',
                                   'parent',
                                   'sample-menu',
                                   'id',
                                   'href',
                                   'title',
                                   array('order'),
                                   '`menu_name` = "'.$param->name.'" AND `parent` = "'.$this->Tag('parent').'"'
                                   );
    if (count($arr) == 0) {
      return '';
    }
    $this->Tag('items', $arr);
    $this->Html($this->CreateView(array(), $this->_Template($template)));
    fsCache::CreateOrUpdate($cache, $this->Html());
    return $this->Html();
  }
}
?>