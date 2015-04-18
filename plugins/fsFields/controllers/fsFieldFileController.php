<?php
class fsFieldFileController extends AdminPanel
{
  private $_pathUpload, $_urlUpload;

  private function _JsonAnswerArray($data, $status = 0) 
  {
    return array('status' => $status, 'data' => $data);
  }

  public function Init($request)
  {
    $this->_pathUpload = PATH_PLUGINS.'fsFields/uploads/';
    $this->_urlUpload = '//'.$_SERVER['SERVER_NAME'].'/plugins/fsFields/uploads/';
    parent::Init($request);
  }

  public function actionUpload($param)
  {
    if($param->name == '') {
      return $this->Html("error:".T('XMLcms_text_need_all_data'));
    }
    
    fsFunctions::CreateDirectory($this->_pathUpload);
    $prefix = '';
    
    if($param->Exists('image')) {
      $prefix = 'images/';
      $this->_urlUpload  .= $prefix;
      $this->_pathUpload .= $prefix;        
      fsFunctions::CreateDirectory($this->_pathUpload);
      
      $error = fsFunctions::CheckUploadFiles($param->name,
                                             array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'),
                                             false,
                                             true,
                                             false);
      if($error) {
        return $this->Json($this->_JsonAnswerArray(T('XMLcms_text_bad_file_format'), 1));
      }
    }
    $newFile = '';
    if (fsFunctions::UploadFiles($param->name, $this->_pathUpload, $newFile)) {
      return $this->Json($this->_JsonAnswerArray($this->_urlUpload.$newFile));   
    }
    return $this->Json($this->_JsonAnswerArray(T('XMLcms_text_file_upload_error'), 2));
  }
}