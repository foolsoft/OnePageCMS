<?php

class fsFieldRadioList extends fsField 
{

    protected $_title;
    protected $_name;

    public function Input($htmlFormName, $value = '', $htmlAttributes = array(), $possibleValues = array(), $arrayName = 'fields') 
    {
        $c = count($possibleValues);
        $html = '';
        if ($arrayName !== '') {
            $htmlFormName = $arrayName . '[' . $htmlFormName . ']';
        }
        for ($i = 0; $i < $c; ++$i) {
            if ($value == $possibleValues[$i]) {
                $htmlAttributes['checked'] = 'checked';
            } else {
                unset($htmlAttributes['checked']);
            }
            $html .= fsFunctions::Radio($htmlFormName, $possibleValues[$i], $htmlAttributes) . ' ' . T($possibleValues[$i]) . ' ';
        }
        return $html;
    }

    public function __construct() 
    {
        parent::__construct('radiolist', 'Radio list');
    }

}
