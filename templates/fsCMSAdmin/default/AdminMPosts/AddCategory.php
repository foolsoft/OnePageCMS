[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Categories', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoAddCategory/table/posts_category/referer/Categories/" method="post">
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'title', array(), array('onkeyup' => 'fsCMS.Chpu(this.value, \'alt-\' + this.getAttribute(\'id\').split(\'-\')[1]);', 'maxlength' => 100)); ?>
  </p>
  <p class='title'>
    <?php echo T('XMLcms_text_link'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'alt', array(), array('onkeyup' => 'fsCMS.Chpu(this.value, this.id);', 'maxlength' => 100)); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_description'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'meta_description'); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_kw'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'meta_keywords'); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_parent'); ?>:<br />
    <?php echo fsHtml::Select('id_parent', $tag->parents, false, array('asis' => true, 'class' => 'input-100')); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_default_page_template'); ?>:
    <?php echo fsHtml::Select('tpl', $tag->templates); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template_short'); ?>:
    <?php echo fsHtml::Select('tpl_short', $tag->templates_ps); ?>
    <span class="space"></span>
    <?php _T('XMLcms_text_template'); ?>:
    <?php echo fsHtml::Select('tpl_full', $tag->templates_pf); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_auth_needed'); ?>: 
    <input type='checkbox' name='auth' />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />   
</form>
[endblock-content]