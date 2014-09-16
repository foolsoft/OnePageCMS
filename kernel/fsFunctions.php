<?php
/**
* Kernel global functions class
* @package fsKernel
*/
class fsFunctions
{
  private static $_slash = '/';
  
  public static function NotEmpty($obj)
  {
    return isset($obj) && !empty($obj);
  }
  
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
  
  //Форматированный вывод print_r($data)
  public static function FormatPrint($data)
  {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
  }
  
  //Возвращаем путь к директории файла $filePath 
  public static function GetDirectoryFromFullFilePath($filePath)
  {
    return substr($filePath, 0, strrpos($filePath, self::$_slash, -3) + 1);
  }
  
  //Вычисляем дату относительно $startDate
  public static function AddTime($stringOfChange, $startDate = false) 
  {
    if($startDate === false) {
      $startDate = date('Y-m-d H:i:s');
    }
    return date('Y-m-d H:i:s', strtotime($stringOfChange, strtotime($startDate)));
  }
  
  //Split с возможностью удалять пустые вхождения
  public static function Explode($delimiter, $string, $deleteEmpty = false, $limit = false) 
  {
    $arr = is_numeric($limit) ? explode($delimiter, $string) : explode($delimiter, $string, $limit);
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
  
  //Создание директории $path если таковая не существует с правами $access
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
  
  //Редирект на страницу $url
  public static function Redirect($url)
  {
    Header('Location: '.$url);
    exit;
  }
  
  //Проверяем наличие строки $slash на конце строки $string
  //если $slash в $string отсутсвует, приписываем ее
  public static function Slash($string, $slash = false)
  {
    if ($slash === false) {
      $slash = self::$_slash;
    }
    return substr($string, -strlen($slash)) != $slash ? $string.$slash : $string;
  }
  
  //Отпрака письма с атачем 
  //$file = array('path' => array('пути к файлу'), 'name' => array('имена файлов в письме'));
  public static function Mail($to, $subj, $text, $from = false, $file = array(), $contentType = 'text/html', $charset = 'utf-8')
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
    if ($from) {
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
  
  //Получаем содержимое папки
  //$path - путь
  //$searchFile - флаг поиска файлов 
  //$searchDir - флаг поиска подкаталогов
  //$prefix - префикс имен для поиска
  //$fileFormat - фильтр форматов файлов для поиска, если первый символ !,
  //то данный формат игнорируется
  public static function DirectoryInfo($path, $searchFile = true, $searchDir = true, $prefix = false, $fileFormats = array())
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
          if($prefix !== false) {
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
          if ($fileFormatsCount > 0 &&
             (in_array('!'.$ext, $fileFormats) || !in_array($ext, $fileFormats))) {
            continue;
          }
        } 
        if ($searchDir && is_dir($fileName)) {
          if($prefix !== false && strpos($dir, $prefix) !== 0) {
            continue;
          }
        }
        if (($searchDir && is_dir($fileName)) ||
            ($searchFile && is_file($fileName))) { 
          $arr['NAMES'][] = $dir;
          ++$arr['LENGTH'];
        }
     }
     closedir($dh); 
    }
    return $arr;
  }
  
  //Получаем IP адресс клиента
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
  
  //Подключение файла $file,
  //$data - ассоциативный массив переменных для файла file  
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
  
  //Подключение файла $file,
  //$data - ассоциативный массив переменных для файла file  
  public static function IncludeFiles($files, $data = array())
  {
      foreach ($files as $file) {
        self::IncludeFile($file, $data);
      }    
  }
  
  //Подключение файлов из директории $path      
  public static function IncludeFolder($path, $prefix = false, $format = array('php'), $notInclude = array())
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
  
  //Проверяем является ли массив ассоциативным
  public static function IsArrayAssoc($array) 
  {                                     
    return is_array($array) && count($array) > 0 && array_values($array) !== $array;
  }
  
  //Получем текущее время
  public static function GetMicrotime()
  {
    $mt = explode(' ', microtime());
    return (float)$mt[0] + (float)$mt[1];
  }
  
  //Меняем структуру массива $_FILES на более приятную
  public static function RestrucGlobalFILES()
  {
    if(!isset($_FILES)) {
      return false;
    }
    $fileArr = array();
    foreach ($_FILES as $field => $arr) {
      $fileArr[$field] = array();
      $file_count = (is_array($arr['name']) ? count($arr['name']) : 1);
      $file_keys = array_keys($arr);
      for ($i=0; $i < $file_count; ++$i) {
        foreach($file_keys as $key) {
          $fileArr[$field][$i][$key] = (is_array($arr['name'])
                                       ? $_FILES[$field][$key][$i]
                                       : $_FILES[$field][$key]);
        }
      }
    }
    $_FILES = $fileArr;
    return true;
  }
  
  //Удаляем файл $file
  public static function DeleteFile($file)
  {
    if (file_exists($file)) {
      unlink($file);
      return true;
    }
    return false;
  }
  
  //Удаляем папку $path со всем содержимым
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
  
  //ЧПУ для $string
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
  
  //Сохранение массива $arr с именем $name в файл $file 
  public static function ArrayToFile($arr, $name, $file)
  {
    if (!is_array($arr)) {
      return false;
    }
    $f = fopen($file, 'w');
    if (!$f) {
      return false;
    }
    fwrite($f, '<?php $'.$name.' = array(');
    foreach ($arr as $k => $v) {
      fwrite($f, "'".$k."'=>'".$v."',");
    }
    fwrite($f, '); ?'.'>');
    fclose($f);
    return true;
  }

  //Получаем результат работы файла $file  
  //$param переменные для файла
  public static function PhpOutput($file, $param = array())
  {
    if (!file_exists($file) || !is_file($file)) {
      return 'File '.$file.' not found!';
    }
    if (!is_array($param)) {
      return false;
    }
    foreach ($param as $p => $v) {
      $$p = $v;
    }
    ob_start();
    include $file;
    return ob_get_clean();  
  }
  
  //Заменят в строке $string входжения {0}, {1}... на значение соответсующих
  //индексов массива $arrArgs
  public static function StringFormat($string, $arrArgs = array()) 
  {
    foreach ($arrArgs as $idx => $value) {
      $string = str_replace('{'.$idx.'}', $value, $string);        
    }
    return $string;
  }

  //Загрузка файлов на сервер
  //$name - имя input 
  //$path - путь для загрузки
  //$uploadfile - имя или массив имен загруженных файлов
  //$changeName - флаг генерации уникального имени файла
  //возвращаем последний результат move_uploaded_file
  public static function UploadFiles($name, $path, &$uploadfile, $changeName = true)
  {
    if (!isset($_FILES) || empty($_FILES[$name][0]['tmp_name'])) {
      return true;
    }
    $fc = count($_FILES[$name]);
    $uploadfile = Array(); 
    $return = true;
    for ($i = 0; $i < $fc && $return; ++$i) {
      $uploadfile[$i] = self::Chpu($_FILES[$name][$i]['name']);
      if ($changeName) {
        $ext = pathinfo(basename($_FILES[$name][$i]['name']), PATHINFO_EXTENSION);
        $uploadfile[$i] = md5(rand().basename($_FILES[$name][$i]['name'])).".".$ext;
        while (file_exists($Path.$uploadfile)) {
          $uploadfile[$i] = md5(rand().basename($_FILES[$name][$i]['name'])).".".$ext;
        }
      }
      $return = move_uploaded_file($_FILES[$name][$i]['tmp_name'], $path.$uploadfile[$i]);
    }
    if (count($uploadfile) == 1) {
      $uploadfile = $uploadfile[0];
    }
    return $return;
  }
  
  //Проверка загружаемых файлов
  //$name - имя input 
  //$mimes - допустимые MIME типы
  //$blacklistFormats - запрещенные форматы файлов
  //$useFileMem - флаг генерации уникального имени файла
  //$canBeEmpty - флаг вохможности отсутсвия загружаемого файла
  //Возвразаем true - если проверка не была пройдена 
  public static function CheckUploadFiles($name, $mimes = false, $blacklistFormats = false, $useFileMem = false, $canBeEmpty = true)
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
    if (is_array($mimes)) {
      for($i = 0; $i < $fc; ++$i) {
        $error = true;
        $imageinfo = @getimagesize($_FILES[$name][$i]['tmp_name']);
        foreach ($mimes as $mime) {
          if (($mime == $imageinfo['mime']) ||
               ($useFileMem && $_FILES[$name][$i]['type'] == $mime)) {
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
      if (!is_array($blacklistFormats)) { 
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

  //Функция уменьшения изображения
  //$fileSrc - исходный файл
  //$size - новый размер файла
  //$fileDesc - путь и имя для нового файла
  public static function ResizeImage($fileSrc, $size, $fileDesc)
  {
    $dirDesc = self::Slash($dirDesc);
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
  
   //Получение GPS координат из Exif файла
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
  public static function GetExifGpsInfo($file)
  {
    $exif = exif_read_data($file);
    $result = array('DateTime' => $exif['DateTimeOriginal'],
                    'Lat'      => self::_GetGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']),
                    'Lng'      => self::_GetGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']));
    return $result;
  } 
}
?>