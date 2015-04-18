<?php
/**
 * Translate function
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

/**
 * Translate function with auto echo
 * @package fsKernel
 * @param string $data Text or XML constant name for translate.
 */
function _T($data) 
{
    T($data, true);
}