$(document).ready(function() {
  UpdateRedactor();
  $('#lang').change(function() { UpdateRedactor(); });  
});

function UpdateRedactor() 
{
  fsCMS.Ajax(URL_ROOT + 'AdminMDictionary/Redactor' + URL_SUFFIX, 'post', 'lang=' + $('#lang').val(), 'dictionary-redactor', 'dictionary-redactor', 16, function(answer) {
  });
}

function DictionaryXmlTranslate(lang, row)
{
  $('.xml-translate').hide();
  $('#xml-' + row + '-' + lang).show();
  CKeditorRenew('#xml-' + row + '-' + lang);
}

function DictionaryAddWord()
{
  redactorIndex = redactorIndex + 1;
  fsCMS.Ajax(URL_ROOT + 'AdminMDictionary/NewRow' + URL_SUFFIX, 
  'post', 
  'row=' + redactorIndex + '&lang=' + $('#lang').val(), 
  false, false, false, function(answer) {
    $('#dictionary-redactor').append(answer);
    $('#original\\['+ redactorIndex + '\\]').focus();
  });
}