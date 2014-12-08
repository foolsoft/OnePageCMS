<?php
class AdminMModules extends AdminPanel
{
  protected $_tableName = 'admin_menu';
  private $_pathZip; 

  public function Init($request)
  {
    $this->Tag('title', T('XMLcms_text_modules'));
    $this->_pathZip = PATH_ROOT.'temp/';
    parent::Init($request);
  }

  public function actionDoAdd($param)
  {
    $error = fsFunctions::CheckUploadFiles('userfile',
                                           array('application/zip',
                                                 'application/octet-stream',
                                                 'application/x-zip-compressed'
                                                ),
                                           false,
                                           true,
                                           false
    );
    $this->Redirect($this->_My('Add'));
    if($error) {
      return $this->Message(T('XMLcms_text_bad_file_format'));
    }
    $newFile = '';
    if (fsFunctions::UploadFiles('userfile', $this->_pathZip, $newFile)) {
        $zip = new ZipArchive();
        $res = $zip->open($this->_pathZip.$newFile);
        if ($res === true) {
         $zip->extractTo(PATH_ROOT);
         $zip->close();
         if (file_exists(PATH_ROOT.'settings.php')) {
            include PATH_ROOT.'settings.php';
            if (isset($MNAME)  && !empty($MNAME)) {
              if (!isset($MADMIN_START)) {
                $MADMIN_START = '';
              }
              if (!isset($MTEXT)) {
                $MTEXT = $MNAME;
              }
              $this->_table->AddModule($MNAME, $MADMIN_START, $MTEXT);
              if (isset($SETTINGS) && is_array($SETTINGS)) {
                $controller_settings = new controller_settings();
                foreach ($SETTINGS as $name => $value) {
                  $controller_settings->Add($MNAME, $name, $value);
                }
                unset($controller_settings);
              }
              if (isset($MENU) && is_array($MENU)) {
                $controller_menu = new controller_menu();
                foreach ($MENU as $title => $href) {
                  $controller_menu->Add($MNAME, $title, $href);
                }
                unset($controller_menu);  
              } 
            }
            unlink(PATH_ROOT.'settings.php');
         }
         if (file_exists(PATH_ROOT.'install.php')) {
            $db = new fsDBconnection();
            include PATH_ROOT.'install.php';
            unlink(PATH_ROOT.'install.php');
         }
         $this->Redirect($this->_My());
         fsCache::Clear();
        } else {
          $this->Message(T('XMLcms_text_unzip_error'));
        }
    } else {
        $this->Message(T('XMLcms_text_file_upload_error'));
    }
  }

  public function actionAdd($param)
  {
  }

  public function actionIndex($param)
  {
    $this->Tag('modules', $this->_table->GetModules());
  }
  
  public function actionDelete($param)
  {
    if (parent::actionDelete($param) == 0) {
      $classNames = array();
      $classNames[] = $param->key;
      $isAdminController = substr(strtolower($classNames[0]), 0, 5) == 'admin';
      $classNames[] = $isAdminController ? substr($classNames[0], 5) : 'Admin'.$classNames[0];
      $controller_menu = new controller_menu();
      $controller_menu->DeleteByControllerName($classNames);
      unset($controller_menu);
      $controller_settings = new controller_settings();
      $classNames[0] = explode('/', $classNames[0]);
      $classNames[0] = $classNames[0][0];
      $classNames[1] = explode('/', $classNames[1]);
      $classNames[1] = $classNames[1][0];
      $controller_settings->DeleteByControllerName($classNames);
      unset($controller_settings);
      
      fsFunctions::DeleteFile(PATH_ROOT.'controllers/'.$classNames[0].'.php');
      fsFunctions::DeleteFile(PATH_ROOT.'controllers/'.$classNames[1].'.php');
      
      foreach ($classNames as $class) {
        if (class_exists($class) && method_exists($class, 'UnInstall')) {
          $c = new $class();
          $c->UnInstall();
          unset($c);  
        }
      }
    }
  }

}