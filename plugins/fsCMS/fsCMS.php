<?php
/*
* Page navigation class.
* @package fsCMS
*/
class Paginator 
{
   /*
   * Get page navigation line.
   * @param string $link Base url.
   * @param string $param Page GET parameter.
   * @param integer $count Total records count.           
   * @param integer $pp (optional) Records per page. Default <b>20</b>.
   * @param integer $current (optional) Current page from 1. Default <b>1</b>.   
   * @param array $htmlAttributes (optional) Url HTML attributes. Default <b>empty array</b>.
   * @return string HTML code for page select. 
   */
    public static function Get($link, $param, $count, $pp = 20, $current = 1, $htmlAttributes = array())
    {
        $pCount = ($count % $pp == 0) ? (int)($count / $pp) : (int)($count / $pp) + 1;
        if($pCount < 2) {
          return '';
        }
        $attributes = ''; $PT = T('XMLcms_page');
        if(isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] .= ' paginator-item';
        } else {
            $htmlAttributes['class'] = 'paginator-item';
        }
        foreach($htmlAttributes as $attributeName => $attributeValue) {
          $attributes .= ' '.$attributeName.'="'.$attributeValue.'"';
        }
        $asReplace = preg_match('/^{.+}$/', $param);
        $html = '<span class="text-pages">'.T('XMLcms_pages').':</span> ';     
        $sym = false === strpos($link, '?') ? '?' : (substr($link, -1) == '&' ? '' : '&');
        for ($i = 1; $i <= $pCount; ++$i) {
          $html .= ($i != $current
                    ? "<a ".$attributes." href='".($asReplace ? str_replace($param, $i, $link) : $link.$sym.$param."=".$i)."' title='".$PT." ".$i."'>".$i.'</a>'
                    : '<b>'.$i.'</b>').
                ($i == $pCount ? '' : ' | ');
        }
        return $html;
    }
}

/*
 * fsCMS base class.
 */
class cmsController extends fsController 
{
  /* var boolean Flag for auto loading controller settings from database. */
  protected $_autoLoadSettings = true;
  /* var fsStruct Controller settings. */
  public    $settings = null;
  
  /*
   * Action when request is denied.
   * @param fsStruct $request User request.
   * @return mixed Developer value. 
   */
  public function OnDenied($request)
  {
    $this->Message(T('XMLcms_denied'));
    $this->Redirect(fsHtml::Url(CMSSettings::GetInstance('denied_page')));
    return '';  
  }
  
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
          $arr[$const['name']] = array('Value' => $const['value'], 'ReadOnly' => true);
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
  
    /*
    * Action before main conroller action.
    * @param fsStruct $request User request.
    * @return void 
    */
    public function Init($request)
    {
        $allow = fsFunctions::Explode("\n", fsSession::GetArrInstance(AUTH ? 'AUTH' : GUEST, 'type_allow'), true);
        $disallow = fsFunctions::Explode("\n", fsSession::GetArrInstance(AUTH ? 'AUTH' : GUEST, 'type_disallow'), true);
        $denied = false;
        foreach($disallow as $d) {
            if($d == '*' || preg_match('/^'.$request->controller.'\/\*$/', $d) 
                || preg_match('/^'.$request->controller.'\/'.$request->method.'$/', $d) 
                || preg_match('/^\*\/'.$request->method.'$/', $d)) {
                $denied = true;
                break;
            }
        }
        if($denied) {
            foreach($allow as $a) {
                if($d == '*' || preg_match('/^'.$request->controller.'\/\*$/', $a) 
                  || preg_match('/^'.$request->controller.'\/'.$request->method.'$/', $a) 
                  || preg_match('/^\*\/'.$request->method.'$/', $a)) {
                  $denied = false;
                  break;
                }
            }
        }
        if($denied) {
            $this->_accessDenied = true;
            $this->OnDenied();
        }
        parent::Init($request);
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

/*
 * fsCMS base class with auth.
 */
class cmsNeedAuthController extends cmsController
{
    /*
    * Action before main conroller action.
    * @param fsStruct $request User request.
    * @return void 
    */
    public function Init($request)
    {
        if (!AUTH) {
            return $this->Redirect(fsHtml::Url(URL_ROOT.'MAuth/Auth'));
        }
        parent::Init($request);
    }
}