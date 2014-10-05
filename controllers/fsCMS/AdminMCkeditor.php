<?php
class AdminMCkeditor extends AdminPanel
{
  public function actionUploadImage($param) 
  {
    $path = PATH_PLUGINS.'ckeditor/upload/';
    fsFunctions::CreateDirectory($path);
    $error = '';
    $uploadfile = '';
    $http_path = '';
    if(fsFunctions::CheckUploadFiles('upload', array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), false, true, false)) {
      $error = 'Invalid file format';
    } else {
      if(!fsFunctions::UploadFiles('upload', $path, $uploadfile, true)) {
        $error = 'Some error occured please try again later.';
      } else {
        $http_path = URL_PLUGINS.'ckeditor/upload/'.$uploadfile;
      }
    }
    $this->Html("<script type=\"text/javascript\">
      window.parent.CKEDITOR.tools.callFunction(".$param->CKEditorFuncNum.",  \"".$http_path."\", \"".$error."\" );
    </script>");
    
  }
}