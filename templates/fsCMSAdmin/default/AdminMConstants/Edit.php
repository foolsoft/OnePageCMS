[parent:../AdminPanel/Index.php]

[block-content]
<?php
$name = $tag->key;
echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back'));
?>
<hr />
<form action="<?php echo $myLink; ?>DoEdit/key/<?php echo $name; ?>/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input value='<?php echo $name; ?>' id='name' class='input-100' maxlength='100' onkeyup='fsCMS.Chpu(this.value, this.id);' type='text' name='name' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_value'); ?>:<br />
    <textarea name='value' class='ckeditor'><?php echo $tag->constants->$name; ?></textarea>
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
</form>
[endblock-content]