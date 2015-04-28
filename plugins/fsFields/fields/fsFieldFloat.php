<?php
class fsFieldFloat extends fsField
{
  protected $_title;
  protected $_name;
  
  public function Input($htmlFormName, $value = '', $htmlAttributes = array(), $possibleValues = array())
  {
    $htmlAttributes['step'] = '0.01';
    return parent::Input($htmlFormName, $value, $htmlAttributes, $possibleValues);
  }
  
  public function __construct()
  {
    parent::__construct('numberfloat', 'Float');
  }
}