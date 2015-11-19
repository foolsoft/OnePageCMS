<?php
/**
* Html helper
* @package fsKernel
*/
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
  
    /** 
    * Create input field.   
    * @since 1.0.0
    * @api    
    * @param string $type Type of input field.
    * @param string $name Name for input field.
    * @param string $value (optional) Value for input field. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Input($type, $name, $value = '', $htmlAttributes = array())
    {
      if(!isset($htmlAttributes['id'])) {
        $htmlAttributes['id'] = $name;
      }
      $prefix = $suffix = '';
      if(isset($htmlAttributes['prefix'])) {
        $prefix = '<label>'.$htmlAttributes['prefix'].'</label>';
        unset($htmlAttributes['prefix']);
      }
      if(isset($htmlAttributes['suffix'])) {
        $suffix = $htmlAttributes['suffix'];
        unset($htmlAttributes['suffix']);
      }
      return fsFunctions::StringFormat('{4}<input type="{0}"{1} value="{2}"{3}/>{5}', array(
        $type,
        $name === false ? ' ' : ' name="'.$name.'"',
        $value,
        self::_HtmlAttributesToString($htmlAttributes),
        $prefix,
        $suffix     
      ));
    }
    
    /** 
    * Create multi language input field.   
    * @since 1.1.0
    * @api    
    * @param string $type fsHtml type of input.
    * @param array $languages Language identifiers.
    * @param string $name Name for input field.
    * @param array $values (optional) Values for input fields. Default <b>empty array</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    private static function _InputMultiLanguage($type, $languages, $name, $values = array(), $htmlAttributes = array())
    {
        $langCount = count($languages);
        if(!method_exists(get_class(), $type) || $langCount == 0) {
            return '-';
        }
        $typeLowerCase = strtolower($type);
        $html = '<div class="multilang-wrapper wrapper-'.$typeLowerCase.'">';
        if(!isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] = '';
        }
        $htmlAttributes['class'] .= ' multilang-input';
        $alreadyHidden = false;
        $languagesNames = array();
        foreach($languages as $languageId => $languageInfo) {
            $languagesNames[] = $languageInfo['name'];
            $htmlAttributes['id'] = $name.'-'.$languageInfo['name'];
            $html .= '<div id="container-'.$htmlAttributes['id'].'" class="tab-'.$name.($alreadyHidden ? ' hidden' : '').'">'. 
                call_user_func(array('fsHtml', $type), $name.'['.$languageId.']', isset($values[$languageId]) ? $values[$languageId] : '', $htmlAttributes).
                '</div>';
            if(!$alreadyHidden) {
                $alreadyHidden = true;
            }
        } 
        if($langCount > 1 && $type != 'Hidden') {
            $html .= self::Select($name.'-language-selector', $languagesNames, '', 
                array(
                    'class' => 'multilang-selector multilang-selector-'.$name, 
                    'onchange' => "$('.tab-".$name."').hide();$('#container-".$name."-' + this.value).show();"
                )
            );
        }
        return $html.'</div>';
    }
    
    /** 
    * Create multi language text input field.   
    * @since 1.1.0
    * @api    
    * @param array $languages Language identifiers.
    * @param string $name Name for input field.
    * @param array $values (optional) Values for input fields. Default <b>empty array</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function EditorMultiLanguage($languages, $name, $values = array(), $htmlAttributes = array())
    {
        return self::_InputMultiLanguage('Editor', $languages, $name, $values, $htmlAttributes);
    }
    
    /** 
    * Create multi language hidden input field.   
    * @since 1.1.0
    * @api    
    * @param array $languages Language identifiers.
    * @param string $name Name for input field.
    * @param array $values (optional) Values for input fields. Default <b>empty array</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function HiddenMultiLanguage($languages, $name, $values = array(), $htmlAttributes = array())
    {
        return self::_InputMultiLanguage('Hidden', $languages, $name, $values, $htmlAttributes);
    }
    
    /** 
    * Create multi language textarea input field.   
    * @since 1.1.0
    * @api    
    * @param array $languages Language identifiers.
    * @param string $name Name for input field.
    * @param array $values (optional) Values for input fields. Default <b>empty array</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function TextareaMultiLanguage($languages, $name, $values = array(), $htmlAttributes = array())
    {
        return self::_InputMultiLanguage('Textarea', $languages, $name, $values, $htmlAttributes);
    }
    
    
    /** 
    * Create hidden input field.   
    * @since 1.0.0
    * @api    
    * @param string $name Name for input field.
    * @param string $value (optional) Value for input field. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Hidden($name, $value = '', $htmlAttributes = array())
    {
      return self::Input('hidden', $name, $value, $htmlAttributes);
    }
    
    /** 
    * Create password input field.   
    * @since 1.0.0
    * @api    
    * @param string $name Name for input field.
    * @param string $value (optional) Value for input field. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Password($name, $value = '', $htmlAttributes = array())
    {
      return self::Input('password', $name, $value, $htmlAttributes);
    }
    
    /** 
    * Create text input field.   
    * @since 1.0.0
    * @api    
    * @param string $name Name for input field.
    * @param string $value (optional) Value for input field. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Editor($name, $value = '', $htmlAttributes = array())
    {
      return self::Input('text', $name, $value, $htmlAttributes);
    }
    
    /** 
    * Create number input field.   
    * @since 1.0.0
    * @api    
    * @param string $name Name for input field.
    * @param integer $value (optional) Value for input field. Default <b>0</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Number($name, $value = 0, $htmlAttributes = array())
    {
      return self::Input('number', $name, is_numeric($value) ? $value : 0, $htmlAttributes);
    }
    
    /** 
    * Create checkbox input field.   
    * @since 1.0.0
    * @api    
    * @param string $name Name for input field.
    * @param string $value (optional) Value for input field. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Checkbox($name, $value = '', $htmlAttributes = array())
    {
      return self::Input('checkbox', $name, $value, $htmlAttributes);
    }
    
    /** 
    * Create radio input field.   
    * @since 1.0.0
    * @api    
    * @param string $name Name for input field.
    * @param string $value (optional) Value for input field. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Radio($name, $value = '', $htmlAttributes = array())
    {
      return self::Input('radio', $name, $value, $htmlAttributes);
    }
    
    /** 
    * Create file input field.   
    * @since 1.0.0
    * @api    
    * @param string $name Name for input field.
    * @param string $value (optional) Value for input field. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function File($name, $value = '', $htmlAttributes = array())
    {
      return self::Input('file', $name, $value, $htmlAttributes);
    }
    
    /** 
    * Create button input field.   
    * @since 1.0.0
    * @api    
    * @param string $value Button text.
    * @param string $onclick (optional) JavaScript for onclick event. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Button($value, $onclick = '', $htmlAttributes = array())
    {
      $htmlAttributes['onclick'] = $onclick;
      $name = false;
      if(isset($htmlAttributes['name'])) {
        $name = $htmlAttributes['name']; 
        unset($htmlAttributes['name']);
      }
      return self::Input('button', $name, $value, $htmlAttributes);
    }
    
    /** 
    * Create textarea input field.   
    * @since 1.0.0
    * @api    
    * @param string $name Name for input field.
    * @param string $value (optional) Value for input field. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Textarea($name, $value = '', $htmlAttributes = array())
    {
      if(!isset($htmlAttributes['id'])) {
        $htmlAttributes['id'] = $name;
      }
      $prefix = $suffix = '';
      if(isset($htmlAttributes['prefix'])) {
        $prefix = '<label>'.$htmlAttributes['prefix'].'</label>';
        unset($htmlAttributes['prefix']);
      }
      if(isset($htmlAttributes['suffix'])) {
        $suffix = $htmlAttributes['suffix'];
        unset($htmlAttributes['suffix']);
      }
      return fsFunctions::StringFormat('{3}<textarea name="{0}"{2}>{1}</textarea>{4}', array(
        $name,
        $value,
        self::_HtmlAttributesToString($htmlAttributes),
        $prefix,
        $suffix
      ));
    }

    /** 
    * Create select input field.   
    * @since 1.0.0
    * @api    
    * @param string $name Name for input field.
    * @param array $values (optional) Possible values. Default <b>empty array</b>.
    * @param string|boolean $selectedValue (optional) Selected value. Default <b>false</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Select($name, $values = array(), $selectedValue = false, $htmlAttributes = array())
    {
      $options = '';
      $asIs = isset($htmlAttributes['asis']);
      if(!fsFunctions::IsArrayAssoc($values) && !$asIs) {
        $temp = array();
        foreach($values as $value) {
          $temp[$value] = $value;
        }  
        $values = $temp;
      }
      if($asIs) {
        unset($htmlAttributes['asis']);
      }
      $matches = array();
      $patternGroupOpen = '/^\[group=(.+)\]$/i';
      $patternGroupClose = '/^\[\/group=(.+)\]$/i';
      foreach($values as $value => $text) {
        if(preg_match($patternGroupOpen, $value, $matches) || preg_match($patternGroupOpen, $text, $matches)) {
          $options .= '<optgroup label="'.T($matches[1]).'">';
          continue;  
        } else if(preg_match($patternGroupClose, $value, $matches) || preg_match($patternGroupClose, $text, $matches)) {
          $options .= '</optgroup>';
          continue;  
        } 
        $options .= '<option value="'.$value.'" '.($selectedValue == $value ? 'selected' : '').'>'.$text.'</option>';  
      }
      if(!isset($htmlAttributes['id'])) {
        $htmlAttributes['id'] = $name;
      }
      $prefix = $suffix = '';
      if(isset($htmlAttributes['prefix'])) {
        $prefix = '<label>'.$htmlAttributes['prefix'].'</label>';
        unset($htmlAttributes['prefix']);
      }
      if(isset($htmlAttributes['suffix'])) {
        $suffix = $htmlAttributes['suffix'];
        unset($htmlAttributes['suffix']);
      }
      return fsFunctions::StringFormat('{3}<select name="{0}"{2}>{1}</select>{4}', array(
        $name,
        $options,
        self::_HtmlAttributesToString($htmlAttributes),
        $prefix,
        $suffix
      ));
    }

    /** 
    * Generate link
    * @since 1.0.0
    * @api    
    * @param string $href Url address.
    * @param boolean $addSuffix (optional) Flag for suffix append. Default <b>true</b>.
    * @return string Url address.      
    */
    public static function Url($href, $addSuffix = true)
    {
      if(preg_match('/^\{.*\}$/', $href)) {
        return $href;
      }
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
    
    /** 
    * Generate link tag
    * @since 1.0.0
    * @api    
    * @param string $href Url address.
    * @param string $text Link text.
    * @param string $title (optional) Link title. If empty use text value. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Link($href, $text, $title = '', $htmlAttributes = array())
    {
      $href = self::Url($href, !isset($htmlAttributes['suffix']) || $htmlAttributes['suffix'] === true);
      if(isset($htmlAttributes['suffix'])) {
        unset($htmlAttributes['suffix']);
      }
      return fsFunctions::StringFormat('<a href="{0}" title="{2}"{3}>{1}</a>', array(
        $href,
        $text,
        $title === '' ? $text : $title,
        self::_HtmlAttributesToString($htmlAttributes)     
      ));
    }
    
    /** 
    * Generate label tag
    * @since 1.0.0
    * @api    
    * @param string $text Label text.
    * @param string $for (optional) Value for 'For' attribute. Default <b>empty string</b>.
    * @param array $htmlAttributes (optional) Array of tag attributes. Default <b>empty array</b>.
    * @return string Html code.      
    */
    public static function Label($text, $for = '', $htmlAttributes = array())
    {
       return fsFunctions::StringFormat('<label{1}{2}>{0}</label>', array(
        $text,
        $for === '' ? '' : ' for="'.$for.'"',
        self::_HtmlAttributesToString($htmlAttributes) 
       ));
    } 
}