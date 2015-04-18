[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoAddPost/referer/Index/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'title', array(), array('onkeyup' => 'fsCMS.Chpu(this.value, \'alt-\' + this.getAttribute(\'id\').split(\'-\')[1]);', 'maxlength' => 100)); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'alt', array(), array('onkeyup' => 'fsCMS.Chpu(this.value, this.id);', 'maxlength' => 100)); ?>
  </p>
  <p class='title'>
    META - <?php _T('XMLcms_text_description'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'meta_description'); ?>
  </p>
  <p class='title'>
    META - <?php _T('XMLcms_text_kw'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'meta_keywords'); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_category'); ?>: <br />
    <select onclick="PostTemplateLoad($(this).val());" class='input-100' multiple name='id_category[]' size='10'>
      <?php foreach ($tag->categories as $category) { ?>
        <option value='<?php echo $category['id']; ?>'><?php echo FunctionsPosts::GetFullCategoryName($tag->categories, $category); ?></option>
      <?php } ?>
    </select>
  </p>
  <p class='title'>
    <?php echo T('XMLcms_text_content').' ('.T('XMLcms_text_short').')'; ?>:<br />
    <?php echo fsHtml::TextareaMultiLanguage($tag->languages, 'html_short', array(), array('class' => 'ckeditor')); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_content'); ?>:<br />
    <?php echo fsHtml::TextareaMultiLanguage($tag->languages, 'html_full', array(), array('class' => 'ckeditor')); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_date'); ?>:
    <input id='datepicker' class='input-small' type='text' name='date' value='<?php echo date('Y-m-d'); ?>' />
    <span class='space'></span>
    <?php _T('XMLcms_text_time'); ?>:
    <input class='input-small' type='text' name='time' value='<?php echo date('H:i:s'); ?>' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template'); ?>:
    <?php echo fsHtml::Select('tpl', $tag->templates); ?>
    <span class='space'></span>
    <?php _T('XMLcms_text_template_short'); ?>:
    <?php echo fsHtml::Select('tpl_short', $tag->templates_short); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_order'); ?>:
    <input class='input-small' type='number' name='position' value='0' />
    <span class='space'></span>
    <?php _T('XMLcms_text_is_active'); ?>:
    <input checked type='checkbox' name='active' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_auth_needed'); ?>: 
    <input type='checkbox' name='auth' id='auth' />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />   
</form>
[endblock-content]