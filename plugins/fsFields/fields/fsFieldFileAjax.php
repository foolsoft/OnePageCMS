<?php
class fsFieldFileAjax extends fsField
{
  protected $_title;
  protected $_name;
  
  public function Input($htmlFormName, $value = '', $htmlAttributes = array(), $possibleValues = array())
  {
    $params = '?name='.$htmlFormName.'ajax';
    if(isset($htmlAttributes['params'])) {
      $params .= '&'.$htmlAttributes['params'];
      unset($htmlAttributes['params']);
    }
    $jsRemove = ''; $js = ''; $code = '';
    if(isset($htmlAttributes['js'])) {
      $js = $htmlAttributes['js'];
      unset($htmlAttributes['js']);
    }
    if(isset($htmlAttributes['jsRemove'])) {
      $jsRemove = $htmlAttributes['jsRemove'];
      unset($htmlAttributes['jsRemove']);
    }
    if(isset($htmlAttributes['html'])) {
      $code = $htmlAttributes['html'];
      unset($htmlAttributes['html']);
    }
    $sha1 = sha1($htmlFormName);
    $html = '<script type="text/javascript">$(document).ready(function() {';
    $html .= '$.ajax_upload($("#'.md5($htmlFormName).'"), {';
    $html .= 'action: "'.fsHtml::Url(URL_ROOT.'fsFieldFileController/Upload'.$params).'", name: "'.$htmlFormName.'ajax",
    onSubmit: function(file, ext) { fsCMS.WaitAnimation("on"); this.disable(); },
    onComplete: function(file, response) { fsCMS.WaitAnimation("off"); var temp = response.split(":"); this.enable(); if(temp[0] == "error") { alert(temp[1]); return; }
    $("#delete'.$sha1.'").show();var file=temp[1];$("#'.$sha1.'").val(file); $("#fs'.$sha1.'").text(file);'.$js.'}';
    $html .= '});});</script>';
    
    $html .= fsHtml::Button(T('XMLcms_browse'), false, array('id' => md5($htmlFormName)));
    $html .= fsHtml::Hidden('fields['.$htmlFormName.']', $value, array('id' => $sha1));
    $html .= ' <span id="fs'.$sha1.'">'.$value.'</span> '.
      fsHtml::Button(T('XMLcms_delete'), "$(this).hide();$('#".$sha1."').val('');$('#fs".$sha1."').text('');".$jsRemove, array('id' => 'delete'.$sha1, 'class' => $value == '' ? 'hidden' : '')).$code;
    return $html;
  }
  
  public function __construct($name = 'fileajax', $title = 'File (ajax)')
  {
    parent::__construct($name, $title);
  }
}