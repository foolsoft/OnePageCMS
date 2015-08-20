<?php
/**
* Page navigation class.
* @package fsKernel
*/
class fsPaginator 
{
   /**
   * Get page navigation line.
   * @param string $link Base url.
   * @param string $param Page GET parameter.
   * @param integer $count Total records count.           
   * @param integer $pp (optional) Records per page. Default <b>20</b>.
   * @param integer $current (optional) Current page from 1. Default <b>1</b>.   
   * @param array $htmlAttributes (optional) Url HTML attributes. Default <b>empty array</b>.
   * @return string HTML code for page select. 
   */
    public static function Get($link, $param, $count, $pp = 20, $current = 1, $htmlAttributes = array())
    {
        $pCount = ($count % $pp == 0) ? (int)($count / $pp) : (int)($count / $pp) + 1;
        if($pCount < 2) {
          return '';
        }
        $attributes = ''; $PT = T('XMLcms_page');
        if(isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] .= ' paginator-item';
        } else {
            $htmlAttributes['class'] = 'paginator-item';
        }
        foreach($htmlAttributes as $attributeName => $attributeValue) {
          $attributes .= ' '.$attributeName.'="'.$attributeValue.'"';
        }
        $asReplace = preg_match('/^{.+}$/', $param);
        $html = '<span class="text-pages">'.T('XMLcms_pages').':</span> ';     
        $sym = false === strpos($link, '?') ? '?' : (substr($link, -1) == '&' ? '' : '&');
        for ($i = 1; $i <= $pCount; ++$i) {
          $html .= ($i != $current
                    ? "<a ".$attributes." href='".($asReplace ? str_replace($param, $i, $link) : $link.($i == 1 ? '' : $sym.$param.'='.$i))."' title='".$PT." ".$i."'>".$i.'</a>'
                    : '<b>'.$i.'</b>').
                ($i == $pCount ? '' : ' | ');
        }
        return $html;
    }
    
    /**
    * Get url for next page.
    * @param string $link Base url.
    * @param string $param Page GET parameter.
    * @param integer $count Total records count.
    * @param integer $pp (optional) Records per page. Default <b>20</b>.
    * @param integer $current (optional) Current page from 1. Default <b>1</b>.
    * @return string Url for next page.
    */
    public static function NextPage($link, $param, $count, $pp = 20, $current = 1)
    {
        $pCount = ($count % $pp == 0) ? (int)($count / $pp) : (int)($count / $pp) + 1;
        if($pCount < 2) {
          return '';
        }
        $sym = false === strpos($link, '?') ? '?' : (substr($link, -1) == '&' ? '' : '&');
        $asReplace = preg_match('/^{.+}$/', $param);
        return $pCount == $current
            ? ''
            : ($asReplace ? str_replace($param, ($current + 1), $link) : $link.$sym.$param.'='.($current + 1));
    }

    /**
    * Get url for prevoius page.
    * @param string $link Base url.
    * @param string $param Page GET parameter.
    * @param integer $current (optional) Current page from 1. Default <b>1</b>.
    * @return string Url for previous page.
    */
    public static function PreviousPage($link, $param, $current = 1)
    {
        $sym = false === strpos($link, '?') ? '?' : (substr($link, -1) == '&' ? '' : '&');
        $asReplace = preg_match('/^{.+}$/', $param);
        return 1 == $current
            ? ''
            : ($asReplace ? str_replace($param, ($current - 1), $link) : $link.$sym.$param.'='.($current - 1));
    }
}