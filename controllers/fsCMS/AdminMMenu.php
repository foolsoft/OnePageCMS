<?php
class AdminMMenu extends AdminPanel
{
  private $_cache_file = '_menu.html';
  protected $_tableName = 'menu';
  
  public function Init($request)
  {
    $this->Tag('title', T('XMLcms_text_menu'));
    parent::Init($request);
  }
  
  public function actionDoItems($Param)
  {
    if ($Param->Exists('key', true)) {
      if ($Param->key < 1) {
        $this->_Referer();
        return;
      } 
      $Param->table = 'menu_items';
      parent::actionDoEdit($Param);
    } else {
      parent::actionDoAdd($Param);
    }
    $this->Redirect($this->_My('EditItems?last='.$Param->menu_name));
    $this->_DeleteCache();
  }
  
  private function _DeleteCache()
  {
    fsFunctions::DeleteDirectory(PATH_CACHE);
  }
  
  public function actionDoAdd($Param)
  {
    $Param->name = fsFunctions::Chpu($Param->name);
    if(!$this->_CheckUnique($Param->name, 'name')) {
      return;
    }
    parent::actionDoAdd($Param);
  }
  
  public function actionDoEdit($Param)
  {
    $Param->name = fsFunctions::Chpu($Param->name);
    if(!$this->_CheckUnique($Param->name, 'name', $Param->key, 'name')) {
      return;
    }
    if(parent::actionDoEdit($Param) === 0) {
      $menu_items = new menu_items();
      $menu_items->UpdateName($Param->key, $Param->name);
      $this->_DeleteCache();
    }
  }
  
  private function _Templates()
  {
    $templates = fsFunctions::DirectoryInfo(PATH_TPL.CMSSettings::GetInstance('template').'/MMenu', true, false, 'Menu', array('php'));
    return $templates['NAMES'];
  }
  
  public function actionAdd($Param)
  {
    $this->Tag('templates', $this->_Templates());
    $this->Tag('current_template', $this->settings->default_template);
  }
  
  public function actionConfig($Param)
  {
    $this->Tag('templates', $this->_Templates());
    $this->Tag('current_template', $this->settings->default_template);
  }
  
  public function actionEdit($Param)
  {
    $this->_table->current = $Param->key;
    if ($this->_table->name != $Param->key) {
      $this->_Referer();
      return;
    }
    $this->Tag('templates', $this->_Templates());
    $this->Tag('menu', $this->_table->result);
  }
  
  public function actionAjaxItemsInMenu($Param)
  {
    $menu_items = new menu_items();
    $items = $menu_items->GetItemsInMenu($Param->menu);
    $this->Html('<option value="0" '.($selected == 0 ? 'selected' : '').'>'.T('XMLcms_no').'</option>');
    foreach ($items as $item) {
      $this->Html('<option value="'.$item['id'].'">'.$item['title'].'</option>');
    }
  }
  
  
  public function actionEditItems($Param)
  {
    $menus = $this->_table->GetAll(true, false, array('title'));
    if (count($menus) == 0) {
      $this->_Referer();
      $this->Message(T('XMLcms_text_need_add_menu'));
      return;
    }
    $this->Tag('last', $Param->Exists('last') ? $Param->last : '');
    $menu_items = new menu_items();
    $pages = new pages();
    $posts_category = new posts_category();
    $controller_menu = new controller_menu();
    
    $key = $Param->Exists('key', true) ? $Param->key : 0;
    $menu = $key == 0 ? $menus[0]['name'] : $menu_items->GetMenuName($key);
    $readyLinks = $pages->GetMenuPages(); 
    $readyControllerLinks = $controller_menu->GetAll();
    $readyCategoryLinks = $posts_category->GetAll();
    
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
  
  public function actionDelete($Param)
  {
    $menu = '';
    $menu_items = new menu_items();
    if ($Param->Exists('table')) {
      $menu = $menu_items->GetMenuName($Param->key);
    }
    if (parent::actionDelete($Param) == 0) {
      if (!$Param->Exists('table')) {
        $menu_items->DeleteBy($Param->key, 'menu_name');
      } else {
        $menu_items->NullParent($Param->key);
        $this->Redirect($this->_My('EditItems?last='.$Param->menu_name));
      }
      $this->_DeleteCache();
    }
  }
  
  public function actionIndex($Param)
  {
    $this->Tag('menus', $this->_table->GetAll(true, false, array('name')));
  }

} 
?>