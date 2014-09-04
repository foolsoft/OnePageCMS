<?php
class AdminMDictionary extends AdminPanel 
{
  private $_fileName = 'custom'; 
  private $_xml = '';
  private $_php = array();
  private $_languages = array();
  private $_phpGroups = array();
  
  public function Init($request)
  {
    $this->_xml = PATH_LANG.'xml/';
    fsFunctions::CreateDirectory($this->_xml);
    $this->Tag('title', T('XMLcms_dictionary'));
    $this->_xml .= $this->_fileName.'.xml';
    if(!file_exists($this->_xml)) {
      fsFileWorker::UpdateFile($this->xml, '<'.'?xml version="1.0" encoding="UTF-8"?'.'><dictionary></dictionary>');  
    }
    $folders = fsFunctions::DirectoryInfo(PATH_LANG, false, true);
    $arrForPhp = array();
    foreach($folders['NAMES'] as $f) {
      $split = explode('-', $f);
      if(count($split) == 3) {
        $file = PATH_LANG.$f.'/'.$this->_fileName.'.php';
        if(!file_exists($file)) {
          fsFileWorker::UpdateFile($file, '<'.'?php $LANG = array (); ?'.'>'); 
        }
        $arrForPhp[$f] = $file;
        array_push($this->_phpGroups, $f);
        if(!in_array($split[0], $this->_languages)) {
          array_push($this->_languages, $split[0]);
        }
        if(!in_array($split[2], $this->_languages)) {
          array_push($this->_languages, $split[2]);
        }
      }
    }
    $this->_php = $arrForPhp;
    parent::Init($request);
  }
  
  public function actionCreateLanguage($param)
  {
    $this->_Referer();
    if($param->from == '' || $param->to == '') {
      return $this->Message(T('XMLcms_text_need_all_data'));
    }
    $param->from = strtolower(substr(strip_tags($param->from), 0, 3));
    $param->to = strtolower(substr(strip_tags($param->to), 0, 3));    
    if($param->to != $param->from) {
      fsFunctions::CreateDirectory(PATH_LANG.$param->from.'-to-'.$param->to);
    } else {
      $this->Message(T('XMLcms_unique_data_error'));
    }
  }
  
  public function actionDeleteLanguage($param)
  {
    $this->_Referer();
    $lang = explode('-', $param->lang);
    if($param->lang == '' || count($lang) != 3) {
      return $this->Message(T('XMLcms_text_need_all_data'));
    }    
    fsFunctions::DeleteDirectory(PATH_LANG.$param->lang);
  }
  
  public function actionIndex($param)
  {
    $this->Tag('languages', $this->_languages);      
    $this->Tag('xml', $this->_xml);
    $this->Tag('php', $this->_phpGroups);
    $this->Tag('selected', $param->Exists('selected') ? $param->selected : false);
    $dictionaries = $this->_phpGroups;
    array_push($dictionaries, 'xml');
    $this->Tag('dictionaries', $dictionaries);  
  }
  
  public function actionSave($param)
  {
    $this->Redirect($this->_My('Index?selected='.$param->file));
    $translate = $param->translate;
    if($param->file == 'xml') {
      $newXML = new DOMDocument('1.0', 'UTF-8');
      $el = $newXML->createElement("dictionary");
      $newXML->appendChild($el);
      foreach($param->original as $idx => $original) {
        $original = trim($original);
        if($original === '') {
          continue;
        }
        $text = $newXML->createElement("text");
        $attr = $newXML->createAttribute('name');
        $attr->value = fsFunctions::Chpu($original);
        foreach($translate[$idx] as $lang => $value) {
          $tag = $newXML->createElement($lang, trim($value));
          $text->appendChild($tag);  
        }
        $text->appendChild($attr);
        $el->appendChild($text);
      }
      $newXML->save($this->_xml);
    } else {
      if(!isset($this->_php[$param->file])) {
        $this->Message(T('XMLcms_text_need_all_data'));
        return;
      }
      $array = array();
      foreach($param->original as $idx => $original) {
        $original = trim($original);
        if($original !== '') {
          $array[$original] = trim($translate[$idx]);
        }                 
      }
      fsFunctions::ArrayToFile($array, 'LANG', $this->_php[$param->file]);
    }
    fsFunctions::DeleteDirectory(PATH_CACHE);
  }
  
