[parent:../AdminPanel/AddEdit.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoEdit/key/<?php echo $page['id']; ?>/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_title'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'title', $page['title'], array('maxlength' => 100)); ?>
  </p>
  <?php if($page['id'] > 0) { ?>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'alt', $page['alt'], array('onkeyup' => 'fsCMS.Chpu(this.value, this.id);', 'maxlength' => 100)); ?>
  </p>
  <?php } else { ?>
    <?php echo fsHtml::HiddenMultiLanguage($tag->languages, 'alt', $page['alt']); ?>
  <?php } ?>
  <p>
    META - <?php _T('XMLcms_text_description'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'description', $page['description']); ?>
  </p>
  <p>
    META - <?php _T('XMLcms_text_kw'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'keywords', $page['keywords']); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_content'); ?>:<br />
    <?php echo fsHtml::TextareaMultiLanguage($tag->languages, 'html', $page['html'], array('class' => 'ckeditor')); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_template'); ?>:
    <?php echo fsHtml::Select('tpl', $tag->templates, $page['tpl']); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_as_menu'); ?>: 
    <input type='checkbox' name='in_menu' <?php echo $page['in_menu'] == 1 ? 'checked' : ''; ?> />
    <span class='space'></span>
    <?php _T('XMLcms_text_is_active'); ?>: 
    <input type='checkbox' name='active' <?php echo $page['active'] == 1 ? 'checked' : ''; ?> />
    <br />
    <?php _T('XMLcms_text_auth_needed'); ?>: 
    <input type='checkbox' name='auth' <?php echo $page['auth'] == 1 ? 'checked' : ''; ?> />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />
</form>
[endblock-content]