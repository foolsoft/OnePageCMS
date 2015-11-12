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
   * @param array $options (optional) Display options. Default <b>empty array</b>.
   * @return string HTML code for page select. 
   */
    public static function Get($link, $param, $count, $pp = 20, $current = 1, $htmlAttributes = array(), $options = array())
    {
        $options = array_merge(array(
            'delimiter' => ' | ',
            'maxSize' => 0,
            'hiddenPagesHtml' => ' <span class="hidden-pages">...</span> ',
            'beforeHtml' => '<span class="text-pages">'.T('XMLkernel_pages').':</span> ',
            'afterHtml' => '',
            'showToFirst' => true,
            'toFirstText' => T('XMLkernel_first'),
            'showToLast' => true,
            'toLastText' => T('XMLkernel_last'),
        ), $options);
        $pCount = ($count % $pp == 0) ? (int)($count / $pp) : (int)($count / $pp) + 1;
        if($pCount < 2) {
          return '';
        }
        $attributes = ''; $titleText = T('XMLkernel_page');
        if(isset($htmlAttributes['class'])) {
            $htmlAttributes['class'] .= ' paginator-item';
        } else {
            $htmlAttributes['class'] = 'paginator-item';
        }
        foreach($htmlAttributes as $attributeName => $attributeValue) {
          $attributes .= ' '.$attributeName.'="'.$attributeValue.'"';
        }
        $asReplace = preg_match('/^{.+}$/', $param);
        $html = $options['beforeHtml'];
        $sym = false === strpos($link, '?') ? '?' : (substr($link, -1) == '&' ? '' : '&');

        $offset = $fixOffset = 0;
        $from = 1; $till = $pCount;
        if($options['maxSize'] > 0) {
          $offsetLeft = (int)($options['maxSize'] / 2);
          $offsetRight = $options['maxSize'] % 2 == 0 ? $offsetLeft - 1 : $offsetLeft;
          $from = $pCount > $options['maxSize'] ? $current - $offsetLeft : 1;
          $till = $pCount > $options['maxSize'] ? $from + $offsetLeft + $offsetRight : $pCount;
          if($from < 1) {
            $till += (1 - $from);
            $from = 1;
          }
          if($till > $pCount) {
            $from -= ($till - $pCount);
            $till = $pCount;
          }
        }
        if($options['showToFirst'] && $from > 1) {
            $html .= "<a ".$attributes." href='".($asReplace ? str_replace($param, $i, $link) : $link)."' title='".$titleText." 1'>".$options['toFirstText']."</a>"
                .$options['delimiter'].$options['hiddenPagesHtml'].$options['delimiter'];
        }
        for ($i = $from; $i <= $till; ++$i) {
          $html .= ($i != $current
                    ? "<a ".$attributes." href='".($asReplace ? str_replace($param, $i, $link) : $link.($i == 1 ? '' : $sym.$param.'='.$i))."' title='".$titleText." ".$i."'>".$i.'</a>'
                    : '<b>'.$i.'</b>').
                ($i == $till ? '' : $options['delimiter']);
        }
        if($options['showToLast'] && $till < $pCount) {
            $html .= $options['delimiter'].$options['hiddenPagesHtml'].$options['delimiter']."<a ".$attributes." href='".($asReplace ? str_replace($param, $i, $link) : $link.$sym.$param.'='.$pCount)."' title='".$titleText." ".$pCount."'>".$options['toLastText']."</a>";
        }
        $html .= $options['afterHtml'];
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
        return $pCount == $current ? ''
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
        return 1 == $current ? ''
            : ($asReplace ? str_replace($param, ($current - 1), $link) : $link.$sym.$param.'='.($current - 1));
    }
}