<?php
//Генерация списка номеров страниц с материалом
//@ $link основная ссылка
//@ $param нзвание параметра номера страницы
//@ $count общее число записей
//@ $pp число записей на странице
//@ $current тукущая страница 
class Paginator 
{
 public static function Get($link, $param, $count, $pp = 20, $current = 1)
 {
    $pCount = ($count % $pp == 0) ? (int)($count / $pp) : (int)($count / $pp) + 1;
    if($pCount < 2) {
      return '';
    }
    $asReplace = preg_match('/^{.+}$/', $param);
    $html = '<span class="text-pages">'.T('XMLcms_pages').':</span> ';     
    $PT = T('XMLcms_page');
    $sym = false === strpos($link, '?') ? '?' : (substr($link, -1) == '&' ? '' : '&');
    for ($i = 1; $i <= $pCount; ++$i) {
      $html .= ($i != $current
                ? "<a href='".($asReplace ? str_replace($param, $i, $link) : $link.$sym.$param."=".$i)."' ".$i."' title='".$PT." ".$i."' class='paginator-item'>".$i.'</a>'
                : '<b>'.$i.'</b>').
            ($i == $pCount ? '' : ' | ');
    }
    return $html;
  } 
}

class cmsController extends fsController 
{
  protected $_autoLoadSettings = true;
  public    $settings = null;
  
  protected function _TemplatePath($folder = null)
  { 
    if ($folder === null) {
      $folder = get_class($this);
    }
    $path = PATH_TPL.fsSession::GetInstance('Template').'/'.$folder;
    if (!is_dir($path)) {
      $path = PATH_TPL.'fsCMS/default/'.$folder;
      if (!is_dir($path)) {
        $path = parent::_TemplatePath($folder);
      }
    }
    return fsFunctions::Slash($path);
  }
  
  public function CreateView($params = array(), $template = '', $show = false, $adminMode = false) 
  {
    $params['myLink'] = $this->_link;
    $params['referer'] = $_SERVER['HTTP_REFERER'];
    return parent::CreateView($params, $template, $show, $adminMode);
  }
  
  private function _LoadConstants()
  {
    $constants = new constants();
	$constants = $constants->GetAll();
    $arr = array();
	foreach ($constants as $const) {
      $arr[$const['name']] = array('Value' => $const['value'],
                                  'ReadOnly' => true);
    }
    unset($constants);
    $this->Tag('constants', new fsStruct($arr));
  }
  
  protected function _LoadSettings($className = '') 
  {
    $className = empty($className) ? get_class($this) : $className;
    $t = new controller_settings();
    $t->Load($className);
    $config = array();
    while ($t->next()) {
      if (!isset($config['controller'])) {
        $config['controller'] = array('ReadOnly' => true, 'Value' => $t->result->controller);
      }
      $config[$t->result->name] = array('ReadOnly' => true, 'Value' => $t->result->value);        
    }
    return new fsStruct($config);
  }
  
  public function __construct()
  {
    parent::__construct();
    if ($this->_autoLoadSettings) {
      $this->settings = $this->_LoadSettings();
    }
    $this->_LoadConstants();
    CMSSettings::Init($this->_LoadSettings('Panel'));
    if (!fsSession::Exists('Template')) {
      fsSession::Create('Template', CMSSettings::GetInstance('template'));
    }
    define('URL_ATHEME_CSS', URL_CSS.CMSSettings::GetInstance('template_admin').'/');
    define('URL_ATHEME_JS',  URL_JS.CMSSettings::GetInstance('template_admin').'/');
    define('URL_ATHEME_IMG', URL_IMG.CMSSettings::GetInstance('template_admin').'/');
    define('URL_THEME_CSS',  URL_CSS.fsSession::GetInstance('Template').'/');
    define('URL_THEME_JS',   URL_JS.fsSession::GetInstance('Template').'/');
    define('URL_THEME_IMG',  URL_IMG.fsSession::GetInstance('Template').'/');
    define('PATH_ATHEME_CSS', PATH_CSS.CMSSettings::GetInstance('template_admin').'/');
    define('PATH_ATHEME_JS',  PATH_JS.CMSSettings::GetInstance('template_admin').'/');
    define('PATH_ATHEME_IMG', PATH_IMG.CMSSettings::GetInstance('template_admin').'/');
    define('PATH_THEME_CSS',  PATH_CSS.fsSession::GetInstance('Template').'/');
    define('PATH_THEME_JS',   PATH_JS.fsSession::GetInstance('Template').'/');
    define('PATH_THEME_IMG',  PATH_IMG.fsSession::GetInstance('Template').'/');
  }
}

class cmsNeedAuthController extends cmsController
{
    public function Init($param)
    {
        if (!AUTH) {
            $this->Redirect(fsHtml::Url(URL_ROOT.'MAuth/Auth'));
            return;
        }
        parent::Init($param);
    }
}
?>