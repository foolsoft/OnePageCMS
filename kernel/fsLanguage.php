<?php    
class fsLanguage implements iSingleton
{
  protected static $obj = null;
	private function __construct(){ }
	private function __clone()    { } 
	private function __wakeup()   { }
  
  public static function GetInstance()
  {
    if (self::$obj == null) {
      $SYSTEM_LANGUAGE = fsConfig::GetInstance('system_language'); 
      if(empty($SYSTEM_LANGUAGE)) {
        return;
      }
      if (!fsSession::Exists('Language')) {
        fsSession::Create('Language', $SYSTEM_LANGUAGE);
      }
      if(!empty($_REQUEST['language']) && $_REQUEST['language'] != fsSession::GetInstance('Language')) {
        fsSession::Set('Language', $_REQUEST['language']);
        fsFunctions::DeleteFile(PATH_JS.'initFsCMS.js');
      }
      $DICTIONARY = array();
      $DT = fsFunctions::DirectoryInfo(PATH_LANG, false, true, false);
      $SYSTEM_DICTIONARY = array();
      $DCACHEFILE = PATH_CACHE.'_dictionary_'.$SYSTEM_LANGUAGE.'_'.fsSession::GetInstance('Language').'.php';
      $SDCACHEFILE = PATH_CACHE.'_dictionary_'.$SYSTEM_LANGUAGE.'.php';
      $XDCACHEFILE = PATH_CACHE.'_text_dictionary_'.fsSession::GetInstance('Language').'.xml';
      $isSDCACHEFILE = file_exists($SDCACHEFILE);
      $isDCACHEFILE = file_exists($DCACHEFILE);
      $isXDCACHEFILE = file_exists($XDCACHEFILE);
      if (!$isDCACHEFILE || !$isSDCACHEFILE || !$XDCACHEFILE) {
        $newXML = new DOMDocument('1.0', 'UTF-8');
        $el = $newXML->createElement("dictionary");
        $newXML->appendChild($el);
        if (!$isXDCACHEFILE) $newXML->save($XDCACHEFILE);
        unset($newXML);
        foreach ($DT['NAMES'] as $D) {
          $TD = explode('-', $D);
          if (count($TD) != 3 && $D != 'xml') {
            continue;
          }
          $LP = PATH_LANG.$D.'/';
          $SL = !$isSDCACHEFILE && $TD[2] == $SYSTEM_LANGUAGE;
          $L = !$isDCACHEFILE
               && $SYSTEM_LANGUAGE != fsSession::GetInstance('Language')
               && $TD[0] == $SYSTEM_LANGUAGE
               && $TD[2] == fsSession::GetInstance('Language');
          $XL = !$isXDCACHEFILE && $D == 'xml';
          if ($XL) { 
            $arr = fsFunctions::DirectoryInfo($LP, true, false, false, array('xml'));
            foreach ($arr['NAMES'] as $A) {
              $xml = simplexml_load_file($LP.$A);   
              $result = $xml->xpath('/dictionary/text');
              if (!$result) {
                continue;
              }
              foreach ($result as $res) {
                $res = (array)$res;
                if (!isset($res['@attributes']['name']) || !isset($res[fsSession::GetInstance('Language')])) {
                  continue;
                }
                $xml = simplexml_load_file($XDCACHEFILE);
                $xml->addChild('XML'.$res['@attributes']['name'], $res[fsSession::GetInstance('Language')]);
                $xml->asXML($XDCACHEFILE);
              }
              unset($xml);  
            } 
          }
          if ($SL || $L) {
            $arr = fsFunctions::DirectoryInfo($LP, true, false, false, array('php'));
            foreach ($arr['NAMES'] as $A) {
              include $LP.$A; 
              if ($SL) {
                $SYSTEM_DICTIONARY = array_merge((array)$SYSTEM_DICTIONARY, (array)$LANG);
              } else {
                $DICTIONARY = array_merge((array)$DICTIONARY, (array)$LANG);
              }  
            }
          }
        }
        if (!$isDCACHEFILE && count($DICTIONARY) > 0) {
          fsFunctions::ArrayToFile($DICTIONARY, 'DICTIONARY', $DCACHEFILE);
        }
        if (!$isSDCACHEFILE && count($SYSTEM_DICTIONARY) > 0) {
          fsFunctions::ArrayToFile($SYSTEM_DICTIONARY, 'SYSTEM_DICTIONARY', $SDCACHEFILE);
        }
        unset($DT);
        unset($SYSTEM_DICTIONARY);
        unset($DICTIONARY);
      }
      if(file_exists($SDCACHEFILE)) {
        include $SDCACHEFILE;
      }
      if(file_exists($DCACHEFILE)) {
        include $DCACHEFILE;
      }
      $GLOBALS['DICTIONARY'] = array_merge((array)$SYSTEM_DICTIONARY, (array)$DICTIONARY);        
      unset($SYSTEM_DICTIONARY);
      unset($DCACHEFILE);
      unset($SDCACHEFILE);
      unset($XDCACHEFILE);
      unset($isDCACHEFILE);
      unset($isSDCACHEFILE);
      unset($isXDCACHEFILE);
    }
  }  
}
?>