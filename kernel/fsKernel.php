<?php
/* Translate function
 * @package fsKernel
 * @param string $data Text or XML constant name for translate.
 * @param boolean $show (optional) Flag for auto echo. Default <b>false</b>.
 */
function T($data, $show = false)
{ 
  $xml = strlen($data) > 3 && strtoupper(substr($data, 0, 3)) == 'XML';
  $return = $data;
  if (!$xml && isset($GLOBALS['DICTIONARY'][$data])) {
    if (!empty($GLOBALS['DICTIONARY'][$data])) {
      $return = $GLOBALS['DICTIONARY'][$data]; 
    }
  } else if ($xml) {
    if (!isset($GLOBALS['XDICTIONARY'])) {
      $GLOBALS['XDICTIONARY'] = fsCache::GetXml('_text_dictionary_'.fsSession::GetInstance('Language').'.xml');
    }  
    if($GLOBALS['XDICTIONARY'] == null) {
        return $return;
    }
    $result = $GLOBALS['XDICTIONARY']->xpath('/dictionary/'.$data);
    if (!$result) {
        return $return;
    }
    $result = $result[0];
    if (!$result && (string)$result) {
      $return = (string)$result;
    } else {
      $result = (array)$result;
      if ($result[0] != '') {
        $return = $result[0];
      }
    }
  }
  if ($show) {
    echo $return;
  }
  return $return; 
}

/* Translate function with auto echo
 * @package fsKernel
 * @param string $data Text or XML constant name for translate.
 */
function _T($data) 
{
    T($data, true);
}

/**
* Kernel class
* @package fsKernel
*/
class fsKernel extends fsController
{
    /** @var double Script work time */
    private $_workTime; 
  
    /**
    * Translate using exists dictionaries.   
    * @since 1.0.0
    * @api    
    * @param fsStruct $param Attribute 'text': Text or xml constant for translation. 
    * @return void.  
    */
    public function actionTranslate($param)
    {
      if (!$param->Exists('text') || $param->text == '') {
        return $this->EmptyResponse(true);
      }
      $this->Html(T($param->text));
    }
  
    /**
    * Change current user language.  
    * @since 1.0.0
    * @api    
    * @param fsStruct $param Attribute 'name': User language. 
    * @return void.  
    */
    public function actionLanguage($param)
    {
      $lang = fsSession::GetInstance('Language');
      if ($param->Exists('name')) {
        fsSession::Set('Language', $param->name);
      }
      fsFunctions::DeleteFile(PATH_JS.'initFsCMS.js');
      fsCache::Clear();
      $referer = $this->_Referer(false);
      if(fsConfig::GetInstance('multi_language')) {
        $referer = str_replace('/'.$lang.'/', '/'.$param->name.'/', $referer);  
      }
      $this->Redirect($referer);
    }
  
    /**
    * Execute user request. 
    * @since 1.0.0
    * @api    
    * @param boolean $showWorkTime (optional) Flag for attach a script work time into response. Default <b>false</b>. 
    * @return void.  
    */
    public function DoMethod($showWorkTime = false)
    {
            $m = isset($_REQUEST['method']) ? $_REQUEST['method'] : false;
            $c = fsFunctions::NotEmpty($_REQUEST['controller']) ? $_REQUEST['controller'] : false;
            if ($c !== false && !class_exists($c)) {
        $this->_Stop(fsConfig::GetInstance('url_404'));
      }
      if (empty($m)) {
        $m = 'Index';
        $_REQUEST['method'] = $m;
      }
      if ($m !== false) {
          $m = 'action'.$m;
      }
      $class = ($c === false ? $this : new $c());
      if ($m === false || 
          !method_exists($class, $m) ||
          !method_exists($class, 'Init') ||
          !method_exists($class, 'Finnaly')) { 
        $this->_Stop(fsConfig::GetInstance('url_404'));
      }
      $request = new fsStruct($_REQUEST, true);
      $class->Init($request);
      if(fsSession::Exists('Message')) {
        fsSession::Delete('Message');
      }
      if ($class->Redirect() != '') {
        fsSession::Create('Message', $class->Message());
        $this->_Stop($class->Redirect());
      }
      call_user_func(array($class, $m), $request);
      unset($request);
            fsSession::Create('Message', $class->Message());
      if ($class->Redirect() != '') {
        $this->_Stop($class->Redirect());
      }
      $class->Finnaly();
      $html = $class->Html();
      if ($html === '' && !$class->response->empty) {
        $html = $class->CreateView();
      }
      $html = preg_replace("/<\s*\/\s*body\s*>/", $_REQUEST['includeBody'].'</body>', $html);
      $html = preg_replace("/<\s*\/\s*head\s*>/", $_REQUEST['includeHead'].'</head>', $html);
      $this->_workTime = fsFunctions::GetMicrotime() - $this->_workTime;
      if($showWorkTime) {
        $html = preg_replace("/<\s*\/\s*body\s*>/", $this->_WorkTimeTemplate($this->_workTime).'</body>', $html);
      }
      echo $html; 
    }
  
    /**
    * Redirect to start page from config. 
    * @since 1.0.0
    * @api    
    * @param fsStruct $param User request. 
    * @return void.  
    */
    public function actionStartPage($param)
    {
        $this->Redirect(fsHtml::Url(URL_ROOT.fsConfig::GetInstance('start_page')));
    }
  
   /**
   * Html template of element with script work time. 
   * @since 1.0.0
   * @api    
   * @param double $workTime Script work time. 
   * @return string Html code.  
   */
   private function _WorkTimeTemplate($workTime)
   {
     return '<div style="padding:5px;position:fixed;bottom:0px;left:0px;background:rgba(0,0,0,0.8);color:#fff;">'.$workTime.'</div>';
   }
  
    /**
    * Get script work time. 
    * @since 1.0.0
    * @api    
    * @param boolean $show (optional) Flag for auto 'echo'. Default <b>true</b>.
    * @return double Script work time.  
    */
    public function WorkTime($show = true)
    {
      if($show) {
        echo $this->_WorkTimeTemplate($this->_workTime);
      }
      return $this->_workTime;
    }
  
    /**
    * Kernel constructor. 
    * @since 1.0.0
    * @api    
    * @return void  
    */
    public function __construct()
    {
        $this->_workTime = fsFunctions::GetMicrotime();
	parent::__construct();
    }
}