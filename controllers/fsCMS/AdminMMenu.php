<?php
class AdminMMenu extends AdminPanel
{
    protected $_tableName = 'menu';
  
    public function Init($request)
    {
        $this->Tag('title', T('XMLcms_text_menu'));
        parent::Init($request);
    }
  
    public function actionDoItems($param)
    {
        if($param->title == '' || $param->href == '') {
            $this->Message(T('XMLcms_text_need_all_data'));
            return $this->_Referer();
        }
        if ($param->Exists('key', true)) {
            if ($param->key < 1) {
              return $this->_Referer();
            } 
            $param->table = 'menu_items';
            parent::actionDoEdit($param);
        } else {
            parent::actionDoAdd($param);
        }
        $this->Redirect($this->_My('EditItems?last='.$param->menu_name));
        fsCache::Clear(FunctionsMenus::$CACHE_PREFIX.'_'.$param->menu_name);
    }
  
    public function actionDoAdd($param)
    {
        $param->name = fsFunctions::Chpu($param->name);
        if(!$this->_CheckUnique($param->name, 'name')) {
            return;
        }
        parent::actionDoAdd($param);
    }
  
    public function actionDoEdit($param)
    {
        $param->name = fsFunctions::Chpu($param->name);
        if(!$this->_CheckUnique($param->name, 'name', $param->key, 'name')) {
            return;
        }
        if(parent::actionDoEdit($param) === 0) {
            $menu_items = new menu_items();
            $menu_items->UpdateName($param->key, $param->name);
            fsCache::Clear(FunctionsMenus::$CACHE_PREFIX.'_'.$param->key);
        }
    }
  
    private function _Templates()
    {
        $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MMenu', true, false, array('Menu'), array('php'));
        return $templates['NAMES'];
    }
  
    public function actionAdd($param)
    {
        $this->Tag('templates', $this->_Templates());
        $this->Tag('current_template', $this->settings->default_template);
    }
  
    public function actionConfig($param)
    {
        $this->Tag('templates', $this->_Templates());
        $this->Tag('current_template', $this->settings->default_template);
    }
  
    public function actionEdit($param)
    {
        $this->_table->current = $param->key;
        if ($this->_table->name != $param->key) {
            return $this->_Referer();
        }
        $this->Tag('templates', $this->_Templates());
        $this->Tag('menu', $this->_table->result);
    }
  
    public function actionAjaxItemsInMenu($param)
    {
        $menu_items = new menu_items();
        $items = $menu_items->GetItemsInMenu($param->menu);
        $this->Html('<option value="0" selected>'.T('XMLcms_no').'</option>');
        foreach ($items as $item) {
            $this->Html('<option value="'.$item['id'].'">'.T($item['title']).'</option>');
        }
    }
  
    public function actionEditItems($param)
    {
        $menus = $this->_table->GetAll(true, false, array('title'));
        if (count($menus) == 0) {
            $this->Message(T('XMLcms_text_need_add_menu'));
            return $this->_Referer();
        }
        $this->Tag('last', $param->Exists('last') ? $param->last : '');
        $menu_items = new menu_items();
        $pages = new pages();
        $posts_category = new posts_category();
        $controller_menu = new controller_menu();
        
        $key = $param->Exists('key', true) ? $param->key : 0;
        $menu = $key == 0 ? $menus[0]['name'] : $menu_items->GetMenuName($key);
        $readyLinks = $pages->GetMenuPages(fsSession::GetInstance('LanguageId')); 
        $readyControllerLinks = $controller_menu->GetAll();
        $readyCategoryLinks = $posts_category->GetCategories(fsSession::GetInstance('LanguageId'), array('title'));
        
        unset($controller_menu);
        unset($pages);
        unset($posts_category);
        
        $this->Tag('key', $key);
        $this->Tag('menus', $menus);
        $this->Tag('menu', $menu);
        $this->Tag('menu_items', $menu_items->GetItemsInMenu($this->Tag('last') != '' ? $this->Tag('last') : $menu));
        $this->Tag('linksPages', $readyLinks);
        $this->Tag('linksPosts', $readyCategoryLinks);
        $this->Tag('linksControllers', $readyControllerLinks);
    }
  
    public function actionDelete($param)
    {
        $menu = '';
        $menu_items = new menu_items();
        if ($param->Exists('table')) {
            $menu = $menu_items->GetMenuName($param->key);
        }
        if (parent::actionDelete($param) == 0) {
            if (!$param->Exists('table')) {
                $menu_items->DeleteBy($param->key, 'menu_name');
            } else {
                $menu_items->NullParent($param->key);
                $this->Redirect($this->_My('EditItems?last='.$param->menu_name));
            }
            fsCache::Clear(FunctionsMenus::$CACHE_PREFIX.'_'.$param->menu_name);
        }
    }
  
    public function actionIndex($param)
    {
        $this->Tag('menus', $this->_table->GetAll(true, false, array('name')));
    }
} 