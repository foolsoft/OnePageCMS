[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Fields', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoFieldsEdit/key/<?php echo $tag->field->name; ?>/" method="post">
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input value="<?php echo $tag->field->name; ?>" id='name' class='input-100' maxlength='15' onkeyup='fsCMS.Chpu(this.value, this.id);' type='text' name='name' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_title'); ?>:<br />
    <input class="input-100" name="title" value="<?php echo $tag->field->title; ?>" maxlength='50' />
  </p>
  <p>
    <?php _T('XMLcms_text_required'); ?>:
    <input type="checkbox" name="required" <?php echo $tag->field->required == '1' ? 'checked' : ''; ?> />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
</form>
[endblock-content] 