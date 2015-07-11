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
        'alph' => 'qwerty123klzxcLKJvbnm4567890QWERTYUIOPHGFDSAuiopasdfghjZXCVBNM',
        'length' => 4,
        'linesCount' => 2,
        'bgColor' => array(255, 255, 255),
        'textColor' => array(0, 0, 0),
        'lineColor' => array(0, 0, 0) 
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
        $settings = array_merge(self::$_settings, $settings);
        $ac = strlen($settings['alph']);
        $img = imagecreatetruecolor($settings['width'], $settings['height']);
        $textColor = imagecolorallocate($img , $settings['textColor'][0], $settings['textColor'][1], $settings['textColor'][2]);
        $lineColor = imagecolorallocate($img , $settings['lineColor'][0], $settings['lineColor'][1], $settings['lineColor'][2]);
        $bgColor =  imagecolorallocate($img, $settings['bgColor'][0], $settings['bgColor'][1], $settings['bgColor'][2]);
        imagefill($img, 0, 0, $bgColor);
        $_SESSION['fsCaptcha'] = '';
        for ($i = 0; $i < $settings['length']; ++$i) {
            $_SESSION['fsCaptcha'] .= $settings['alph'][rand(0, $ac)];
        }
        for ($i = 0; $i < $settings['linesCount']; ++$i) {
            $x1 = rand(0, $settings['width'] / 2);
            $x2 = rand($settings['width'] / 2, $settings['width']);
            $y1 = rand(0, $settings['height'] / 2);
            $y2 = rand($settings['height'] / 2, $settings['height']);
            imageline($img, $x1, $y1, $x2, $y2, $lineColor);
        }                         
        imagestring($img, rand(5, 12), rand(0, $settings['width']/2), rand(0, $settings['height']/2), $_SESSION['fsCaptcha'], $textColor);
        imagejpeg($img);
        exit;
    }
}