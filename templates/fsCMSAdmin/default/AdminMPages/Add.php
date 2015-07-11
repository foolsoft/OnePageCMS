[parent:../AdminPanel/AddEdit.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoAdd/referer/Index/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_title'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'title', array(), array('onkeyup' => 'fsCMS.Chpu(this.value, \'alt-\' + this.getAttribute(\'id\').split(\'-\')[1]);', 'maxlength' => 100)); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'alt', array(), array('onkeyup' => 'fsCMS.Chpu(this.value, this.id);', 'maxlength' => 100)); ?>
  </p>
  <p>
    META - <?php _T('XMLcms_text_description'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'description'); ?>
  </p>
  <p>
    META - <?php _T('XMLcms_text_kw'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'keywords'); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_content'); ?>:<br />
    <?php echo fsHtml::TextareaMultiLanguage($tag->languages, 'html', array(), array('class' => 'ckeditor')); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_template'); ?>:
    <?php echo fsHtml::Select('tpl', $tag->templates); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_as_menu'); ?>: 
    <input type='checkbox' name='in_menu' checked />
    <span class='space'></span>
    <?php _T('XMLcms_text_is_active'); ?>: 
    <input type='checkbox' name='active' checked />
    <br />
    <?php _T('XMLcms_text_auth_needed'); ?>: 
    <input type='checkbox' name='auth' />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />
</form>
[endblock-content]