[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form enctype='multipart/form-data' action="<?php echo fsHtml::Url($myLink.'DoAdd'); ?>" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_archive_module'); ?>:<br />
    <input size='50' type='file' name='userfile' />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />   
</form>
[endblock-content]