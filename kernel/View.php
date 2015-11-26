<?php
/**
* View class
* @package fsKernel
*/
class View 
{
  /** @var integer Current recursion level for page generation */
  private $_recursionLevel = -1;
  /** @var integer Maximum count of function calls */
  private $_compileLoop   = 3;
  /** @var boolean Flag for skipping actions render  */
  public  $actionsCompile = true;
  /** @var boolean Flag for skipping blocks render */
  public  $blocksCompile  = true;
  
  /**
  * View constructor  
  * @api
  * @since 1.0.0
  * @return void      
  */
  public function __construct() { }
  
  /**
  * Render templates blocks 
  * @api
  * @since 1.0.0
  * @param string $tplPath Template path.  
  * @param string $html Template html code.  
  * @param array $params (optional) Template data.  
  * @return string Html code.      
  */
  protected function _TemplateCompile($tplPath, $html, $params = array()) 
  {
    $matches = array();
    $matchesParent = array();
    $matchesCount = preg_match_all("/\[block\-([0-9a-zA-Z_\-]+)\](.*)\[endblock\-\\1\]/s", $html, $matches);
    $matchesCountParent = preg_match_all("/\[parent:([0-9a-zA-Z_\-\.\/]+\\".EXT_TPL.")\]/s", $html, $matchesParent);
    if ($matchesCountParent == 1) {
      $this->blocksCompile = false;
      $html = $this->CreateView($tplPath.$matchesParent[1][0], $params, false, false);
    } 
    if($this->blocksCompile) { 
      for ($i = 0; $i < $matchesCount; ++$i) {
        $html = preg_replace("/\[block\-(".$matches[1][$i].")\](.*)\[endblock\-\\1\]/s", $matches[2][$i], $html);
        $html = str_replace('[block-'.$matches[1][$i].']', '', $html);
        $html = str_replace('[endblock-'.$matches[1][$i].']', '', $html);        
      }
      if($this->_recursionLevel == 0) {
        $html = preg_replace("/\[block\-([a-zA-Z0-9\-\_]+)\](.*)\[endblock\-\\1\]/s", "\\2", $html);
      }
    }
    if ($matchesCountParent != 1) {
      $this->blocksCompile = true;
    }
    return $html;                  
  }
  
  /**
  * Render dictionary function 
  * @api
  * @since 1.0.0
  * @param string $html Template html code.  
  * @return string Html code.      
  */
  public function LanguageCompile($html) 
  {
    $matches = array();
    $matchesCount = preg_match_all("/\{\s*T\(([^\}]+)\)\s*\}/", $html, $matches);
    for ($i = 0; $i < $matchesCount; ++$i) {
      $html = str_replace($matches[0][$i],  T($matches[1][$i]), $html);
    } 
    return $html;
  }
  
  /**
  * Render template 
  * @api
  * @since 1.0.0
  * @param string $template Template path.  
  * @param array $params (optional) Template data.  
  * @param boolean $show (optional) Flag for auto 'echo' of result. Default <b>false</b>.  
  * @param boolean $noHtmlCompile (optional) Flag for skipping html variables. Default <b>false</b>.  
  * @return string Html code.      
  */
  public function CreateView($template, $params = array(), $show = false, $noHtmlCompile = false) 
  {
    ++$this->_recursionLevel;
    $tpl_path = fsFunctions::GetDirectoryFromFullFilePath($template);
    $buffer = fsFunctions::PhpOutput($template, $params);
    $hash = '';
    $try = 0;
    $params['SYSTEM_LANG'] = fsConfig::GetInstance('system_language');
    $params['USER_LANG'] = fsSession::GetInstance('Language');
    do {
      $hash = md5($buffer);
      $buffer = $this->_TemplateCompile($tpl_path, $buffer, $params);
      if ($this->actionsCompile) {
        $buffer = $this->_ActionsCompile($buffer, $params);
      }
    } while ($this->_recursionLevel == 0 && ++$try < $this->_compileLoop && $hash != md5($buffer));
    if (!$noHtmlCompile) {
        $buffer = $this->LanguageCompile($buffer);
        $buffer = $this->HtmlCompile($buffer, array(
          'USER_LANG' => fsSession::GetInstance('Language'),  
          'SYSTEM_LANG' => fsConfig::GetInstance('system_language'),
          'URL_ROOT' => URL_ROOT,
          'URL_ROOT_CLEAR' => URL_ROOT_CLEAR,
          'URL_SUFFIX' => fsConfig::GetInstance('links_suffix'),
        ));
    }
    if ($show) {
      echo $buffer;
    }
    --$this->_recursionLevel;
    return $buffer;
  }
  
