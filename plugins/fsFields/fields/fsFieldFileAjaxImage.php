<?php

class fsFieldFileAjaxImage extends fsFieldFileAjax 
{

    protected $_title;
    protected $_name;

    public function Input($htmlFormName, $value = '', $htmlAttributes = array(), $possibleValues = array(), $arrayName = 'fields') 
    {
        $sha1 = sha1($htmlFormName);
        $htmlAttributes['params'] = 'image=true';
        $htmlAttributes['js'] = '$("#div' . $sha1 . '").show();$("#img' . $sha1 . '").attr("src", file);';
        $htmlAttributes['jsRemove'] = "$('#div" . $sha1 . "').hide();$('#img" . $sha1 . "').attr('src', '');";
        $htmlAttributes['html'] = '<div id="div' . $sha1 . '" class="' . ($value == '' ? 'hidden' : '') . '">
            <img id="img' . $sha1 . '" src="' . $value . '" border="1" width="300px" />
            </div>';
        return parent::Input($htmlFormName, $value, $htmlAttributes, $possibleValues, $arrayName);
    }

    public function __construct() 
    {
        parent::__construct('fileajaximage', 'Image');
    }

}
