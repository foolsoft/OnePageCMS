function LoadPostsTable(category) {
  fsCMS.Ajax(
    URL_ROOT + 'AdminMPosts/AjaxPostsTable/category/' + category + '/',
    'POST', '', 'post-table', true 
  );
}
function PostTemplateLoad(val) {
    if (val.length > 1) {
        $('#tpl, #tpl_short').prop('disabled', true);
    } else {
        $('#tpl, #tpl_short').prop('disabled', false);
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