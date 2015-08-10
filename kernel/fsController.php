<?php
/**
* @property-read Response $response
* @package fsKernel
*/
class fsController 
{
  /** @var Response Instance of response object  */
  protected $_response  = null;  
  
  /** @var View Instance of view object */
  protected $_view      = null;  
  
  /** @var string Url to current controller */
  protected $_link      = '';    
  
  /** @var string Controller MySQL table name or empty string if table not exits */
  protected $_tableName = ''; 
     
  /** @var fsDBTableExtension|object|null Instance of MySQL table as controller model or fsDBTableExtension object or null if $_tableName is empty */
  protected $_table     = null; 
  
  /** @var boolean Flag for denied action. Default <b>false</b>. */
  protected $_accessDenied = false;
  
  /**
  * Get controller denied status.   
  * @api
  * @since 1.0.0
  * @return boolean Controller status.      
  */
  public function IsDenied()
  {
    return $this->_accessDenied;
  }
  
  /**
  * Getting Url for method of controller.   
  * @api
  * @since 1.0.0
  * @param string $methodName (optional) Name of controller method for Url generating. If empty use 'Index' method. Default empty string.
  * @param boolean $action (optional) If true generate url with 'action' prefix. Default <b>false</b>.   
  * @return string Full url for method.      
  */
  final protected function _My($methodName = '', $action = false)
  {
    $method = '';
    if ($methodName != '') {
      $method = ($action ? 'action' : '').$methodName;
    } else {
      $method = 'Index';
    }
    return fsHtml::Url($this->_link.$method);
  }
  
  /**
  * Stop execution of PHP.   
  * @api
  * @since 1.0.0
  * @param string|boolean $url (optional) Url for redirect. If value is <b>false</b> only stopping PHP. Default <b>false</b>.
  * @return void      
  */
  final protected function _Stop($url = false)
  {
    if (is_string($url)) {
      fsFunctions::Redirect($url);
    }
  }
  
  /**
  * Generate HTML link and script tags for including controller css and js files into template.    
  * @api
  * @since 1.0.0
  * @param boolean $js (optional) Flag for including script file. Default <b>true</b>.
  * @param boolean $css (optional) Flag for including styles file. Default <b>true</b>.
  * @param string|bollean $urlJs (optional) Url to folder with controller script file. If <b>false</b> uses URL_JS constant value. Default <b>false</b>.
  * @param string|bollean $urlCss (optional) Url to folder with controller styles file. If <b>false</b> uses URL_CSS constant value. Default <b>false</b>.
  * @return void      
  */
  final protected function _AddMyScriptsAndStyles($js = true, $css = true, $urlJs = false, $urlCss = false) 
  {
    if($urlJs === false) {
      $urlJs = URL_JS;
    }
    if($urlCss === false) {
      $urlCss = URL_CSS;
    }
    if($js) {
      $jqFormFile = '<script src="'.$urlJs.get_class($this).'.js" type="text/javascript"></script>';
      if (strpos($_REQUEST['includeHead'], $jqFormFile) === false) {
        $_REQUEST['includeHead'] .= $jqFormFile;
      }
    }
    if($css) {
      $cssControllerFile = '<link href="'.$urlCss.get_class($this).'.css" rel="stylesheet" type="text/css" />';
      if (strpos($_REQUEST['includeHead'], $cssControllerFile) === false) {
          $_REQUEST['includeHead'] .= $cssControllerFile;
      }
    }
  }
  
  /**
  * Generate answer with redirection to page 404, which settled in fsConfig::url_404    
  * @api
  * @since 1.0.0
  * @return string Url for redirect.      
  */
  public function HttpNotFound() 
  {
    header('HTTP/1.0 404 Not Found');
    die('<meta http-equiv="refresh" content="0;'.fsConfig::GetInstance('url_404').'">');
  }
  
  /**
  * Get request referer or put redirect to referer in to response   
  * @api
  * @since 1.0.0
  * @param bollean $setRedirect (optional) If <b>true</b> generate response with redirect. Default <b>true</b>.
  * @return string Referer address.      
  */ 
  final protected function _Referer($setRedirect = true)
  {
    $referer = !fsFunctions::NotEmpty($_SERVER['HTTP_REFERER']) ? URL_ROOT : $_SERVER['HTTP_REFERER'];
    if ($setRedirect) {
      $this->Redirect($referer);
    }
    return $referer;
  }
  
