function CommentsAdd(form) 
{
  fsCMS.Ajax(
    $(form).attr('action'),
    'post', 
    $(form).serialize(),
    false,
    'loader',
    16,
    function(answer) {
      var json = JSON.parse(answer);
      if(json.Status==0) {
        CommentsUpdate($(form).find('input[name=group]').val());
        $(form).html(json.Text);
      } else {
        $('#loader').html(json.Text);
      }
    }
  );
  return false;
}

function CommentsUpdate(group, id) 
{
  id = id || 'comments-' + group;
  if(document.getElementById(id) == null) {
    return;
  }
  fsCMS.Ajax(URL_ROOT + 'MComments/Comments' + URL_SUFFIX, 'post', 'group='+group , id, id, 16);
}

function CommentsPage(group, page, id) 
{
  page = page || 1;
  id = id || 'comments-' + group;
  if(document.getElementById(id) == null) {
    return;
  }
  fsCMS.Ajax(URL_ROOT + 'MComments/Comments' + URL_SUFFIX, 'post', 'comment_page=' + page + '&group='+group , id, id, 16);
}

function CommentAnswer(commentId)
{
  if(commentId < 1) {
    return;
  }
  fsCMS.Ajax(URL_ROOT + 'MComments/Form' + URL_SUFFIX, 'post', 'parent='+commentId , 'comment-'+commentId+'-ajax', 'comment-'+commentId+'-ajax', 16);
}

function CommentDelete(commentId)
{
  if(commentId < 1) {
    return;
  }
  fsCMS.Ajax(URL_ROOT + 'MComments/Delete' + URL_SUFFIX, 'post', 'id='+commentId , 'comment-'+commentId, 'comment-'+commentId+'-ajax', 16);
}