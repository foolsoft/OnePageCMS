<?php
class fsHtml
{
  private static function _HtmlAttributesToString($htmlAttributes = array()) 
  {
    $attributes = '';
    foreach($htmlAttributes as $attributeName => $attributeValue) {
      $attributes .= ' '.$attributeName.'="'.$attributeValue.'"';
    }
    return $attributes;
  }
  
  public static function Input($type, $name, $value = '', $htmlAttributes = array())
  {
    if(!isset($htmlAttributes['id'])) {
      $htmlAttributes['id'] = $name;
    }
    return fsFunctions::StringFormat('<input type="{0}"{1} value="{2}"{3}/>', array(
      $type,
      $name === false ? ' ' : ' name="'.$name.'"',
      $value,
      self::_HtmlAttributesToString($htmlAttributes)     
    ));
  }
  
  public static function Hidden($name, $value = '', $htmlAttributes = array())
  {
    return self::Input('hidden', $name, $value, $htmlAttributes);
  }
  public static function Password($name, $value = '', $htmlAttributes = array())
  {
    return self::Input('password', $name, $value, $htmlAttributes);
  }
  public static function Editor($name, $value = '', $htmlAttributes = array())
  {
    return self::Input('text', $name, $value, $htmlAttributes);
  }
  public static function Number($name, $value = '', $htmlAttributes = array())
  {
    return self::Input('number', $name, $value, $htmlAttributes);
  }
  public static function Checkbox($name, $value = '', $htmlAttributes = array())
  {
    return self::Input('checkbox', $name, $value, $htmlAttributes);
  }
  public static function Radio($name, $value = '', $htmlAttributes = array())
  {
    return self::Input('radio', $name, $value, $htmlAttributes);
  }
  public static function File($name, $value = '', $htmlAttributes = array())
  {
    return self::Input('file', $name, $value, $htmlAttributes);
  }
  public static function Button($value, $onclick = false, $htmlAttributes = array())
  {
    $htmlAttributes['onclick'] = $onclick;
    $name = false;
    if(isset($htmlAttributes['name'])) {
      $name = $htmlAttributes['name']; 
      unset($htmlAttributes['name']);
    }
    return self::Input('button', $name, $value, $htmlAttributes);
  }
  public static function Textarea($name, $value = '', $htmlAttributes = array())
  {
    if(!isset($htmlAttributes['id'])) {
      $htmlAttributes['id'] = $name;
    }
    return fsFunctions::StringFormat('<textarea name="{0}"{2}>{1}</textarea>', array(
      $name,
      $value,
      self::_HtmlAttributesToString($htmlAttributes)
    ));
  }
  
  public static function Select($name, $values = array(), $selectedValue = false, $htmlAttributes = array())
  {
    $options = '';
    if(!fsFunctions::IsArrayAssoc($values)) {
      $temp = array();
      foreach($values as $value) {
        $temp[$value] = $value;
      }  
      $values = $temp;
    }
    foreach($values as $value => $text) {
      $options .= '<option value="'.$value.'" '.($selectedValue == $value ? 'selected' : '').'>'.$text.'</option>';  
    }
    if(!isset($htmlAttributes['id'])) {
      $htmlAttributes['id'] = $name;
    }
    return fsFunctions::StringFormat('<select name="{0}"{2}>{1}</select>', array(
      $name,
      $options,
      self::_HtmlAttributesToString($htmlAttributes),
    ));
  }
  
  public static function Url($href, $addSuffix = true)
  {
    $hrefSuffix = fsConfig::GetInstance('links_suffix');
    if($hrefSuffix !== null && $addSuffix === true) {
      $params = strpos($href, '?');
      if($params === false) {
        $href .= $hrefSuffix;
      } else {
        $href = preg_replace('/\?/', $hrefSuffix.'?', $href, 1);
      }
    }
    return $href; 
  }
  
  public static function Link($href, $text, $title = false, $htmlAttributes = array())
  {
    $href = self::Url($href, !isset($htmlAttributes['suffix']) || $htmlAttributes['suffix'] === true);
    if(isset($htmlAttributes['suffix'])) {
      unset($htmlAttributes['suffix']);
    }
    return fsFunctions::StringFormat('<a href="{0}" title="{2}"{3}>{1}</a>', array(
      $href,
      $text,
      !$title ? $text : $title,
      self::_HtmlAttributesToString($htmlAttributes)     
    ));
  }
  
  public static function Label($text, $for = false, $htmlAttributes = array())
  {
     return fsFunctions::StringFormat('<label{1}{2}>{0}</label>', array(
      $text,
      $for !== false ? ' for="'.$for.'"' : '',
      self::_HtmlAttributesToString($htmlAttributes) 
     ));
  } 
}
?>