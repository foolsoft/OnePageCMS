<?php
class AdminMComments extends AdminPanel 
{
  protected $_tableName = 'comments';

  public function Init($request)
  {
    $this->Tag('title', T('XMLcms_text_comments'));
    parent::Init($request);
  }
  
  public function actionIndex($param)
  {
    $active = $param->Exists('active', true) ? $param->active : null;
    $startPage = $param->Exists('page', true) && $param->page > 0 ? $param->page : 1;
    $onPage = $param->Exists('onpage', true) && $param->onpage > 0 ? $param->onpage : 50;  
    
    $comments = $this->_table->Get(false, $active, false, $onPage * ($startPage - 1), $onPage, false);
    $table = new fsDBTableExtension('comment_fields');
    $fields = $table->GetAll(true, false, array('title', 'name'));
    for($i = 0; $i < count($comments); ++$i) {
      $commentFields = $comments[$i]['additional'] != '' ? json_decode($comments[$i]['additional']) : array();
      $str = '';
      for($j = 0; $j < count($fields); ++$j) {
        $f = $fields[$j]['name'];
        if(isset($commentFields->$f)) {
          $str .= '<div class="comment-field">'.$fields[$j]['title'].': '.$commentFields->$f.'</div>'; 
        }  
      }
      $comments[$i]['additional'] = $str;
    }
    $this->Tag('comments', $comments);
    
    $count = $this->_table->GetCount(); 
    $this->Tag('pages', 
               Paginator::Get(
                fsHtml::Url($this->_link.'Index'),
                'page',
                $count,
                $onPage,
                $startPage
               )
    );
  }
  
  private function _CheckField($param)
  {
    $param->required = $param->Exists('required') ? 1 : 0;
    $param->name = fsFunctions::Chpu($param->name);
    if($param->name == '' || $param->title == ''
      || ($param->Exists('key') && $param->key == '')) {
      $this->_Referer();
      $this->Message(T('XMLcms_text_need_all_data'));
      return false;
    }
    if(!$param->Exists('key')) {
      $param->key = false;
    }
    if(!$this->_CheckUnique($param->name, 'name', $param->key, $param->key === false ? false : 'name', 'comment_fields')) {
      return false;
    }
    return true;
  }
  
  public function actionEdit($param)
  {
    $this->_table->current = $param->key;
    if(!$param->Exists('key', true) || $this->_table->result->id != $param->key) {
      $this->Html(T('XMLcms_text_page_not_found'));
      return;
    }
    
    $fields = array();
    $commentFields = $this->_table->result->additional != '' ? json_decode($this->_table->result->additional) : array();
    $table = new fsDBTableExtension('comment_fields');
    $fields = $table->GetAll(true, false, array('title', 'name'));
    for($i = 0; $i < count($fields); ++$i) {
      $f = $fields[$i]['name']; 
      $fields[$i]['value'] = isset($commentFields->$f)
        ? $commentFields->$f
        : '';
    }
    
    $this->Tag('comment', $this->_table->result);
    $this->Html('<form onsubmit="return CommentSave(this);" method="post" action="'.$this->_link.'DoEdit/key/'.$param->key.'/">'.    
      $this->CreateView(array('fields' => $fields), $this->_Template('Edit')).'</form>');
  }
  
  public function actionConfig($param)
  {
      $this->Tag('settings', $this->settings);
  }
  
  public function actionDoDelete($param)
  {
    if(parent::actionDoDelete($param) == 0) {
      $this->_table->Remove($param->key);
    }
  }
  
  public function actionDoEdit($param)
  {
    $this->_table->current = $param->key;
    if($param->key == '' || $param->key != $this->_table->result->id) {
      $this->Html(T('XMLcms_text_page_not_found'));
    } else {
      $param->text = strip_tags($param->text);  
      $param->active = $param->Exists('active') ? '1' : '0';
      $commentFields = array();
      $table = new fsDBTableExtension('comment_fields');
      $fields = $table->GetAll(true, false, array('title', 'name'));
      $additional = $param->additional;
      for($i = 0; $i < count($fields); ++$i) {
        $f = $fields[$i]['name'];
        $commentFields[$f] = isset($additional[$f]) ? $additional[$f] : '';
      }
      
      $param->additional = json_encode($commentFields);
      parent::actionDoEdit($param);
      $this->Redirect('');
      $this->Html($this->Message());
    }
  }
  
  public function actionFieldsAdd($param)
  {
  }
  
  public function actionDoFieldsAdd($param)
  {
    if(!$this->_CheckField($param)) {
      return;
    }
    $param->table = 'comment_fields';
    parent::actionDoAdd($param);
  }
  
  public function actionDoFieldsEdit($param)
  {
    if(!$this->_CheckField($param)) {
      return;
    } 
    $param->table = 'comment_fields';
    parent::actionDoEdit($param);
  }
  
  public function actionFieldsEdit($param)
  {
    $table = new fsDBTableExtension('comment_fields');
    $table->current = $param->key;
    if($param->key == '' || $table->result->name != $param->key) {
      $this->_Referer();
      return;
    }  
    $this->Tag('field', $table->result);
  }
  
  public function actionFields($param)
  {
    $table = new fsDBTableExtension('comment_fields');
    $this->Tag('fields', $table->GetAll(true, false, array('title', 'name')));
  }
}
?>