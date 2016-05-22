<?php
/**
 * fsCMS base class.
 */
class cmsController extends fsController 
{
  /** @var boolean Flag for auto loading controller settings from database. */
  protected $_autoLoadSettings = true;
  /** @var fsStruct Controller settings. */
  public    $settings = null;
  
  /**
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
  
  /**
  * Get full path for template folder.
  * @param string $folder Needed template folder.
  * @return string Template path. 
  */
  protected function _TemplatePath($folder = '')
  { 
    if ($folder === '') {
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
  
  
    /**
    * Generate page HTML code.
    * @param array $params (optional) Additional variables. Default <b>empty array</b>.
    * @param string $template (optional) Template name. If empty get default template using controller and method name. Default <b>empty string</b>.    
    * @param boolean $show (optional) Flag for auto echo of result. Default <b>false</b>.
    * @param boolean $adminMode (optional) Flag for skipping some actions. Default <b>false</b>.
    * @return string Html code of page. 
    */
    public function CreateView($params = array(), $template = '', $show = false, $adminMode = false) 
    {
      $params['myLink'] = $this->_link;
      $params['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
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
  
    /**
    * Action before main conroller action.
    * @param fsStruct $request User request.
    * @return void 
    */
    public function Init($request)
    {
        $allow = fsFunctions::Explode("\n", fsSession::GetArrInstance(AUTH ? 'AUTH' : 'GUEST', 'type_allow'), true);
        $disallow = fsFunctions::Explode("\n", fsSession::GetArrInstance(AUTH ? 'AUTH' : 'GUEST', 'type_disallow'), true);
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
            $this->OnDenied($request);
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
        
        if(!defined('URL_ATHEME_CSS')) {
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
}