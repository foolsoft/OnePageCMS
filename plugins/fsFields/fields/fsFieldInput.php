<?php
class fsFieldInput extends fsField
{
  protected $_title;
  protected $_name;
  
  public function __construct()
  {
    parent::__construct('input', 'String');
  }
}