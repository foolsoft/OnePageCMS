function LoadPostsTable(category)
{
  fsCMS.Ajax(
    URL_ROOT + 'AdminMPosts/AjaxPostsTable/category/' + category + '/',
    'POST', '', 'post-table', true 
  );
}
function PostTemplateLoad(val)
{
    var count = 0;
    $(val).each(function() { ++count });
    if (count > 1) {
        $('#tpl').prop('disabled', true);
        $('#tpl_short').prop('disabled', true);
    } else {
        $('#tpl').prop('disabled', false);
        $('#tpl_short').prop('disabled', false);
        fsCMS.Ajax(
            URL_ROOT + 'AdminMPosts/AjaxCategoryTemplate/category/' + val + '/',
            'POST', false, false, true, false,
            function(answer) {
                var data = $.parseJSON(answer);
                if(data['short']) {
                    $('#tpl_short').val(data['short']);
                }
                if(data['full']) {
                    $('#tpl').val(data['full']);
                }
                if(data['auth']) {
                    $('#auth').prop('checked', data['auth'] == '1');
                }
            }
        );
    }
}