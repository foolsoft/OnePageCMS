[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Groups', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoAddGroup/referer/Groups/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input class='input-100' maxlength='100' type='text' name='name' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_allow_actions'); ?>:<br />
    <textarea class='input-100' name='allow'>*</textarea>
  </p>
  <p class='title'>
    <?php _T('XMLcms_disallow_actions'); ?>:<br />
    <textarea class='input-100' name='disallow'></textarea>
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />   
</form>
[endblock-content]