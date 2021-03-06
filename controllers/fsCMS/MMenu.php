<?php
class MMenu extends cmsController
{
    protected $_tableName = 'menu';

    private function _CacheFile($name, $level = 1, $parent = 0)
    {
        return FunctionsMenus::$CACHE_PREFIX.'_'.$name.'_'.str_replace(array('/', ' '), '', fsSession::GetInstance('Template')).'_'.fsSession::GetInstance('Language').'_'.$parent.$level.'.html';
    }

    private function _TagInit($param)
    {
        $this->Tag('level', $param->Exists('level', true) ? $param->level : 1);
        $this->Tag('parent', $param->Exists('parent') ? $param->parent : 0);    
        $this->Tag('class', $param->Exists('class') ? $param->class : 'menu');
        $this->Tag('menu', $param->name);
        $this->Tag('category', $param->category);
    }
  
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
        $template = $param->Exists('template') ? $param->template : $this->_table->GetMenuTemplate($param->name);
        if ($template == '') {
            $template = $this->settings->default_template;
        } 
        $arr = fsMenuGenerator::GetArray('menu_items',
            'parent',
            'id',
            'href',
            'title',
            array('position'),
            '`menu_name` = "'.$param->name.'" AND `parent` = "'.$this->Tag('parent').'"',
            array('parent', 'target'));
        if (count($arr) == 0) {
            return '';
        }
        $this->Tag('items', $arr);
        $this->Html($this->CreateView(array(), $this->_Template($template)));
        fsCache::CreateOrUpdate($cache, $this->Html());
        return $this->Html();
    }
}