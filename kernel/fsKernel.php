<?php
//Функция перевода
//Функция перевода на язык пользователя
function T($data, $show = false)
{ 
  $xml = strtoupper(substr($data, 0, 3)) == 'XML';
  $xml_file = PATH_CACHE.'_text_dictionary_'.fsSession::GetInstance('Language').'.xml';
  $return = $data;
  if (!$xml && isset($GLOBALS['DICTIONARY'][$data])) {
    if (!empty($GLOBALS['DICTIONARY'][$data])) {
      $return = $GLOBALS['DICTIONARY'][$data]; 
    }
  } else if ($xml && file_exists($xml_file)) {
    if (!isset($GLOBALS['XDICTIONARY'])) {
      $GLOBALS['XDICTIONARY'] = simplexml_load_file($xml_file);
    }  
    $result = $GLOBALS['XDICTIONARY']->xpath('/dictionary/'.$data);
    if ($result) {
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
  }
  if ($show) {
    echo $return;
  }
  return $return; //ничего не нашлось, возвращаем то что передали  
}

function _T($data) 
{
    T($data, true);
}

class fsKernel extends fsController
{
  private $_workTime; //время загрузки страницы
  
  //Перевод строки
  public function actionTranslate($param)
  {
    if (!$param->Exists('text') || $param->text == '') {
      return $this->EmptyResponse(true);
    }
    $this->Html(T($param->text));
  }
  
  //Меняем язык пользователя
  public function actionLanguage($param)
  {
    if ($param->Exists('name')) {
      fsSession::Set('Language', $param->name);
    }
    fsFunctions::DeleteFile(PATH_JS.'initFsCMS.js');
    $this->_Referer();
  }
  
  //Обрабоатываем запрос
  public function DoMethod()
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
        !method_exists($class, 'Finnaly')
       ) { 
      $this->_Stop(fsConfig::GetInstance('url_404'));
    }
    $request = new fsStruct($_REQUEST, true);
    $class->Init($request);
    if(fsSession::Exists('Message')) {
      fsSession::Delete('Message');
    }
    if ($class->Redirect() != '') {
      fsSession::Create('Message', $class->response->message);
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
    if (empty($html) && !$class->response->empty) {
      $html = $class->CreateView();
    }
    $html = preg_replace("/<\s*\/\s*body\s*>/", $_REQUEST['includeBody'].'</body>', $html);
    $html = preg_replace("/<\s*\/\s*head\s*>/", $_REQUEST['includeHead'].'</head>', $html);
    $this->_workTime = fsFunctions::GetMicrotime() - $this->_workTime;
    echo $html; 
  }
  
  //Время работы скрипта
  public function WorkTime($show = true)
  {
    echo '<div style="padding:5px;position:fixed;bottom:0px;left:0px;background:rgba(0,0,0,0.8);color:#fff;">'.$this->_workTime.'</div>';
    return $this->_workTime;
  }
  
  public function __construct()
	{
    $this->_workTime = fsFunctions::GetMicrotime();
		parent::__construct();
  }
}




?>