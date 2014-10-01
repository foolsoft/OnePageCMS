<?php
/**
* Captcha class
* @package fsKernel
*/
class fsCaptcha
{
    /** @var array Default captcha image settings */
    private static $_settings = array(
        'width' => 100,
        'height' => 25,
        'alph' => 'qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPLKJHGFDSAZXCVBNM',
        'length' => 4,
        'linesCount' => 2,
        'textColor' => array(255, 255, 255),
        'lineColor' => array(150, 150, 150) 
    );

    /**
    * Check if user typed string is equals with generated string.    
    * @since 1.0.0
    * @api    
    * @param string $string User typed text.
    * @return boolean Сomparison result between $string and captcha code.  
    */
    public static function Check($string)
    {
        return $string == $_SESSION['fsCaptcha'];
    }

    /** 
    * Generate capcha image response for current user session.   
    * @since 1.0.0
    * @api    
    * @param array $settings (optional) Captcha image settings. Default <b>array()</b>.
    * @return image Captcha image data.      
    */
    public static function Create($settings = array())
    {
        header("Content-Type: image/jpeg");
        
        $w = isset($settings['width']) ? $settings['width'] : self::$_settings['width'];
        $h = isset($settings['height']) ? $settings['height'] : self::$_settings['height'];
        $l = isset($settings['length']) ? $settings['length'] : self::$_settings['length'];
        $a = isset($settings['alph']) ? $settings['alph'] : self::$_settings['alph']; 
        $ac = strlen($a);
        $tc = isset($settings['textColor']) ? $settings['textColor'] : self::$_settings['textColor'];
        $lc = isset($settings['lineColor']) ? $settings['lineColor'] : self::$_settings['lineColor'];
        $lsc = isset($settings['linesCount']) ? $settings['linesCount'] : self::$_settings['linesCount'];
        $img = imagecreatetruecolor($w, $h);
        $textColor = imagecolorallocate($img , $tc[0], $tc[1], $tc[2]);
        $lineColor = imagecolorallocate($img , $lc[0], $lc[1], $lc[2]);
        $_SESSION['fsCaptcha'] = '';
        for ($i = 0; $i < $l; ++$i) {
            $_SESSION['fsCaptcha'] .= $a[rand(0, $ac)];
        }
        for ($i = 0; $i < $lsc; ++$i) {
            $x1 = rand(0, $w / 2);
            $x2 = rand($w / 2, $w);
            $y1 = rand(0, $h / 2);
            $y2 = rand($h / 2, $h);
            imageline($img, $x1, $y1, $x2, $y2, $lineColor);
        }                         
        imagestring($img, rand(5, 12), rand(0, $w/2), rand(0, $h/2), $_SESSION['fsCaptcha'], $textColor);
        imagejpeg($img);
        exit;
    }
}