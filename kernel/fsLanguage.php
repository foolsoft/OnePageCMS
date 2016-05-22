<?php    
/**
* Language class
* @package fsKernel
*/
class fsLanguage implements iSingleton
{
    /** @var fsStruct Class instance */ 
    protected static $obj = null;
    private function __construct(){ }
    private function __clone()    { } 
    private function __wakeup()   { }
  
    /**
    * Generate language files.    
    * @since 1.0.0
    * @api    
    * @return void.  
    */
    public static function GetInstance()
    {
      if (self::$obj == null) {
        $SYSTEM_LANGUAGE = fsConfig::GetInstance('system_language'); 
        if($SYSTEM_LANGUAGE == '') {
            return;
        }
        $languages = new fsDBTableExtension('languages');
        if (!fsSession::Exists('Language')) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            switch ($lang) {
                default:
                    $lang = $SYSTEM_LANGUAGE;
                    break;
            }
            fsSession::Create('Language', $lang);
            fsSession::Create('LanguageId', $languages->GetField('id', $lang, 'name'));
        }
        if(isset($_REQUEST['language']) && $_REQUEST['language'] != '' && $_REQUEST['language'] != fsSession::GetInstance('Language')) {
            $lang = $languages->GetOne($_REQUEST['language'], true, 'name');
            if($lang != null && $lang['active'] == 1) {
                fsSession::Set('Language', $_REQUEST['language']);
                fsSession::Set('LanguageId', $lang['id']);
            }
        }
        unset($languages);
        $DICTIONARY = array();
        $DT = fsFunctions::DirectoryInfo(PATH_LANG, false, true);
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
          foreach ($DT['NAMES'] as $D) {
            $TD = explode('-', $D);
            $notXmlFile = $D != 'xml';
            if (count($TD) != 3 && $notXmlFile) {
              continue;
            }
            $LP = PATH_LANG.$D.'/'; 
            $SL = !$isSDCACHEFILE && ($notXmlFile && $TD[2] == $SYSTEM_LANGUAGE);
            $L = !$isDCACHEFILE
                 && $SYSTEM_LANGUAGE != fsSession::GetInstance('Language')
                 && $TD[0] == $SYSTEM_LANGUAGE
                 && $TD[2] == fsSession::GetInstance('Language');
            $XL = !$isXDCACHEFILE && $D == 'xml';
            if ($XL) { 
              $arr = fsFunctions::DirectoryInfo($LP, true, false, array(), array('xml'));
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
                  $tag = $newXML->createElement('XML'.$res['@attributes']['name']);
                  $tag->appendChild($newXML->createCDATASection(trim($res[fsSession::GetInstance('Language')])));
                  $el->appendChild($tag);
                }
              } 
            }
            if ($SL || $L) {
              $arr = fsFunctions::DirectoryInfo($LP, true, false, array(), array('php'));
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
          $newXML->appendChild($el);
          if (!$isXDCACHEFILE) {
            $newXML->save($XDCACHEFILE);
          }
          if (!$isDCACHEFILE && isset($DICTIONARY) && count($DICTIONARY) > 0) {
            fsFunctions::ArrayToFile($DICTIONARY, 'DICTIONARY', $DCACHEFILE);
          }
          if (!$isSDCACHEFILE && isset($SYSTEM_DICTIONARY) && count($SYSTEM_DICTIONARY) > 0) {
            fsFunctions::ArrayToFile($SYSTEM_DICTIONARY, 'SYSTEM_DICTIONARY', $SDCACHEFILE);
          }
          unset($DT, $SYSTEM_DICTIONARY, $DICTIONARY);
        }
        if(file_exists($SDCACHEFILE)) {
          include $SDCACHEFILE;
        }
        if(file_exists($DCACHEFILE)) {
          include $DCACHEFILE;
        }
        if(!isset($DICTIONARY)) {
            $DICTIONARY = array();
        }
        $GLOBALS['DICTIONARY'] = array_merge((array)$SYSTEM_DICTIONARY, (array)$DICTIONARY);        
        unset($SYSTEM_DICTIONARY, $DCACHEFILE, $SDCACHEFILE, $XDCACHEFILE, $isDCACHEFILE, $isSDCACHEFILE, $isXDCACHEFILE);
        self::$obj = true;
      }
    }  
}