<?php
abstract class fsField implements iFsField
{
  protected $_title;
  protected $_name;
  
  public function Input($htmlFormName, $value = '', $htmlAttributes = array(), $possibleValues = array(), $arrayName = 'fields')
  {
    $arrayNameSuffix = $arrayName === '' ? '' : ']';
    $arrayNamePrefix = $arrayName === '' ? '' : $arrayName.'[';
    if(isset($htmlAttributes['empty']) && is_array($possibleValues)) {
      if(!fsFunctions::IsArrayAssoc($possibleValues)) {
        $temp = array();
        foreach($possibleValues as $element) {
          $temp[$element] = $element;
        }
        $possibleValues = $temp;
        unset($temp);  
      } 
      $possibleValues = array_merge(array('' => $htmlAttributes['empty']), $possibleValues);
      unset($htmlAttributes['empty']);
    }
    switch($this->_name) {
      case 'file': case 'fileimage':      return fsHtml::File($htmlFormName, $value, $htmlAttributes);
      case 'checkbox':                    return fsHtml::Checkbox($arrayNamePrefix.$htmlFormName.$arrayNameSuffix, $value, $htmlAttributes);
      case 'radio':                       return fsHtml::Radio($arrayNamePrefix.$htmlFormName.$arrayNameSuffix, $value, $htmlAttributes);
      case 'selectlist': case 'select':   return fsHtml::Select($arrayNamePrefix.$htmlFormName.$arrayNameSuffix, $possibleValues, $value, $htmlAttributes);
      case 'password':                    return fsHtml::Password($arrayNamePrefix.$htmlFormName.$arrayNameSuffix, $value, $htmlAttributes);
      case 'textarea':                    return fsHtml::Textarea($arrayNamePrefix.$htmlFormName.$arrayNameSuffix, $value, $htmlAttributes);
      case 'numberfloat': case 'number':  return fsHtml::Number($arrayNamePrefix.$htmlFormName.$arrayNameSuffix, $value, $htmlAttributes);
      case 'input': default:              return fsHtml::Editor($arrayNamePrefix.$htmlFormName.$arrayNameSuffix, $value, $htmlAttributes);
    }
  }
  
  public function __get($name)
  {
    switch($name) {
      case 'name': return $this->_name;
      case 'title': return $this->_title;
      default: return null;
    }
  }
  
  public function __construct($name, $title)
  {
    $this->_name = $name; 
    $this->_title = T($title);
  }
}