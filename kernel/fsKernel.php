<?php
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
        $languages = new languages();
        $languageId = $languages->GetActiveByName($param->name);
        if($languageId == '') {
            return $this->_Referer();
        }  
        $lang = fsSession::GetInstance('Language');
        fsSession::Set('Language', $param->name);
        fsSession::Set('LanguageId', $languageId);
        $referer = $this->_Referer(false);
        if(fsConfig::GetInstance('multi_language')) {
            $referer = str_replace('/'.$lang.'/', '/'.$param->name.'/', $referer);  
            if($referer == URL_ROOT_CLEAR.$lang) {
                $referer = str_replace('/'.$lang, '/'.$param->name, $referer);
            }
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
        $m = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
        $c = isset($_REQUEST['controller']) ? $_REQUEST['controller'] : '';
        if($c !== '' && !class_exists($c)) {
            $this->HttpNotFound();
        }
        if($m === '') {
            $m = 'Index';
            $_REQUEST['method'] = $m;
        }
        $m = 'action'.$m;
        $class = ($c === '' ? $this : new $c());
        if(!method_exists($class, $m) || !method_exists($class, 'Init') || !method_exists($class, 'Finnaly')) { 
            $this->HttpNotFound();
        }
        $request = new fsStruct($_REQUEST, true);
        $class->Init($request);
        if(fsSession::Exists('Message')) {
            fsSession::Delete('Message');
        }
        if($class->Redirect() != '') {
            fsSession::Create('Message', $class->Message());
            $this->_Stop($class->Redirect(), $class->ResponseCode());
        }
        call_user_func(array($class, $m), $request);
        unset($request);
        fsSession::Create('Message', $class->Message());
        if($class->Redirect() != '') {
            $this->_Stop($class->Redirect(), $class->ResponseCode());
        }
        $class->Finnaly();
        $html = $class->Html();
        if($html === '' && !$class->response->empty) {
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
        $lang = $param->Exists('language') ? $param->language : fsSession::GetInstance('Language');
        $this->Redirect(fsHtml::Url(URL_ROOT_CLEAR.$lang.'/'.fsConfig::GetInstance('start_page')));
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