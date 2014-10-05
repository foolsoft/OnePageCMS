<?php
class post_category extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function GetByPostId($postId)
  {
    $result = array();
    $this->Select()->Where('`id_post` = "'.$postId.'"')->Execute('', false);
    while ($this->Next()) {
      $result[] = $this->result->id_category;
    }
    return $result;
  }
  
  public function Add($post, $categories)
  {
    if (!is_array($categories) || count($categories) == 0) {
      return false;
    }
    foreach ($categories as $category) {
      $this->id_post = $post;
      $this->id_category = $category;
      $this->Insert()->Execute();
    }
    return true;
  }
  
}