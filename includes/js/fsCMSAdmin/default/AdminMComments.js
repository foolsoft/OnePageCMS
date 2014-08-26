function CommentSave(form) 
{
  fsCMS.Ajax(
    $(form).attr('action'),
    'post', 
    $(form).serialize(),
    'message',
    'message',
    16
  );
  return false;
}