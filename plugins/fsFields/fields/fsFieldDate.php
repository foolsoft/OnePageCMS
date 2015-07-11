<?php
class fsFieldDate extends fsField
{
  protected $_title;
  protected $_name;
  
  public function Input($htmlFormName, $value = '', $htmlAttributes = array(), $possibleValues = array(), $arrayName = 'fields')
  {
    $htmlAttributes = $htmlAttributes + array('class' => 'datepicker');
    return parent::Input($htmlFormName, $value, $htmlAttributes, $possibleValues, $arrayName);
  }
  
  public function __construct()
  {
    parent::__construct('date', 'Date');
  }
}