  /**
  * Get folder path in template directory   
  * @api
  * @since 1.0.0
  * @param string $folder (optional) Folder name in template directory. If empty string will use controller name. Default <b>empty string</b>.
  * @return string Full path to needed folder.      
  */
  protected function _TemplatePath($folder = '')
  {
    if ($folder === '') {
      $folder = get_class($this);
    }
    if (is_dir(PATH_TPL.$folder)) {
      return fsFunctions::Slash(PATH_TPL.$folder);
    } else {
      return '/';
    }
  }
  
  /**
  * Get path for template file   
  * @api
  * @since 1.0.0
  * @param string $file (optional) File name in template directory. If empty string will use controller method name. Default <b>empty string</b>.
  * @param string $folder (optional) Folder name in template directory. If empty string will use controller name. Default <b>empty string</b>.
  * @return string Full path to template file.      
  */
  protected function _Template($file = '', $folder = '')
  {
    if ($file === '') {
      $file = empty($_REQUEST['method']) ? 'Index' : $_REQUEST['method'];
    }
    $file = fsFunctions::Slash($file, EXT_TPL);
    return $this->_TemplatePath($folder).$file;
  }
  
  /**
  * Generate HTML code   
  * @api
  * @since 1.0.0
  * @param array $params (optional) Additional variables accessible in template. Default <b>array()</b>.
  * @param string $template (optional) Full path to template file. If empty will use response template variable. Default empty string.
  * @param boolean $show (optional) If <b>true</b> the result of function will automatically show using 'echo'. Default <b>false</b>.
  * @param boolean $adminMode (optional) If <b>true</b> compiler will skip Html and Language patterns. Default <b>false</b>.
  * @return string Html code.      
  */ 
  public function CreateView($params = array(), $template = '', $show = false, $adminMode = false) 
  {
    if (empty($template)) {
      $template = $this->_response->template;
      if (empty($template)) {
        $template = $this->_Template();  
      } else {
        $template = PATH_TPL.$template;  
      }                     
    }
    if (file_exists($template) && is_file($template)) {
	  $params['tag'] = $this->_response->tag;
      return $this->_view->CreateView($template, $params, $show, $adminMode);
    } else {
      return 'Template: "'.$template.'" not found!';  
    }
  }
  
  /**
  * Uniqueness check for some value in MySQL table 
  * @api
  * @since 1.0.0
  * @example _CheckUnique($newValueForUniqueField, 'uniqueField') or _CheckUnique($userLogin, 'login', $userId, 'id')
  * @param string $valueToCheck Value to be checked.
  * @param string $fieldDb (optional) MySQL table key fow where clause. If <b>empty string</b> will use primary key of table. Default <b>empty string</b>.
  * @param string $valueToCheckResult (optional) Additional value for checking. If <b>empty string</b> will use $valueToCheck value. Default <b>empty string</b>.
  * @param string $findedFieldValue (optional) Additional table key for checking. If <b>empty string</b> will use $fieldDb value. Default <b>empty string</b>.
  * @param string $tableName (optional) MySQL table for request. If <b>empty string</b> will use controller table. Default <b>empty string</b>.
  * @return boolean Result of checking. If <b>true</b> value is unique.      
  */ 
  protected function _CheckUnique($valueToCheck, $fieldDb = '', $valueToCheckResult = '', $findedFieldValue = '', $tableName = '')
  {
    $obj = $tableName !== '' ? new fsDBTable($tableName) : $this->_table;
    if ($obj == null) {
      $this->Message(T('XMLcms_text_no_dbtable'));
    } else { 
      if ($valueToCheckResult === '') {
        $valueToCheckResult = $valueToCheck; 
      }
      $result = $obj->IsUnique($valueToCheck, $fieldDb, $findedFieldValue);
      if ($result !== true && ($result === false || ('' !== $valueToCheckResult && $valueToCheckResult != $result))) {
          $this->Message(T('XMLcms_unique_data_error'));
      } 
    }
    if ($this->Message() != '') {
      $this->_Referer();
      return false;
    }
    return true;
  }
  
