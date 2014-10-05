<?php
class MTemplate extends cmsController
{
  public function actionChange($param)
  {
    $this->Redirect(URL_ROOT);
    if (!$param->Exists('name') || !is_dir(PATH_TPL.'fsCMS/'.$param->name)) {
      return;
    }
    fsSession::Set('Template', 'fsCMS/'.$param->name);
  }
}