  /**
  * Render html variables 
  * @api
  * @since 1.0.0
  * @param string $html Template html code.  
  * @param array $params (optional) Template data.  
  * @return string Html code.      
  */
  public function HtmlCompile($html, $params = array())
  {
    if(!fsFunctions::IsArrayAssoc($params)) {
      return $html;
    }
    foreach($params as $tag => $value) {
      if($value instanceof fsStruct) {
        $html = $this->HtmlCompile($html, $value->ToArray());
      } 
      if(is_object($value)) {
        continue;
      }
      $html = str_replace('{'.$tag.'}', $value, $html);
      if($value === '') {
        $match = array();
        preg_match_all("^\{if-".$tag."\}.*\{endif-".$tag."\}^s", $html, $match);
        foreach ($match as $m) {
          if(count($m) > 0) {
            $html = str_replace($m[0], '', $html);  
          }
        }
        $html = str_replace('{ifnot-'.$tag.'}', '', $html);
        $html = str_replace('{endifnot-'.$tag.'}', '', $html);
      } else {
          $html = str_replace('{if-'.$tag.'}', '', $html);
          $html = str_replace('{endif-'.$tag.'}', '', $html);
          $match = array();
          preg_match_all("^\{ifnot-".$tag."\}.*\{endifnot-".$tag."\}^s", $html, $match);
          foreach($match as $m) {
            if(count($m) > 0) {
                $html = str_replace($m[0], '', $html);  
            }
          }
      } 
    }
    return $html;
  }
  
  /**
  * Render controller calls 
  * @api
  * @since 1.0.0
  * @param string $html Template html code.  
  * @param array $params (optional) Template data.  
  * @return string Html code.      
  */
  protected function _ActionsCompile($html, $params = array())
  {
    $matches = array();            
    $subMatch = "([a-zA-Z\_]+)(=([\{\}\s\-_a-zA-Z0-9|]*))?";
    $mainMatch = "^\{%\s+([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)(\s*\|\s*((".$subMatch."\s*?,?\s*?)*)?)?\s+%\}^";
    $matchesCount = preg_match_all($mainMatch, $html, $matches);
    while ($matchesCount > 0) {
      for ($i = 0; $i < $matchesCount; ++$i) {
        $actionResult = '';
        if (class_exists($matches[1][$i])) {
          $class = new $matches[1][$i]();
          if (!method_exists($class, $matches[2][$i])) {
            die('Call undefined method {% '.$matches[1][$i].'/'.$matches[2][$i].' %}');
          }
          $denied = false; 
          $deniedResult = T('XMLcms_denied');
          $paramMaches = array();
          $paramMachesCount = preg_match_all("^".$subMatch."^", $matches[4][$i], $paramMaches);
          $config = array('method' => $_REQUEST['method'], 'controller' => $_REQUEST['controller']);
          foreach ($params as $key => $value) {
            $config[$key] = array('Value' => $value);
          }
          for ($j = 0; $j < $paramMachesCount; ++$j) {
            $config[$paramMaches[1][$j]] = array('Value' => $paramMaches[3][$j]);              
          }
          $request = new fsStruct($config, true);
          if (method_exists($class, 'Init')) {
            call_user_func(array($class, 'Init'), $request);
          }
          if (method_exists($class, 'IsDenied')) {
            $denied = call_user_func(array($class, 'IsDenied'));
          }
          if ($denied && method_exists($class, 'OnDenied')) {
            $deniedResult = call_user_func(array($class, 'OnDenied'), $request);
          }
          $actionResult = $denied ? $deniedResult : call_user_func(array($class, $matches[2][$i]), $request);
          unset($request);
        }
        $html = str_replace($matches[0][$i],  $actionResult, $html);
      }
      $matchesCount = preg_match_all($mainMatch, $html, $matches);
    }
    return $html;
  }
}