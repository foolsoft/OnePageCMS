[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Fields', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoEditField/key/<?php echo $tag->field->id; ?>/table/user_fields/" method='post'>
  <p>
    <?php _T('XMLcms_text_title'); ?>:<br />
    <input value='<?php echo $tag->field->title; ?>' onkeyup="fsCMS.Chpu(this.value, 'name');" class='input-100' maxlength='50' type='text' name='title' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input value='<?php echo $tag->field->name; ?>' onkeyup="fsCMS.Chpu(this.value, this.id);" id='name' class='input-100' maxlength='50' type='text' name='name' />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
</form>
[endblock-content]