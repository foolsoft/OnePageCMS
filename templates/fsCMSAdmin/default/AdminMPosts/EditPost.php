[parent:../AdminPanel/Index.php]

[block-content]
<?php
$postDate = explode(' ', $post['date']);
$postTime = $postDate[1];
$postDate = $postDate[0];
?>
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr /> 
<form action="<?php echo $myLink; ?>DoEditPost/key/<?php echo $post['id']; ?>/" method="post">
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'title', $post['title'], array('maxlength' => 100)); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'alt', $post['alt'], array('onkeyup' => 'fsCMS.Chpu(this.value, this.id);', 'maxlength' => 100)); ?>
  </p>
  <p class='title'>
    META - <?php _T('XMLcms_text_description'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'meta_description', $post['meta_description']); ?>
  </p>
  <p class='title'>
    META - <?php _T('XMLcms_text_kw'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'meta_keywords', $post['meta_keywords']); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_category'); ?>: <br />
    <select onclick="PostTemplateLoad($(this).val());" class='input-100' multiple name='id_category[]' size='10'>
      <?php foreach ($tag->categories as $category) { 
        $selected = in_array($category['id'], $tag->post_categories) ? 'selected' : '';
      ?>
        <option <?php echo $selected; ?> value='<?php echo $category['id']; ?>'><?php echo FunctionsPosts::GetFullCategoryName($tag->categories, $category); ?></option>
      <?php } ?>
    </select>
  </p>
  <p class='title'>
    <?php echo T('XMLcms_text_content').' ('.T('XMLcms_text_short').')'; ?>:<br />
    <?php echo fsHtml::TextareaMultiLanguage($tag->languages, 'html_short', $post['html_short'], array('class' => 'ckeditor')); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_content'); ?>:<br />
    <?php echo fsHtml::TextareaMultiLanguage($tag->languages, 'html_full', $post['html_full'], array('class' => 'ckeditor')); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_date'); ?>:
    <input value='<?php echo $postDate; ?>' id='datepicker' class='input-small' type='text' name='date' />
    <span class='space'></span>
    <?php _T('XMLcms_text_time'); ?>:
    <input value='<?php echo $postTime; ?>' class='input-small' type='text' name='time' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template'); ?>:
    <?php echo fsHtml::Select('tpl', $tag->templates, $post['tpl']); ?>
    <span class='space'></span>
    <?php _T('XMLcms_text_template_short'); ?>:
    <?php echo fsHtml::Select('tpl_short', $tag->templates_short, $post['tpl_short']); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_order'); ?>:
    <input class='input-position' type='number' name='position' value='<?php echo $post['position']; ?>' />
    <span class='space'></span>
    <?php _T('XMLcms_text_is_active'); ?>:
    <input <?php echo $post['active'] == 1 ? 'checked' : ''; ?> type='checkbox' name='active' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_auth_needed'); ?>: 
    <input type='checkbox' id='auth' name='auth' <?php echo $post['auth'] == 1 ? 'checked' : ''; ?> />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
</form>
[endblock-content]