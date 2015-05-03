$(window).scroll(function () {
  var menu = $("#content-left-panel"), menu2 = $("#menu-level-2");
  if ($(window).scrollTop() > 40) {
    menu.css('position', 'fixed'); menu.css('width', '15%'); menu.css('top', '10px');
    menu2.css('position', 'fixed');
  } else {
    menu.css('position', ''); menu.css('width', ''); menu.css('top', '');
    menu2.css('position', 'absolute');
  }
});

$(document).ready(function() {
  setTimeout(function() { $('#fs-controller-message').slideUp(); }, 5000);
  CmsInitJsEvents();
});

$(document).ajaxComplete(function (event, xhr, settings) {
  if (xhr.responseHTML != '') {
    setTimeout(function() {
      var selector = '.ajax-content, #ajax-content'; 
      CmsInitJsEvents(selector); 
    }, 1500);
  }
});

function CmsDestroyJsEvents(selector) {
  $(selector).find('a.confirm, input.confirm[type="button"], button.confirm').off('click');
  $(selector).find('form.confirm, form.cms-ajax').off('submit');
  $(selector).find('.fancybox').off("click.fb-start");
}

function CmsInitJsEvents(selector) {
  selector = selector || 'body';
  $(selector).find("#datepicker, .datepicker").datepicker({dateFormat: 'yy-mm-dd'});
  $(selector).find('.fancybox').fancybox();
  $(selector).find('.sticky-0').sticky({ topSpacing: 0 });
  $(selector).find('form.confirm').submit(function() { return confirm(T('cms_text_sure')); });
  $(selector).find('a.confirm, input.confirm[type="button"], button.confirm').click(function() { return confirm(T('cms_text_sure')); });
  $(selector).find('form.cms-ajax').submit(function() { return FormAjax(this); });
}

function FormAjax(form) {
  var $this = $(form);
  var callback = $this.data('callback');
  var idForResult = $this.data('result-id') || false;
  fsCMS.Ajax($this.attr('action') || '', $this.attr('method') || 'post', $this.serialize(), idForResult, idForResult, 16, function(answer) {
    if(typeof(callback) == 'function') {
      callback(answer);
    }
  });
  return false;
}

function TemplateManagerLoadFiles(template) {
  fsCMS.Ajax(URL_ROOT + 'AdminPanel/TemplateManager' + URL_SUFFIX, 'post', 'template='+template, 'file', 'template-manager-message', 16, function(answer) {
    TemplateManagerLoadFile($('#file').val());  
  });  
}

function TemplateManagerLoadFile(file) {
  if(file != '') {
    fsCMS.Ajax(URL_ROOT + 'AdminPanel/TemplateManager' + URL_SUFFIX, 'post', 'file=' + file + '&template='+$('#template').val(), 'template-redactor', 'template-manager-message', 16, function(answer) {
      cmsAdminAceEditor = ace.edit("template-manager-editor");
      cmsAdminAceEditor.getSession().setMode("ace/mode/" + $('#syntax').val());
      cmsAdminAceEditor.getSession().on('change', function(){ $('#template-manager-content').val(cmsAdminAceEditor.getSession().getValue()); });
      $('#template-manager-content').val(cmsAdminAceEditor.getSession().getValue());
    });
  } else {
    $('#template-redactor').find('textarea').val('');
  }  
}

function CKeditorRenew(parent) 
{
  parent = parent || 'body';
  var e = $(parent).find('.ckeditor');
  for (var i = 0; i < e.length; ++i) { 
    if (CKEDITOR.instances[$(e[i]).attr('id')]) {
       CKEDITOR.instances[$(e[i]).attr('id')].destroy(true);
    }
    CKEDITOR.replace($(e[i]).attr('id'));
  }
}

var cmsAdminAceEditor = null;