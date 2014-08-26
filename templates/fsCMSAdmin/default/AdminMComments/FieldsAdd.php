[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Fields', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoFieldsAdd/referer/Fields/" method="post">
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input id='name' class='input-100' maxlength='15' onkeyup='fsCMS.Chpu(this.value, this.id);' type='text' name='name' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_title'); ?>:<br />
    <input type="text" name="title" class='input-100' value="" maxlength='50' />
  </p>
  <p>
    <?php _T('XMLcms_text_required'); ?>:
    <input type="checkbox" name="required" />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />   
</form>
[endblock-content]