  /**
  * Get or set response tags values  
  * @api
  * @since 1.0.0
  * @param string $name Tag name. 
  * @param mixed $value (optional) New tag value. Create tag with $name if it not exists. Default <b>null</b>. 
  * @return mixed Tag value.      
  */  
  final protected function Tag($name, $value = null)
  {
    if ($value !== null) {
      $this->_response->tag->$name = $value;
    }
    return $this->_response->tag->$name;
  } 
  
  /**
  * Get or set response redirect value  
  * @api
  * @since 1.0.0
  * @param mixed $url (optional) New url for redirect. Default <b>null</b>.  
  * @return string Url for redirect.      
  */
  final public function Redirect($url = null)
  {
    if (is_string($url)) {
      $this->_response->redirect = $url;
    }
    return $this->_response->redirect;      
  }
  
  /**
  * Get or set response message  
  * @api
  * @since 1.0.0
  * @param mixed $message (optional) New response message. Default <b>null</b>.  
  * @return string Current response message.      
  */
  final public function Message($message = null)
  {
    if (is_string($message)) {
      $this->_response->message = $message;
    }
    return $this->_response->message; 
  }
  
  /**
  * Get or set value of response flag of empty result  
  * @api
  * @since 1.0.0
  * @param mixed $value (optional) New response empty result flag value. Default <b>null</b>.  
  * @return boolean Response empty result flag value.      
  */
  final public function EmptyResponse($value = null)
  {
    if ($value === true || $value === false) {
      $this->_response->empty = $value;
    }
    return $this->_response->empty;  
  }
  
  /**
  * Get or set response HTML code  
  * @api
  * @since 1.0.0
  * @param mixed $html (optional) Html code for adding to current response html string. Default <b>null</b>.  
  * @param boolean $clear (optional) If <b>true</b> response Html code will clear before adding new value. Default <b>false</b>.  
  * @return string Html code.      
  */
  final public function Html($html = null, $clear = false)
  {
    if (is_string($html)) {
      if ($clear === true) {
        $this->_response->html = '';  
      }
      $this->_response->html .= $html;  
    }
    return $this->_response->html;  
  }
  
  /**
  * Set response HTML as JSON string  
  * @api
  * @since 1.0.0
  * @param mixed $data Array of data or string.  
  * @return string JSON string.      
  */
  final public function Json($data)
  {
    $this->Html(json_encode(
      !fsFunctions::IsArrayAssoc($data) 
        ? array('data' => $data)
        : $data  
      ), true); 
    return $this->Html();  
  }
  
  /**
  * fsController constructor  
  * @api
  * @since 1.0.0
  * @return void      
  */
  public function __construct()
  {
    $controller = get_class($this);
    $this->_view = new View();
    $this->_response = new Response();
    $this->_link = URL_ROOT.$controller.'/';
    if (!empty($this->_tableName)) {
      $class = $this->_tableName;
      $this->_table = class_exists($class)
                      ? new $class(false, fsConfig::GetInstance('cache_use'), fsConfig::GetInstance('cache_table'))
                      : new fsDBTableExtension($class, fsConfig::GetInstance('cache_use'), fsConfig::GetInstance('cache_table')); 
    }
  }
  
  /**
  * Function which will execute before main method function  
  * @api
  * @since 1.0.0
  * @param fsStruct $request Global array $_REQUEST as fsStruct object.  
  * @return void      
  */
  public function Init($request)
  {
    $this->Tag('message', '');
    if ($request->Exists('tag') && $request->tag instanceof fsStruct) {
      $tagStruct = $request->tag->GetStruct(array('constants'));
      foreach ($tagStruct as $tag) {
        $this->Tag($tag, $request->tag->$tag);  
      }
      $request->Delete('tag'); 
    }
    $message = fsSession::GetInstance('Message');
    if ($message !== null && !empty($message)) {
      $this->Tag('message', "<div class='fs-controller-message' id='fs-controller-message'>".$message."</div>");
    }
  }
  
  /**
  * Function which will execute after main method function  
  * @api
  * @since 1.0.0
  * @return void      
  */
  public function Finnaly() {}   
  
  public function __get($attr)
  {
    if ($attr == 'response') {
      return $this->_response;
    }
    throw new Exception('Controller invalid field "'.$attr.'"');
  }
  
}