  public function actionRedactor($param)
  {
    $dataArray = array();
    if($param->lang == 'xml') {
      $xml = simplexml_load_file($this->_xml);   
      $result = $xml->xpath('/dictionary/text');
      if ($result) {
        $langsArray = array();
        foreach ($result as $res) {
          $res = (array)$res;
          if (!isset($res['@attributes']['name'])) {
            continue;
          }
          if(!isset($dataArray[$res['@attributes']['name']])) {
            $dataArray[$res['@attributes']['name']] = array();
          } 
          foreach($this->_languages as $lang) {
            $dataArray[$res['@attributes']['name']][$lang] = isset($res[$lang]) ? $res[$lang] : '';
            if(!isset($langsArray[$lang])) {
              $langsArray[$lang] = '';
            }
          }
        }
      } else {
        foreach($this->_languages as $lang) {
          $langsArray[$lang] = '';
        }
      }
      $dataArray[' '] = $langsArray; //Для добавления нового значения      
    } else {
      if(!isset($this->_php[$param->lang])) {
        $this->Html(T('XMLcms_text_need_all_data'));
        return;
      }
      include $this->_php[$param->lang];
      $dataArray = $LANG;
      $dataArray[' '] = '';  //Для добавления нового значения
    }
    $this->Html($this->_RedactorRow($dataArray, $param->lang));
    $this->Html('<script type="text/javascript">var redactorIndex = '.count($dataArray).';</script>');
    $this->Html(fsHtml::Hidden('file', $param->lang));
  }
  
  public function actionNewRow($param) 
  {
    $dataArray = array();
    if($param->lang != 'xml') {
      $dataArray[' '] = '';
    } else {
      $dataArray[' '] = array();
      foreach($this->_languages as $lang) {
        $dataArray[' '][$lang] = '';
      }
    }
    $this->Html($this->_RedactorRow($dataArray, $param->lang, $param->row));
  }
  
  private function _RedactorRow($dataArray, $language, $idx = 0)
  {
    if(!is_numeric($idx) || $idx < 0) {
      $idx = 0;
    }
    $html = '';
    $textAfterSave = T('XMLcms_after_save');
    $textDelete = T('XMLcms_delete');
    $textTranslate = T('XMLcms_translate');
    $from = ''; $to = ''; $textOriginal = ''; 
    if($language != 'xml') {
      $split = explode('-', $language);
      $from =  ' ('.$split[0].')';
      $to = ' ('.$split[2].')';
      $textOriginal = T('XMLcms_original');
    } else {
      $textOriginal = T('XMLcms_text_template'); 
    }
    
    foreach($dataArray as $key => $value) {
      $btnDelete = ' '.fsHtml::Button($textDelete, "if(confirm(T('cms_text_sure'))){\$('#row-".$idx."').html('".$textAfterSave."');}");
      $html .= '<div class="div-row" id="row-'.$idx.'">';
      $html .= $textOriginal.$from.': '.fsHtml::Editor('original['.$idx.']', trim($key), array('size' => '50')).' ';
      if(is_array($value)) { //XML
        $html .= '<span>';
        $htmlTemp = '';
        foreach($value as $lang => $text) {
          $html .= '<a href="javascript:DictionaryXmlTranslate(\''.$lang.'\', '.$idx.');" title="'.$lang.'">'.$lang.'</a> ';
          $htmlTemp .= '<div class="xml-translate hidden" id="xml-'.$idx.'-'.$lang.'">'.
            $textTranslate.' ('.$lang.'):<br />'.fsHtml::Textarea('translate['.$idx.']['.$lang.']', $text, array('class' => 'ckeditor')).
            '</div>';
        }
        $html .= '</span>'.$btnDelete.'<div>'.$htmlTemp.'</div>';
      } else { //PHP
        $html .= $textTranslate.$to.': '.fsHtml::Editor('translate['.$idx.']', $value, array('size' => '50'));
        $html .= $btnDelete;
      }
      $html .= '</div>';
      ++$idx;
    }
    return $html;
  }
}
?>