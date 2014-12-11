[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo fsHtml::Url($myLink.'DoConfig'); ?>" method="post">
  <p class="title">
   <?php _T('XMLcms_allow_register'); ?>:
   <?php echo fsHtml::Select('allow_register', array('1' => T('XMLcms_yes'), '0' => T('XMLcms_no')), $tag->settings->allow_register); ?>
  </p>
  <hr />
  <input class="fsCMS-btn admin-btn-save" type="submit" value="<?php _T('XMLcms_save'); ?>" />     
</form>      
[endblock-content]