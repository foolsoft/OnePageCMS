<?php
class MComments extends cmsController 
{
  protected $_tableName = 'comments';

  public function actionDelete($param)
  {
    if(!$param->Exists('id', true) || $param->id < 1) {
      return $this->Html(T('XMLcms_error_action'));
    }
    $this->_table->GetOne($param->id);
    if($this->_table->result->id != $param->id) {
      return $this->Html(T('XMLcms_error_action'));
    }
    if($this->_table->result->author_id != 0
      && ((AUTH && fsSession::GetArrInstance('AUTH', 'id') != $this->_table->result->author_id)
        || !AUTH)) {
      return $this->Html(T('XMLcms_error_action'));
    }
    if($this->_table->result->author_id == 0 && $this->_table->result->ip != fsFunctions::GetIp()) {
      return $this->Html(T('XMLcms_error_action'));
    }
    if(fsFunctions::AddTime('+'.$this->settings->edit_time.' seconds', $this->_table->result->date) < date('Y-m-d H:i:s')) {
      return $this->Html(T('XMLcms_error_action'));
    } 
    $this->_table->Remove($param->id);
    $this->Html(T('XMLcms_record_deleted'));
  }

  public function Form($param)
  {
    $isAnswer = $param->Exists('parent', true) && $param->parent > 0;
    if((!AUTH && $this->settings->allow_guests != '1') || (!$isAnswer && (!$param->Exists('group') || $param->group == ''))) {
      return '';
    }
    if(!$isAnswer) {
      $param->group = str_replace(array(' ', '--'), array('-', ''), strip_tags($param->group));
    } else {
      $this->_table->Select()->Where('`id` = "'.$param->parent.'"')->Execute();
      if($this->_table->result->id == '') {
        return '';
      }
      $param->group = $this->_table->result->group;
    }
    $template = 'Form';
    if($param->Exists('template')) {
      $template = $param->template;
    }
    $table = new fsDBTableExtension('comment_fields');
    $fields = $table->GetAll(true, false, array('title', 'name'));
    $this->_AddMyScriptsAndStyles(true, true, URL_THEME_JS, URL_THEME_CSS);
    $html = '<form id="comment-addform-'.$param->group.'" action="'.$this->_My('Add').'" method="post" onsubmit="return CommentsAdd(this);">
      <input type="hidden" name="group" value="'.$param->group.'" />'.
      ($isAnswer ? '<input type="hidden" name="parent" value="'.$param->parent.'" />' : '').
      $this->CreateView(array('fields' => $fields), $this->_Template($template)).'</form>';
    if($param->Exists('ajax')) {
      $this->Html($html);
    }
    return $html;
  }

  public function actionForm($param)
  {
    $param->ajax = true;
    if($this->Form($param) == '') {
      $this->Html(T('XMLcms_error_action'));
    }  
  }

  public function actionComments($param)
  {
    $param->ajax = true;
    $this->Comments($param);
  }

  public function Comments($param)
  {
    if(!$param->Exists('group') || $param->group == '') {
      return '';
    }
    $template = 'Comments';
    if($param->Exists('template')) {
      $template = $param->template;
    }
    $commentTemplate = 'Comment.php';
    if($param->Exists('comment_template')) {
      $commentTemplate = $param->comment_template;
    }
    $param->group = str_replace(' ', '-', strip_tags($param->group));
    
    $this->Tag('template', $commentTemplate);
    $this->Tag('group', $param->group);
    
    $limitFrom = false; $limitCount = false;
    if($this->settings->comments_on_page > 0) {
      if($param->Exists('comment_page', true) && $param->comment_page > 0) {
        $limitFrom = ($param->comment_page - 1) * $this->settings->comments_on_page;
      }
      $limitCount = $this->settings->comments_on_page;
      $count = $this->_table->GetCount('`main_parent` = "0"'); 
      $this->Tag('pages', 
                 Paginator::Get(
                  'javascript:CommentsPage("'.$param->group.'", {comment_page});',
                  '{comment_page}',
                  $count,
                  $this->settings->comments_on_page,
                  $param->comment_page
                 )
      );
    }
    $this->_AddMyScriptsAndStyles(true, true, URL_THEME_JS, URL_THEME_CSS);
    $this->Tag(
      'comments',
      $this->_table->Get($param->group, 1, $this->settings->sorting == 'ASC', $limitFrom, $limitCount)
    );
    $this->Tag('edit_time', $this->settings->edit_time);
    
    $html = $this->CreateView(array(), $this->_Template($template));
    if($param->Exists('ajax')) {
      $this->Html($html);
    }
    return $html;
  }

  public function actionAdd($param)
  {
    if(!$param->Exists('group') || $param->group == '') {
      return $this->Json(array('Status' => 1, 'Text' => T('XMLcms_error_action')));
    }
    
    $ip = fsFunctions::GetIp();
    $ips = fsFunctions::Explode("\n", $this->settings->block_ip, '');
    $users = fsFunctions::Explode("\n", $this->settings->block_users, '');
    
    if(!fsCaptcha::Check($param->captcha)) {
        return $this->Json(array('Status' => 9, 'Text' => T('XMLcms_captcha_wrong')));
    }
    
    if(AUTH) {
      $param->author_id = fsSession::GetArrInstance('AUTH', 'id');
      $param->author_name = fsSession::GetArrInstance('AUTH', 'login');
    } else {
      $param->author_id = 0;
    }
    if(in_array($ip, $ips) || (!AUTH && $this->settings->allow_guests == '0')
      || in_array($param->author_id, $users) || in_array($param->author_name, $users)) {
      return $this->Json(array('Status' => 2, 'Text' => T('XMLcms_error_access')));
    }
    
    if($this->settings->max_length != 0 && strlen($param->text) > $this->settings->max_length) {
      return $this->Json(array('Status' => 3, 'Text' => T('XMLcms_comments_tolong')));
    }
    
    if($this->settings->min_length > strlen($param->text)) {
      return $this->Json(array('Status' => 4, 'Text' => T('XMLcms_comments_toshort')));
    }
    
    if(!AUTH && '' == $param->author_name) {
      return $this->Json(array('Status' => 8, 'Text' => T('XMLcms_text_need_all_data')));
    }

    $commentFields = array();
    $additional = $param->additional;
    if(!is_array($additional)) {
      $additional = array();
    }
    $table = new fsDBTableExtension('comment_fields');
    $fields = $table->GetAll(true, false, array('title', 'name'));
    for($i = 0; $i < count($fields); ++$i) {
      $f = $fields[$i]['name'];
      $commentFields[$f] = isset($additional[$f]) ? strip_tags($additional[$f]) : '';
      if($fields[$i]['required'] == '1' && $commentFields[$f] === '') {
        return $this->Json(array('Status' => 5, 'Text' => T('XMLcms_text_need_all_data')));
      }  
    }
    
    if(0 < $this->_table->Add(
        $param->group, $param->author_id, $param->author_name, $param->text,
        $param->parent, $commentFields, AUTH ? 1 : 0, $ip)) {
      $this->Json(array('Status' => 0, 'Text' => T('XMLcms_added')));  
    } else {
      $this->Json(array('Status' => 6, 'Text' => T('XMLcms_error_action')));
    }
  }
}