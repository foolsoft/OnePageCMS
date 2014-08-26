[parent:../AdminPanel/Index.php]

[block-content]
<?php
$isMainAdmin = fsConfig::GetInstance('main_admin') == $tag->user->login;
echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back'));
?>
<hr />
<form action="<?php echo $myLink; ?>DoEdit/key/<?php echo $tag->user->id; ?>/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_login'); ?>:<br />
    <input <?php echo $isMainAdmin ? 'readonly' : ''; ?>
           class='input-100'
           maxlength='50'
           type='text'
           name='login'
           value='<?php echo $tag->user->login; ?>' 
     />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_password'); ?>:<br />
    <input class='input-100' maxlength='50' type='password' name='password' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_repassword'); ?>:<br />
    <input class='input-100' maxlength='50' type='password' name='rpassword' />
  </p>   
  <?php if(!$isMainAdmin) { ?>
  <p class='title'>
    <?php _T('XMLcms_text_type'); ?>:
    <select class='select-small' name='type'>
    <?php foreach ($tag->types as $type) { ?>
      <option
        <?php echo $tag->user->type == $type['id'] ? 'selected' : ''; ?>
        value="<?php echo $type['id']; ?>"
      ><?php echo $type['name']; ?></option>
    <?php } ?>
    </select>
    <span class='space'></span>
    <?php _T('XMLcms_text_he_active'); ?>:
    <input type='checkbox'
           name='active'
           <?php echo $tag->user->active == '1' ? 'checked' : ''; ?>
     />
  </p>
  <?php } ?>
  <?php foreach ($tag->fields as $field) { ?>
  <p>
    <?php _T($field['title']); ?>:<br />
    <input class='input-100' value='<?php echo $field['value']; ?>' type='text' name='user_field[<?php echo $field['id']; ?>]' />
  </p> 
  <?php } ?>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
</form>
[endblock-content]