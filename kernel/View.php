<?php
class View 
{
  private $_compileLoop   = 3;
  public  $actionsCompile = true;
  public  $parentCompile  = true;
  
  public function __construct() { }
  
  protected function _TemplateCompile($tpl_path, $html, $params = array()) 
  {
    $tags = array();
    $matches = array();
    $matchesParent = array();
    $matchesCount = preg_match_all("/\[block\-([0-9a-zA-Z_]+)\](.*)\[endblock\-\\1\]/s", $html, $matches);
    $matchesCountParent = preg_match_all("/\[parent:([0-9a-zA-Z_\-\.\/]+\\".EXT_TPL.")\]/s", $html, $matchesParent);
    if ($matchesCountParent == 1) {
      $this->parentCompile = false;
      $html = $this->CreateView($tpl_path.$matchesParent[1][0], $params);
    }
    for ($i = 0; $i < $matchesCount; ++$i) {
      $html = preg_replace("/\[block\-(".$matches[1][$i].")\](.*)\[endblock\-\\1\]/s", $matches[2][$i], $html);
      $html = str_replace('[block-'.$matches[1][$i].']', '', $html);
      $html = str_replace('[endblock-'.$matches[1][$i].']', '', $html);        
    }
    $html = preg_replace("/\[block\-([a-zA-Z0-9\-\_]+)\](.*)\[endblock\-\\1\]/s", "\\2", $html);
    return $html; 
  }
  
  public function LanguageCompile($html) 
  {
    $matches = array();
    $matchesCount = preg_match_all("/\{\s*T\(([^\}]+)\)\s*\}/", $html, $matches);
    for ($i = 0; $i < $matchesCount; ++$i) {
      $html = str_replace($matches[0][$i],  T($matches[1][$i]), $html);
    } 
    return $html;
  }
   
  public function CreateView($template, $params = array(), $show = false, $adminMode = false) 
  {
    $tpl_path = fsFunctions::GetDirectoryFromFullFilePath($template);
    $buffer = fsFunctions::PhpOutput($template, $params);
    $hash = '';
    $try = 0;
    $params['SYSTEM_LANG'] = fsConfig::GetInstance('system_language');
    $params['USER_LANG'] = fsSession::GetInstance('Language');
    do {
      $hash = md5($buffer);
      try {
        if ($this->actionsCompile) {
          $buffer = $this->_ActionsCompile($buffer, $params);
        }
        if ($this->parentCompile) {
          $buffer = $this->_TemplateCompile($tpl_path, $buffer, $params);
        }
      } catch (Exception $e) { 
        throw new ViewException($e);
      }
    } while (++$try < $this->_compileLoop && $hash != md5($buffer));
    if (!$adminMode) {
        $buffer = $this->LanguageCompile($buffer);
        $buffer = $this->HtmlCompile($buffer, array(
          'USER_LANG' => fsSession::GetInstance('Language'),  
          'SYSTEM_LANG' => fsConfig::GetInstance('system_language'),
          'URL_ROOT' => URL_ROOT,
          'URL_SUFFIX' => fsConfig::GetInstance('links_suffix'),
        ));
    }
    if ($show) {
      echo $buffer;
    }
    return $buffer;
  }
  
  public function HtmlCompile($html, $params = array())
  {
    if(!is_array($params)) {
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
          $html = str_replace($m[0], '', $html);  
        }
        $html = str_replace('{ifnot-'.$tag.'}', '', $html);
        $html = str_replace('{endifnot-'.$tag.'}', '', $html);
      } else {
          $html = str_replace('{if-'.$tag.'}', '', $html);
          $html = str_replace('{endif-'.$tag.'}', '', $html);
          $match = array();
          preg_match_all("^\{ifnot-".$tag."\}.*\{endifnot-".$tag."\}^s", $html, $match);
          foreach($match as $m) {
            $html = str_replace($m[0], '', $html);  
          }
      } 
    }
    return $html;
  }
  
  protected function _ActionsCompile($html, $params = array())
  {
    $matches = array();            
    $subMatch = "([a-zA-Z\_]+)(=([\{\}\s\-_a-zA-Z0-9]*))?";
    $mainMatch = "^\{%\s+([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)(\s*\|\s*((".$subMatch."\s*?,?\s*?)*)?)?\s+%\}^";
    $matchesCount = preg_match_all($mainMatch, $html, $matches);
    while ($matchesCount > 0) {
      for ($i = 0; $i < $matchesCount; ++$i) {
        $actionResult = '';
        if (class_exists($matches[1][$i])) {
          $class = new $matches[1][$i]();
          if (method_exists($class, $matches[2][$i])) {
            $paramMaches = array();
            $paramMachesCount = preg_match_all("^".$subMatch."^", $matches[4][$i], $paramMaches);
            $config = array();
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
            $actionResult = call_user_func(array($class, $matches[2][$i]), $request);
            unset($request);
          }
        }
        $html = str_replace($matches[0][$i],  $actionResult, $html);
      }
      $matchesCount = preg_match_all($mainMatch, $html, $matches);
    }
    return $html;
  }
}

class ViewException extends Exception
{
}
?>