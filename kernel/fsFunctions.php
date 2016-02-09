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
    * Print stack trace information.    
    * @since 1.1.0
    * @api    
    * @param boolean $stopScript (optional) Flag for abort script execution. Default <b>false</b>.
    * @return void  
    */
  public static function Trace($stopScript = false)
  {
    self::FormatPrint(debug_backtrace());
    if($stopScript === true) {
        exit;
    }
  }
  
  /**
    * Create basic authorization.    
    * @since 1.1.0
    * @api    
    * @param string $username User name for log in.
    * @param string $password Password for log in.
    * @param string $message (optional) Message if operation was field.
    * @return void  
    */
  public static function BasicAuth($username, $password, $message = 'Access denied')
  {
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) 
        || ($_SERVER['PHP_AUTH_USER'] != $username) || ($_SERVER['PHP_AUTH_PW'] != $password)) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Basic realm="Access denied!"');
        exit($message);
    }
  }
  
  /**
    * Return russian string from number.    
    * @since 1.1.0
    * @api    
    * @param float $number Number for convert.
    * @return string Result of conversion.  
    */
    public static function StringFromNumber($number)
    { 
        if(!is_numeric($number)) {
          return $number;
        }
        $number = explode('.', str_replace(',', '.', $number));
        if(count($number) > 2) {
          return '';
        }
        $num = $number[0];
        $m = array(
            array('ноль'),
            array('-','один','два','три','четыре','пять','шесть','семь','восемь','девять'),
            array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать','пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать'),
            array('-','-','двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят','восемьдесят','девяносто'),
            array('-','сто','двести','триста','четыреста','пятьсот','шестьсот','семьсот','восемьсот','девятьсот'),
            array('-','одна','две')
        );
        $r = array(
            array('...ллион','','а','ов'), 
            array('тысяч','а','и',''),
            array('миллион','','а','ов'),
            array('миллиард','','а','ов'),
            array('триллион','','а','ов'),
            array('квадриллион','','а','ов'),
            array('квинтиллион','','а','ов')
            // ,array(... список можно продолжить
        );
        if($num==0) {
          return $m[0][0];
        }
        $o = array(); 
        foreach(array_reverse(str_split(str_pad($num, ceil(strlen($num) / 3) * 3, '0', STR_PAD_LEFT), 3)) as $k => $p) {
            $o[$k] = array();
            foreach($n = str_split($p) as $kk => $pp) {
                if(!$pp) {
                    continue;
                } 
                switch($kk) {
                    case 0:
                      $o[$k][] = $m[4][$pp];
                      break;

                    case 1:
                      if($pp==1) { 
                          $o[$k][] = $m[2][$n[2]];
                          break 2;
                      } else { 
                          $o[$k][] = $m[3][$pp];
                          break;
                      }

                    case 2:
                      if(($k==1)&&($pp<=2)) {
                          $o[$k][] = $m[5][$pp];
                      } else { 
                          $o[$k][] = $m[1][$pp];
                      }
                      break;
                }
                $p*=1;
                if(!$r[$k]) {
                  $r[$k] = reset($r);
                }
            }

            if($p && $k) {
                switch(true) {
                    case preg_match("/^[1]$|^\\d*[0,2-9][1]$/", $p):
                        $o[$k][] = $r[$k][0].$r[$k][1];
                        break;

                    case preg_match("/^[2-4]$|\\d*[0,2-9][2-4]$/",$p):
                        $o[$k][] = $r[$k][0].$r[$k][2];
                        break;

                    default:
                        $o[$k][] = $r[$k][0].$r[$k][3];
                        break;
                }
            }
            $o[$k] = implode(' ', $o[$k]);
        }
        return implode(' ', array_reverse($o)).(count($number) == 2 ? ' '.$number[1] : '');
    }
  
  /**
    * Start process of file downloading 
    * @since 1.0.0
    * @api    
    * @param string $file Fail path.
    * @return void.  
    */
  public static function StartDownload($file) 
  {
    if (file_exists($file)) {
      if (ob_get_level()) {
        ob_end_clean();
      }
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename=' . basename($file));
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($file));
      readfile($file);
    } else {
      echo 'File not found'; 
    }
    exit; 
  }
  
  /**
    * Start process of string downloading 
    * @since 1.0.0
    * @api    
    * @param string $string Text for downloading.
    * @param string $fileName (optional) File name for downloading. Default <b>download.txt</b>.
    * @return void.  
    */
  public static function StartDownloadString($string, $fileName = '') 
  {
    if (ob_get_level()) {
      ob_end_clean();
    }
    if($fileName === '') {
        $fileName = 'download.txt';
    }
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename='.$fileName);
    die($string);
  }
  
  /**
    * Send GET request 
    * @since 1.0.0
    * @api    
    * @param string $url Url for request.
    * @param array $data (optional) Request data. Default <b>empty array</b>.
    * @param array $options (optional) Additional CURL options. Default <b>empty array</b>.
    * @since 1.1.0
    * @param array $headers (optional) Response headers. Default <b>empty array</b>.
    * @return string Server answer.  
    */
  public static function RequestGet($url, $data = array(), $options = array(), &$headers = array()) 
  {
    $headers = array();
    $defaults = array(
        CURLOPT_URL => $url. (strpos($url, '?') === false && count($data) > 0 ? '?' : ''). http_build_query($data),
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 4
    );
    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if(!($result = curl_exec($ch))) {
        trigger_error(curl_error($ch));
    }
    $headers = curl_getinfo($ch);
    curl_close($ch);
    return $result; 
  }
  
  /**
    * Send POST request 
    * @since 1.0.0
    * @api    
    * @param string $url Url for request.
    * @param array $data (optional) Request data. Default <b>empty array</b>.
    * @param array $options (optional) Additional CURL options. Default <b>empty array</b>.
    * @since 1.1.0
    * @param array $headers (optional) Response headers. Default <b>empty array</b>.
    * @return string Server answer.  
    */
  public static function RequestPost($url, $data = array(), $options = array(), &$headers = array()) 
  {
    $headers = array();  
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
    if(!($result = curl_exec($ch))) {
        trigger_error(curl_error($ch));
    }
    $headers = curl_getinfo($ch);
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
  
  private static function _Lower()
  {
    return array(
        'q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m',
        'ё','й','ц','у','к','е','н','г','ш','щ','з','х','ъ','ф','ы','в','а','п','р','о','л','д','ж','э','я','ч','с','м','и','т','ь','б','ю'
    );  
  }
  
  private static function _Upper()
  {
    return array(
        'Q','W','E','R','T','Y','U','I','O','P','A','S','D','F','G','H','J','K','L','Z','X','C','V','B','N','M',
        'Ё','Й','Ц','У','К','Е','Н','Г','Ш','Щ','З','Х','Ъ','Ф','Ы','В','А','П','Р','О','Л','Д','Ж','Э','Я','Ч','С','М','И','Т','Ь','Б','Ю'
    );
  }
  
  /**
    * Convert string to uppercase.
    * @since 1.1.0
    * @api    
    * @param string $string String for conversion.
    * @return string Input string in uppercase.  
    */
  public static function ToUpper($string)
  {
    return str_replace(self::_Lower(), self::_Upper(), $string);   
  }
  
  /**
    * Convert string to lower case.
    * @since 1.1.0
    * @api    
    * @param string $string String for conversion.
    * @return string Input string in lowercase.  
    */
  public static function ToLower($string)
  {
    return str_replace(self::_Upper(), self::_Lower(), $string);   
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
    * @param string $returnFormat (optional) Format of returned value. Default <b>Y-m-d H:i:s</b>.
    * @return date Calculated date.  
    */
  public static function AddTime($stringOfChange, $startDate = null, $returnFormat = 'Y-m-d H:i:s') 
  {
    if(null == $startDate || $startDate === false) {
      $startDate = date('Y-m-d H:i:s');
    }
    return date($returnFormat, strtotime($stringOfChange, strtotime($startDate)));
  }
  
  /**
    * Split string.
    * @since 1.0.0
    * @api    
    * @param string $delimiter Delimiter.
    * @param string $string String for split.
    * @param boolean|string $deleteEmpty (optional) If false return all values. If string skip this value. Default <b>false</b>.
    * @param integer $limit (optional) Msximum result array length. If 0 - no limit. Default <b>0</b>.
    * @return array Splitting string.  
    */
  public static function Explode($delimiter, $string, $deleteEmpty = false, $limit = 0) 
  {
    $arr = is_numeric($limit) && $limit > 0 ? explode($delimiter, $string, $limit) : explode($delimiter, $string);
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
  public static function CreateDirectory($path, $access = 0755)
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
    * @since 1.1.0
    * @param integer $code (optional) Response code. Default <b>301</b>.  
    * @return void  
    */
  public static function Redirect($url, $code = 301)
  {
    header('HTTP/1.1 '.$code.' Moved');
    header('Location: '.$url);
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
    $head = '';
    if ($from != '') {
      $fromArray = explode('<', $from);
      if(count($fromArray) == 2) {
        $from = '=?UTF-8?B?'.base64_encode(trim($fromArray[0])).'?= <'.$fromArray[1];
      }
      $head .= "From: ".$from."\r\n";
      $head .= "Reply-To: ".$from."\r\n";
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
    return mail($to, '=?UTF-8?B?'.base64_encode($subj).'?=', $zag, $head);
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
    * Check is user use mobile device.
    * @since 1.1.0
    * @api    
    * @return boolean Result of checking. 
    */
  public static function IsMobile()
  {
    return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER['HTTP_USER_AGENT'])
        || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
  }
  
  /**
    * Load csv file into array.
    * @since 1.1.0
    * @api 
    * @param $filePath Path to CSV file. 
    * @param $delimiter (optional) Separator of columns in CSV file. Default <b>;</b>.   
    * @param $quotes (optional) Column wrapper in CSV file. Default <b>"</b>.   
    * @return array Assoc array of csv file. 
    */
  public static function CsvToArray($filePath, $delimiter = ';', $quotes = '"')
  {
    $arr = array();
    if (($handle = fopen($filePath, 'r')) === false) {
        return $arr;
    }
    $i = 0;
    while (($lineArray = fgetcsv($handle, 4000, $delimiter, $quotes)) !== false) {
        $linesCount = count($lineArray);
        for ($j = 0; $j < $linesCount; ++$j) {
            $arr[$i][$j] = $lineArray[$j];
        }
        $i++;
    }
    fclose($handle);
    return $arr;
  }
  
  /**
    * Get files in directory.
    * @since 1.0.0
    * @api    
    * @param string $path Directory for search.
    * @param string $searchFile (optional) Flag for files search. Default <b>true</b>.
    * @param string $searchDir (optional) Flag for folders search. Default <b>true</b>.
    * @param array $prefix (optional) File formats for search. Default <b>empty array</b>.
    * @param array $fileFormats (optional) Prefix mask for search. Default <b>empty string</b>.
    * @param boolean $topDirectory (optional) Flag for search only in top directory without subdirectories. Default <b>true</b>.
    * @return array Associative array with files and folders names. 
    */
    public static function DirectoryInfo($path, $searchFile = true, $searchDir = true, $prefix = array(), $fileFormats = array(), $topDirectory = true)
    {
        $arr = array('LENGTH' => 0, 'NAMES' => array());
        if (!is_dir($path) || ($searchDir === false && $searchFile === false)) {
          return $arr;
        }
        $path = self::Slash($path);
        $cache = PATH_CACHE.'di_'.md5(implode(',', $fileFormats).implode(',', $prefix).$path.$searchFile.$searchDir.$topDirectory).'.php';
        if(file_exists($cache)) {
            return self::IncludeFile($cache);
        }
        $dh = opendir($path);
        $fileFormatsCount = count($fileFormats);
        $checkPrefix = is_array($prefix) && count($prefix) > 0;
        while(false !== ($dir = readdir($dh))) {
          if ($dir == '.' || $dir == '..') {
            continue;
          }
          $fileName = $path.$dir;
          if($checkPrefix) {
            $next = true;
            foreach($prefix as $p) {
              if(($p[0] != '!' && strpos($dir, $p) === 0) 
                || ($p[0] == '!' && strpos($dir, substr($p, 1)) !== 0)) {
                $next = false;
                break;
              }
            }
            if($next) {
                continue;
            }
          }
          if ($searchFile && is_file($fileName)) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            if ($fileFormatsCount > 0 
               && (in_array('!'.$ext, $fileFormats) || !in_array($ext, $fileFormats))) {
              continue;
            }
            $arr['NAMES'][] = $dir;
            ++$arr['LENGTH'];
          } 

          if(is_dir($fileName)) {
            if($searchDir) {
                $arr['NAMES'][] = $dir;
                ++$arr['LENGTH'];
            }
            if(!$topDirectory) {
                $temp = self::DirectoryInfo($fileName, $searchFile, $searchDir, $prefix, $fileFormats, $topDirectory);
                $arr['LENGTH'] += $temp['LENGTH'];
                for($i = 0; $i < $temp['LENGTH']; ++$i) {
                    $arr['NAMES'][] = $dir.'/'.$temp['NAMES'][$i];
                }
            }
          }
       }
       closedir($dh); 
       self::ArrayToFile($arr, '', $cache, true); 
       return $arr;
    }
  
    /**
    * Check is current request over SSL.
    * @since 1.1.0
    * @api    
    * @return boolean Result of checking
    */
    public static function IsSSL()
    {
      return (isset($_SERVER['HTTPS']) && ('on' == strtolower($_SERVER['HTTPS']) || '1' == $_SERVER['HTTPS']))
          || (isset($_SERVER['SERVER_PORT']) && '443' == $_SERVER['SERVER_PORT']);
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
    $ip = explode(',', $ip);
    return $ip[0];
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
      return include $file;
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
    * @param array $prefix (optional) Prefix for file search. Default <b>empty array</b>.
    * @param array $format (optional) File format for search. Default <b>array('php')</b>.
    * @param array $notInclude (optional) Files for exclude. Default <b>empty array</b>.
    * @return boolean Result of action.
    */ 
    public static function IncludeFolder($path, $prefix = array(), $format = array('php'), $notInclude = array())
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
    * @since 1.1.0
    * @api 
    * @param array $array Array for saving.    
    * @param string $name Name of file variable. 
    * @param string $file File path. 
    * @param boolean $asReturn (optional) Flag for return statement in file. Default <b>false</b>.
    * @return boolean Result of action.
    */ 
    public static function ArrayToFile($array, $name, $file, $asReturn = false)
    {
      $f = fopen($file, 'w');
      if (!$f) {
        return false;
      }
      $asString = self::ArrayToString($array, $name).';';
      if($asReturn && $asString !== '') {
        if($name === '') {
            $asString = 'return '.$asString;
        } else {
            $asString .= 'return $'.$name.';';
        }
      }
      fwrite($f, '<?php '.$asString);
      fclose($f);
      return true;
    }

    /**
    * Convert array to PHP string.
    * @since 1.1.0
    * @api 
    * @param array $array Array for saving.    
    * @param string $name Name of file variable. If empty not create variable. 
    * @return string Array as PHP string.
    */ 
    public static function ArrayToString($array, $name = '')
    {
        $result = '';
        $name = trim($name);
        $nameIsEmpty = $name === '';
        if(is_array($array) && ($nameIsEmpty || preg_match('/^[a-zA-Z_]/', $name))) {
          $result = ($nameIsEmpty ? '' : '$'.$name.'=').'array(';
          if(self::IsArrayAssoc($array)) {
            foreach ($array as $k => $v) {
              if(is_array($v)) {
                  $result .= "'".str_replace("'", "\'",$k)."'=>".self::ArrayToString($v).",";
              } else {
                  $ps = is_numeric($v) ? '' : '\'';
                  $result .= "'".str_replace("'", "\'",$k)."'=>".$ps.str_replace("'", "\'", $v).$ps.",\n";
              }
            }
          } else {
            foreach ($array as $v) {
              if(is_array($v)) {
                  $result .= self::ArrayToString($v).',';
              } else {
                  $ps = is_numeric($v) ? '' : '\'';
                  $result .= $ps.str_replace("'", "\'", $v).$ps.',';
              }
            }
          }
          $result .= ")\n";
        }
        return $result; 
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
    * @param array $arrArgs (optional) Array of variables for substitute.    
    * @since 1.1.0
    * @param array $escape (optional) Array of char escape. Default <b>empty array</b>
    * @return string Result of substitution.
    */ 
    public static function StringFormat($string, $arrArgs = array(), $escape = array()) 
    {             
      $from = $to = array();
      foreach($escape as $fromEscape => $toEscape) {
        $from[] = $fromEscape;
        $to[] = $toEscape;
      }
      foreach ($arrArgs as $idx => $value) {
        $string = str_replace('{'.$idx.'}', str_replace($from, $to, $value), $string);        
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
          while (file_exists($path.$uploadfile[$i])) {
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
        return !$canBeEmpty;
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