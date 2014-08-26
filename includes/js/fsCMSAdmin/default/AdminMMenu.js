function LoadItemsInMenu(menu)
{
  fsCMS.Ajax(
              URL_ROOT + 'AdminMMenu/AjaxItemsInMenu/menu/' + menu + '/',
              'POST',
              '',
              'parent,redact_name',
              true
            );
}

function GetReadyLink(obj)
{
  var href = $(obj).val();
  $('#href').attr('value', href);
  $('#title').attr('value', href == '' ? '' : $('#ready_href option:selected').text());
}

function DeleteMenuItem(key)
{
  window.location = URL_ROOT + 'AdminMMenu/Delete/menu_name/' + $('#menu_name').val() + '/table/menu_items/referer/EditItems/key/'+key+'/';
}
