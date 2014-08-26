<?php
class comments extends fsDBTableExtension
{
  public function __destruct()
  {
    parent::__destruct();
  }
  
  public function Add($group, $authorId, $authorName, $text, 
    $parent = 0, $additional = '', $active = '0', $ip = false)
  {
    if($ip === false) {
      $ip = fsFunctions::GetIp();
    }
    if(!is_numeric($parent)) {
      $parent = 0;
    }
    $main_parent = 0;
    if($parent > 0) {
      $this->Select()->Where('`id` = "'.$parent.'"')->Order(array('date'))->Limit(1)->Execute();
      if($this->result->id == '') {
        return -1;
      }
      $main_parent = $this->result->main_parent == '' || $this->result->main_parent == 0 ? $parent : $this->result->main_parent;
    }
    $this->additional = $additional;
    $this->author_id = $authorId;
    $this->author_name = strip_tags($authorName);
    $this->ip = $ip;
    $this->text = strip_tags($text);
    $this->group = strip_tags($group);
    $this->active = $active == '1' ? 1 : 0;
    $this->date = date('Y-m-d H:i:s');
    $this->parent = $parent;
    $this->main_parent = $main_parent;
    $this->Insert()->Execute();
    return  $this->insertedId; 
  }
  
  public function Remove($id)
  {
    $this->Delete()->Where('`id` = "'.$id.'" OR `main_parent` = "'.$id.'" OR `parent` = "'.$id.'"')->Execute();
  }
  
  public function Get($group = false, $active = '1', $asc = false, $limitFrom = false, $limitCount = false, $mainParent = 0)
  {
    $this->Select();
    $where = array();
    if($group !== false) {
      $where[] = array('group' => $group);
    }
    if($mainParent !== false) {
      $where[] = array('main_parent' => $mainParent);
    }
    if($active !== null) {
      $where[] = array('active' => $active);
    }
    if(count($where) > 0) {
      $this->Where($where);
    }
    $this->Order(array('main_parent', 'date'), array(!$asc, !$asc));
    if(is_numeric($limitCount) && $limitCount > 0) {
      $this->Limit($limitCount, is_numeric($limitFrom) && $limitFrom >= 0 ? $limitFrom : false);
    }
    $comments = $this->ExecuteToArray();
    $count = count($comments);
    $users = new users();
    $result = array();
    for($i = 0; $i < $count; ++$i) {
      $childs = $mainParent !== false ? $this->Get($group, $active, $asc, false, false, $comments[$i]['id']) : array();
      for($j = -1; $j < count($childs); ++$j) {
        $c = $j == -1 ? $comments[$i] : $childs[$j];
        $userName = $c['author_name'];
        $answerTo = '';
        if(is_numeric($c['author_id']) && $c['author_id'] > 0) {
          $users->current = $c['authorid'];
          if($users->result->id == $c['author_id']) {
            $userName = $users->result->login;    
          }
        }
        if($c['parent'] > 0) {
          $this->GetOne($c['parent'], false);
          if($this->result->id == $c['parent']) {
            if($this->result->author_id == 0) {
              $answerTo = $this->result->author_name;
            } else {
              $answerTo = $users->GetField('login', $this->result->author_id);
            }
          }   
        }
        $c['author'] = $userName;
        $c['answer'] = $answerTo; 
        $result[] = $c;
      }
    }
    return $result;
  }
}
?>