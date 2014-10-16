<?php
class posts_category extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function Get($category)
  {
    return $this->Select()
                 ->Where('`alt` = "'.$category.'" OR CAST(`id` as CHAR) = "'.$category.'"')
                 ->Execute();
  }
  
  public function Add($name, $alt = '')
  {
    if (empty($alt)) {
      $alt = fsFunctions::Chpu($alt);
    }
    $this->name = $name;
    $this->alt = $alt;
    $this->Insert()->Execute();
    return $this->insertedId;
  }
}