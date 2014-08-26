[parent:../AdminPanel/Index.php]

[block-content]
<?php
echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back'));
?>
<hr />
<form action="<?php echo $myLink; ?>DoAdd/referer/Index/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_login'); ?>:<br />
    <input class='input-100' maxlength='50' type='text' name='login' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_password'); ?>:<br />
    <input class='input-100' maxlength='50' type='password' name='password' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_repassword'); ?>:<br />
    <input class='input-100' maxlength='50' type='password' name='rpassword' />
  </p>   
  <p class='title'>
    <?php echo T('XMLcms_text_type'); ?>:
    <select class='select-small' name='type' >
    <?php foreach ($tag->types as $type) { ?>
      <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
    <?php } ?>
    </select>
    <span class='space'></span>
    <?php _T('XMLcms_text_he_active'); ?>:
    <input type='checkbox' name='active' checked />
  </p>
  <?php foreach ($tag->fields as $field) { ?>
  <p>
    <?php _T($field['title']); ?>:<br />
    <input class='input-100' value='' type='text' name='user_field[<?php echo $field['id']; ?>]' />
  </p> 
  <?php } ?>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />   
</form>
[endblock-content]