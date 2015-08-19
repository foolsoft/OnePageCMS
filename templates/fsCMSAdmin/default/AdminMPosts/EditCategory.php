[parent:../AdminPanel/Index.php]

[block-content]
<?php
$textImage = T('XMLcms_text_image');
echo fsHtml::Link($myLink.'Categories', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back'));
?>
<hr />
<form enctype="multipart/form-data" action="<?php echo $myLink; ?>DoEditCategory/key/<?php echo $category['id']; ?>/table/posts_category/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'title', $category['title'], array('maxlength' => 100)); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'alt', $category['alt'], array('onkeyup' => 'fsCMS.Chpu(this.value, this.id);', 'maxlength' => 100)); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_description'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'meta_description', $category['meta_description']); ?>
  </p>
  <p>
    <?php echo $textImage; ?>:<br />
    <span id="category-image">
    <?php if(!empty($category['image'])) { ?>
        <img src="<?php echo $category['image']; ?>" width="100" alt="<?php echo $textImage; ?>" title="<?php echo $textImage; ?>" />
        <a href="javascript:;" title="<?php _T('XMLcms_delete'); ?>" onclick="$('#category-image').html('<input type=\'file\' name=\'image\' />');"><?php _T('XMLcms_delete'); ?></a>
        <input type="hidden" name="image" value="<?php echo $category['image']; ?>" />
    <?php } else { ?>
        <input type="file" name="image" />
    <?php } ?>
    </span>
  </p>
  <p>
    <?php _T('XMLcms_text_kw'); ?>:<br />
    <?php echo fsHtml::EditorMultiLanguage($tag->languages, 'meta_keywords', $category['meta_keywords']); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_parent'); ?>:<br />
    <?php echo fsHtml::Select('id_parent', $tag->parents, $category['id_parent'], array('asis' => true, 'class' => 'input-100')); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template'); ?>:
    <?php echo fsHtml::Select('tpl', $tag->templates, $category['tpl']); ?>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template_short'); ?>:
    <?php echo fsHtml::Select('tpl_short', $tag->templates_pf, $category['tpl_short']); ?>
    <span class="space"></span>
    <?php _T('XMLcms_text_template'); ?>:
    <?php echo fsHtml::Select('tpl_full', $tag->templates_pf, $category['tpl_full']); ?>
  </p>
  <p>
    <?php _T('XMLcms_text_auth_needed'); ?>: 
    <input type='checkbox' name='auth' <?php echo $category['auth'] ? 'checked' : ''; ?> />
  </p>
  <hr /> 
  <input class="fsCMS-btn admin-btn-save" type="submit" value="<?php _T('XMLcms_save'); ?>" />   
</form>
[endblock-content]