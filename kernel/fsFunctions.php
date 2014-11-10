<?php
/**
* Kernel global functions class
* @package fsKernel
*/
class fsFunctions
{
  /**
    * Check that object exists and not empty.    
    * @since 1.0.0
    * @api    
    * @param mixed $obj Object for checking.
    * @return boolean Result of checking.  
    */
  public static function NotEmpty($obj)
  {
    return isset($obj) && !empty($obj);
  }
  
  /**
    * Send GET request 
    * @since 1.0.0
    * @api    
    * @param string $url Url for request.
    * @param array $data (optional) Request data. Default <b>null</b>.
    * @param array $options (optional) Additional CURL options. Default <b>empty array</b>.
    * @return string Server answer.  
    */
  public static function RequestGet($url, $data = null, $options = array()) 
  {
    $defaults = array(
        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($data),
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 4
    );
    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if(!($result = curl_exec($ch)))
    {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result; 
  }
  
  /**
    * Send POST request 
    * @since 1.0.0
    * @api    
    * @param string $url Url for request.
    * @param array $data (optional) Request data. Default <b>null</b>.
    * @param array $options (optional) Additional CURL options. Default <b>empty array</b>.
    * @return string Server answer.  
    */
  public static function RequestPost($url, $data = null, $options = array()) 
  {
    $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_POSTFIELDS => http_build_query($data)
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if(!($result = curl_exec($ch)))
    {
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;  
  }
  
  /**
    * Print array data
    * @since 1.0.0
    * @api    
    * @param array $data Data for print.
    * @return void.  
    */
  public static function FormatPrint($data)
  {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
  }
  
  /**
    * Get directory from full file path.
    * @since 1.0.0
    * @api    
    * @param string $filePath Full file path.
    * @return string Directory path.  
    */
  public static function GetDirectoryFromFullFilePath($filePath)
  {
    return substr($filePath, 0, strrpos($filePath, '/', -3) + 1);
  }
  
  /**
    * Calculate new date.
    * @since 1.0.0
    * @api    
    * @param string $stringOfChange Change commad (e.g. '+2 days').
    * @param date $startDate (optional) Base timestamp. Default <b>now</b>.
    * @return date Calculated date.  
    */
  public static function AddTime($stringOfChange, $startDate = null) 
  {
    if(null == $startDate || $startDate === false) {
      $startDate = date('Y-m-d H:i:s');
    }
    return date('Y-m-d H:i:s', strtotime($stringOfChange, strtotime($startDate)));
  }
  
  /**
    * Split string.
    * @since 1.0.0
    * @api    
    * @param string $delimiter Delimiter.
    * @param string $string String for split.
    * @param boolean $deleteEmpty (optional) Flag for skipping empty entries. Default <b>false</b>.
    * @param integer $limit (optional) Msximum result array length. If 0 - no limit. Default <b>0</b>.
    * @return array Splitting string.  
    */
  public static function Explode($delimiter, $string, $deleteEmpty = false, $limit = 0) 
  {
    $arr = is_numeric($limit) && $limit > 0 ? explode($delimiter, $string) : explode($delimiter, $string, $limit);
    if($deleteEmpty !== false) {
      $temp = array();
      foreach($arr as $a) {
        if($a === $deleteEmpty) {
          continue;
        }
        $temp[] = $a;
      }
      $arr = $temp;
    }
    return $arr;
  }
  
  /**
    * Create new directory if it not exists.
    * @since 1.0.0
    * @api    
    * @param string $path Directory path.
    * @param integer $access Permissions.
    * @return void  
    */
  public static function CreateDirectory($path, $access = 0777)
  {
    if (!is_dir($path)) {
      $parts = explode('/', $path);
      $fullPartPath = '';
      foreach ($parts as $part) {
          $fullPartPath .= $part.'/';
          if(!is_dir($fullPartPath)) {
              mkdir($fullPartPath, $access);
          }
      }
      return true;
    }
    return false;
  } 
  
  /**
    * Set redirect header and stop script.
    * @since 1.0.0
    * @api    
    * @param string $url Url for redirection.
    * @return void  
    */
  public static function Redirect($url)
  {
    Header('Location: '.$url);
    exit;
  }
  
  /**
    * Check last letter of string for needed symbol and add it if it not exists.
    * @since 1.0.0
    * @api    
    * @param string $string String for checking.
    * @param string $slash (optional) Symbol for check. Default <b>/</b>.
    * @return string  
    */
  public static function Slash($string, $slash = '/')
  {
    return substr($string, -strlen($slash)) != $slash ? $string.$slash : $string;
  }
  
  /**
    * Send email.
    * @since 1.0.0
    * @api   
    * @example File array: $file = array('path' => array('path 1', 'path 2'), 'name' => array('File 1', 'File 2')); 
    * @param string $to Email Destination email address.
    * @param string $subj Message title.
    * @param string $text Message HTML text.
    * @param string $from (optional) Sender email address. Default <b>empty string</b>.
    * @param array $file (optional) Files for attach. Default <b>empty array</b>.
    * @param string $contentType (optional) Message content type. Default <b>text/html</b>.
    * @param string $charset (optional) Message codepage. Default <b>utf-8</b>.
    * @return boolean Result of mail sending.  
    */
  public static function Mail($to, $subj, $text, $from = '', $file = array(), $contentType = 'text/html', $charset = 'utf-8')
  {
    $zag = $text;
    $un = strtoupper(uniqid(time()));
    if (isset($file['path']) && is_array($file['path']) && count($file['path']) > 0) {
      if (!isset($file['name']) || !is_array($file['name']) || count($file['name']) != count($file['path'])) {
        $file['name'] = array();
        foreach ($file['path'] as $path) {
          $file['name'][] = basename($path);
        } 
      } 
      $zag = "------------".$un."\r\nContent-Type:".$contentType.";\r\n";
      $zag .= "Content-Transfer-Encoding: base64\r\n\r\n".base64_encode($text)."\r\n\r\n";
      foreach($file['path'] as $idx => $path) {
        $f   = fopen($path, 'rb');
        $zag .= "------------".$un."\r\n";
        $zag .= "Content-Type: application/octet-stream;";
        $zag .= "name=\"".basename($path)."\"\r\n";
        $zag .= "Content-Transfer-Encoding:base64\r\n";
        $zag .= "Content-Disposition:attachment;";
        $zag .= "filename=\"".$file['name'][$idx]."\"\r\n\r\n";
        $zag .= chunk_split(base64_encode(fread($f, filesize($path))))."\r\n";
        fclose($f);
      }
    }
    $head = "";
    if (!empty($from)) {
      $head .= "From: $from\r\n";
      $head .= "Reply-To: $from\r\n";
    }
    $head .= "X-Mailer: PHPMail Tool\r\n";
    $head .= "Mime-Version: 1.0\r\n";
    $head .= "Content-Type:";
    if ($zag == $text) {
      $head .= $contentType."; charset=\"".$charset."\";";
    } else {
      $head .= "multipart/mixed;";
    }
    $head .= "boundary=\"----------".$un."\"\r\n\r\n";
    return mail($to, $subj, $zag, $head);
  }
  
  
  /**
    * Get array of RGB(A) from HEX color string.
    * @since 1.0.0
    * @api   
    * @param string $hexColor HEX color.
    * @param boolean $allowAlpha (optional) Flag for alpha color. Default <b>false</b>.    
    * @return array RGB(A) array.  
    */
  public static function RgbFromHex($hexColor, $allowAlpha = false) 
  {
    $result = array(0, 0, 0);
    if($allowAlpha) {
        $result[] = 255;
    }
    if(($allowAlpha && strlen($hexColor) != 8) || (!$allowAlpha && strlen($hexColor) != 6)) {
        return $result;
    } 
    $result[0] = hexdec(substr($hexColor, 0, 2)); 
    $result[1] = hexdec(substr($hexColor, 2, 2)); 
    $result[2] = hexdec(substr($hexColor, 4, 2));
    if($allowAlpha) {
        $result[0] = hexdec(substr($hexColor, 6, 2)); 
    }
    return $result;
  }
  
  /**
    * Get files in directory.
    * @since 1.0.0
    * @api    
    * @param string $path Directory for search.
    * @param string $searchFile (optional) Flag for files search. Default <b>true</b>.
    * @param string $searchDir (optional) Flag for folders search. Default <b>true</b>.
    * @param string $prefix (optional) File formats for search. Default <b>empty array</b>.
    * @param array $fileFormats (optional) Prefix mask for search. Default <b>empty string</b>.
    * @param boolean $topDirectory (optional) Flag for search only in top directory without subdirectories. Default <b>true</b>.
    * @return array Associative array with files and folders names. 
    */
  public static function DirectoryInfo($path, $searchFile = true, $searchDir = true, $prefix = '', $fileFormats = array(), $topDirectory = true)
  {
    $arr = array('LENGTH' => 0, 'NAMES' => array());
    if ($searchDir === false && $searchFile === false) {
      return $arr;
    }
    if (is_dir($path)) {
      $path = self::Slash($path);
      $dh = opendir($path);
      $fileFormatsCount = count($fileFormats);
      while(false !== ($dir = readdir($dh))) {
        if ($dir == '.' || $dir == '..') {
          continue;
        }
        $fileName = $path.$dir;
        if ($searchFile && is_file($fileName)) {
          if(!empty($prefix)) {
            if(!is_array($prefix)) {
              $prefix = array($prefix);
            }
            $next = false;
            foreach($prefix as $p) {
              if(strpos($dir, $p) !== 0) {
                $next = true;
                break;
              }
            }
            if($next) {
              continue;
            } 
          }
          $ext = pathinfo($fileName, PATHINFO_EXTENSION);
          if ($fileFormatsCount > 0 
             && (in_array('!'.$ext, $fileFormats) || !in_array($ext, $fileFormats))) {
            continue;
          }
        } 
        if ($searchDir && is_dir($fileName)) {
          if(!empty($prefix) && strpos($dir, $prefix) !== 0) {
            continue;
          }
        }
        if (($searchDir && is_dir($fileName)) || ($searchFile && is_file($fileName))) { 
          $arr['NAMES'][] = $dir;
          ++$arr['LENGTH'];
        }
        if(!$topDirectory && is_dir($fileName)) {
            $fileName = self::Slash($fileName);
            $temp = self::DirectoryInfo($fileName, $searchFile, $searchDir, $prefix, $fileFormats, $topDirectory);
            for($i = 0; $i < $temp['LENGTH']; ++$i) {
                $arr['NAMES'][] = $dir.'/'.$temp['NAMES'][$i];
                ++$arr['LENGTH'];    
            }
        }
     }
     closedir($dh); 
    }
    return $arr;
  }
  
  /**
    * Get user ip address.
    * @since 1.0.0
    * @api    
    * @return string User ip address
    */
  public static function GetIp()
  {
    $ip = '0.0.0.0';
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else { 
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;   
  } 
  
  /**
    * Include file.
    * @since 1.0.0
    * @api    
    * @param string $file Path to file to be include.
    * @param array $data (optional) Associative array of variables for including into file. Default <b>empty array</b>.
    * @return boolean Result of file include.
    */ 
  public static function IncludeFile($file, $data = array())
  {
    if (file_exists($file)) {
      foreach ($data as $name => $value) {
        $$name = $value;
      }
      include_once $file;
      return true;
    } 
    return false;    
  }
  
  /**
    * Include file.
    * @since 1.0.0
    * @api    
    * @param array $files Path to files to be include.
    * @param array $data (optional) Associative array of variables for including into file. Default <b>empty array</b>. 
    * @return void
    */  
    public static function IncludeFiles($files, $data = array())
    {
        foreach ($files as $file) {
          self::IncludeFile($file, $data);
        }    
    }
  
  /**
    * Include folder.
    * @since 1.0.0
    * @api    
    * @param string $path Path to folder to be include.
    * @param string $prefix (optional) Prefix for file search. Default <b>empty string</b>.
    * @param array $format (optional) File format for search. Default <b>empty array</b>.
    * @param array $notInclude (optional) Files for exclude. Default <b>empty array</b>.
    * @return boolean Result of action.
    */ 
    public static function IncludeFolder($path, $prefix = '', $format = array('php'), $notInclude = array())
    {
      $path = self::Slash($path);
      $arr = self::DirectoryInfo($path, true, false, $prefix, $format);
      if ($arr['LENGTH'] == 0) {
        return false;
      }
      for ($i = 0; $i < $arr['LENGTH']; ++$i) {
        if (!in_array($arr['NAMES'][$i], $notInclude)) {
          self::IncludeFile($path.$arr['NAMES'][$i]);
        }
      }
      return true;
    }
  
    /**
    * Check is array associative.
    * @since 1.0.0
    * @api    
    * @param array $array Array for checking.
    * @return boolean result of checking.
    */
    public static function IsArrayAssoc($array) 
    {                                     
      return is_array($array) && count($array) > 0 && array_values($array) !== $array;
    }
  
    /**
    * Get current time.
    * @since 1.0.0
    * @api    
    * @return float Current time.
    */
    public static function GetMicrotime()
    {
      $mt = explode(' ', microtime());
      return (float)$mt[0] + (float)$mt[1];
    }
  
    /**
    * Restruct global array $_FILES.
    * @since 1.0.0
    * @api    
    * @return boolean Result of action.
    */
    public static function RestrucGlobalFILES()
    {
      if(!isset($_FILES)) {
        return false;
      }
      $fileArr = array();
      foreach ($_FILES as $field => $arr) {
        $fileArr[$field] = array();
        $fileCount = (is_array($arr['name']) ? count($arr['name']) : 1);
        $fileKeys = array_keys($arr);
        for ($i = 0; $i < $fileCount; ++$i) {
          foreach($fileKeys as $key) {
            $fileArr[$field][$i][$key] = 
                (is_array($arr['name']) ? $_FILES[$field][$key][$i] : $_FILES[$field][$key]);
          }
        }
      }
      $_FILES = $fileArr;
      return true;
    }
  
    /**
    * Delete file.
    * @since 1.0.0
    * @api 
    * @param string $file Path to file.    
    * @return boolean Result of action.
    */
    public static function DeleteFile($file)
    {
      if (file_exists($file)) {
        unlink($file);
        return true;
      }
      return false;
    }
  
    /**
    * Delete folder.
    * @since 1.0.0
    * @api 
    * @param string $path Path to folder.    
    * @return boolean Result of action.
    */
    public static function DeleteDirectory($path)
    {
      if (!is_dir($path)) {
        return false;
      }
      $path = self::Slash($path);
      $files = array_diff(scandir($path), array('.','..'));
      foreach ($files as $file) {
        $fn = $path.$file;
        if (is_dir($fn)) {
          self::DeleteDirectory($fn);
        } else {
          unlink($fn);
        }
      }
      return rmdir($path); 
    }
  
    /**
    * Convert russian string to chpu string.
    * @since 1.0.0
    * @api 
    * @param string $string Source string.    
    * @param string $space (optional) Symbol for space replace. Default <b>_</b>. 
    * @return string Result of action.
    */
    public static function Chpu($string, $space = '_')
    {
      $rus = array('а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
        'р', 'с', 'т', 'у', 'ф', 'х', 'ъ', 'ы', 'ь', 'э', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 
        'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ъ', 'Ы', 'Ь',
        'Э', ' ', 'ё','ж','ц','ч','ш','щ','ю','я','Ё','Ж','Ц','Ч','Ш','Щ','Ю','Я');
      $lat = array('a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
        'r', 's', 't', 'u', 'f', 'h', '_', 'i', '_', 'e', 'A', 'B', 'V', 'G', 'D', 'E', 'Z',
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', '_', 'I', '_',
        'E', $space, 'yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA');
      return str_replace($rus, $lat, $string);   
    }
  
    /**
    * Save array to php file.
    * @since 1.0.0
    * @api 
    * @param array $array Array for saving.    
    * @param string $name Name of file variable. 
    * @param string $file File path. 
    * @return boolean Result of action.
    */ 
    public static function ArrayToFile($array, $name, $file)
    {
      if (!is_array($array)) {
        return false;
      }
      $f = fopen($file, 'w');
      if (!$f) {
        return false;
      }
      fwrite($f, '<?php $'.$name.' = array(');
      foreach ($array as $k => $v) {
        fwrite($f, "'".$k."'=>'".$v."',");
      }
      fwrite($f, ');');
      fclose($f);
      return true;
    }

    /**
    * Get output of php file.
    * @since 1.0.0
    * @api 
    * @param string $file File for output getting. 
    * @param array $param Array of variables for include.    
    * @return string Result of file include.
    */ 
    public static function PhpOutput($file, $param = array())
    {
      if (!file_exists($file) || !is_file($file)) {
        return '';
      }
      if (!is_array($param)) {
        return '';
      }
      foreach ($param as $p => $v) {
        $$p = $v;
      }
      ob_start();
      include $file;
      return ob_get_clean();  
    }
  
    /**
    * Generate string with substitution.
    * @since 1.0.0
    * @api 
    * @example StringFormat('Hello, {0}! My name is {1}!', array('World', 'Andrey'))
    * @param string $string Source string. 
    * @param array $arrArgs Array of variables for substitute.    
    * @return string Result of substitution.
    */ 
    public static function StringFormat($string, $arrArgs = array()) 
    {
      foreach ($arrArgs as $idx => $value) {
        $string = str_replace('{'.$idx.'}', $value, $string);        
      }
      return $string;
    }

    /**
    * Upload files to server.
    * @since 1.0.0
    * @api 
    * @param string $name Name in $_FILES array. 
    * @param string $path Path for upload.    
    * @param string|array $uploadfile (out) Path of upload files.    
    * @param boolean $changeName (optional) Flag for change file name. Default <b>true</b>.    
    * @return boolean Result of action. 
    */ 
    public static function UploadFiles($name, $path, &$uploadfile, $changeName = true)
    {
      if (!isset($_FILES) || empty($_FILES[$name][0]['tmp_name'])) {
        return true;
      }
      $fc = count($_FILES[$name]);
      $uploadfile = array(); 
      $return = true;
      for ($i = 0; $i < $fc && $return; ++$i) {
        $uploadfile[$i] = self::Chpu($_FILES[$name][$i]['name']);
        if ($changeName) {
          $ext = pathinfo(basename($_FILES[$name][$i]['name']), PATHINFO_EXTENSION);
          $uploadfile[$i] = md5(rand().basename($_FILES[$name][$i]['name'])).'.'.$ext;
          while (file_exists($path.$uploadfile)) {
            $uploadfile[$i] = md5(rand().basename($_FILES[$name][$i]['name'])).'.'.$ext;
          }
        }
        $return = move_uploaded_file($_FILES[$name][$i]['tmp_name'], $path.$uploadfile[$i]);
      }
      if (count($uploadfile) == 1) {
        $uploadfile = $uploadfile[0];
      }
      return $return;
    }
  
    /**
    * Check files before uploading.
    * @since 1.0.0
    * @api 
    * @param string $name Name in $_FILES array. 
    * @param array $mimes (optional) Valid mimes formats. Default <b>empty array</b>.    
    * @param array $blacklistFormats (optional) Prohibited file formats. If empty generate default formats. Default <b>empty array</b>.    
    * @param array $useFileMem (optional) Flag for using $_FILES mime. Default <b>false</b>.    
    * @param boolean $canBeEmpty (optional) Flag possible lack of data. Default <b>true</b>.    
    * @return boolean True if errors was found. 
    */ 
    public static function CheckUploadFiles($name, $mimes = array(), $blacklistFormats = array(), $useFileMem = false, $canBeEmpty = true)
    {
      if (!isset($_FILES) || empty($_FILES[$name][0]['tmp_name'])) {
        if($canBeEmpty) {
          return false;
        } else { 
          return true;
        }
      }
      $error = false;
      $fc = count($_FILES[$name]);
      if (is_array($mimes) && count($mimes) > 0) {
        for($i = 0; $i < $fc; ++$i) {
          $error = true;
          $imageinfo = @getimagesize($_FILES[$name][$i]['tmp_name']);
          foreach ($mimes as $mime) {
            if (($mime == $imageinfo['mime']) || ($useFileMem && $_FILES[$name][$i]['type'] == $mime)) {
              $error = false;
              break;
            }
          }
          if ($error) {
            break;
          }
        }
      }
      if (!$error) {
        if (!is_array($blacklistFormats) || count($blacklistFormats) == 0) { 
          $blacklistFormats = array('css', 'js', 'html', 'php', 'phtml', 'php3', 'php4', 'exe');
        } 
        for($i = 0; $i < $fc; ++$i) {
          foreach ($blacklistFormats as $item) {
            if(preg_match("/\.$item\$/i", $_FILES[$name][$i]['name'])) {
              $error = true;
              break 2;
            }
          }
        }
      }
      return $error;
    }

    /**
    * Get image object
    * @since 1.0.0
    * @api 
    * @param string $filePath Image path. 
    * @return object Image object. If any error return null.
    */
    public static function LoadImage($filePath)
    {
      $type = exif_imagetype($filePath);
      $allowedTypes = array(1, 2, 3, 6);
      if (!in_array($type, $allowedTypes)) {
          return false;
      }
      switch ($type) {
          case 1: return imageCreateFromGif($filePath);
          case 2: return imageCreateFromJpeg($filePath);
          case 3: return imageCreateFromPng($filePath);
          case 6: return imageCreateFromBmp($filePath);
          default: return null;
      }   
    } 

    /**
    * Resize image
    * @since 1.0.0
    * @api 
    * @param string $fileSrc Source file path. 
    * @param string $size Width of new file.    
    * @param string $fileDesc New file path. 
    * @return boolean Result of action.
    */ 
    public static function ResizeImage($fileSrc, $size, $fileDesc)
    {
      $gis = GetImageSize($fileSrc);
      $type = $gis[2];
      switch($type) {
        case '1':
          $imorig = imagecreatefromgif($fileSrc);
          break;

        case '2':
          $imorig = imagecreatefromjpeg($fileSrc);
          break;

        case '3':
          $imorig = imagecreatefrompng($fileSrc);
          break;

        default:
          $imorig = imagecreatefromjpeg($fileSrc);
      }
      $x = imageSX($imorig);
      $y = imageSY($imorig);
      if($gis[0] <= $size) {
        $av = $x;
        $ah = $y;
      } else {
        $yc = $y * 1.3333333;
        $d = $x > $yc ? $x : $yc;
        $c = $d > $size ? $size / $d : $size;
        $av = $x * $c;       
        $ah = $y * $c;       
      }   
      $im = imagecreate($av, $ah);
      $im = imagecreatetruecolor($av, $ah);
      if (imagecopyresampled($im, $imorig, 0, 0, 0, 0, $av, $ah, $x, $y)) { 
        if (imagejpeg($im, $fileDesc)) {
          return true;
        }
      }
      return false; 
    }
  
    private static function _Gps2Num($coordPart)
    {
      $parts = explode('/', $coordPart);
      if (count($parts) <= 0) {
          return 0;
      }
      if (count($parts) == 1) {
          return $parts[0];
      }
      return floatval($parts[0]) / floatval($parts[1]);
    }
    private static function _GetGps($exifCoord, $hemi) {

      $degrees = count($exifCoord) > 0 ? self::_Gps2Num($exifCoord[0]) : 0;
      $minutes = count($exifCoord) > 1 ? self::_Gps2Num($exifCoord[1]) : 0;
      $seconds = count($exifCoord) > 2 ? self::_Gps2Num($exifCoord[2]) : 0;
      $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
      return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
    }
    /**
    * Get GPS info from image file.
    * @since 1.0.0
    * @api 
    * @param string $file File path. 
    * @return array File information.
    */ 
    public static function GetExifGpsInfo($file)
    {
      $exif = exif_read_data($file);
      $result = array('DateTime' => $exif['DateTimeOriginal'],
                      'Lat'      => self::_GetGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']),
                      'Lng'      => self::_GetGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']));
      return $result;
    } 
}