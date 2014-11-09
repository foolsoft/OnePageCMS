[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoAdd/referer/Index/call/Post/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input onkeyup="fsCMS.Chpu(this.value, 'alt');" class='input-100' maxlength='100' type='text' name='title' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <input onkeyup="fsCMS.Chpu(this.value, this.id);" id='alt' class='input-100' maxlength='100' type='text' name='alt' />
  </p>
  <p class='title'>
    META - <?php _T('XMLcms_text_description'); ?>:<br />
    <input class='input-100' maxlength='500' type='text' name='meta_decription' />
  </p>
  <p class='title'>
    META - <?php _T('XMLcms_text_kw'); ?>:<br />
    <input class='input-100' maxlength='500' type='text' name='meta_keywords' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_category'); ?>: <br />
    <select onclick="PostTemplateLoad($(this).val());" class='input-100' multiple name='id_category[]' size='10'>
      <?php foreach ($tag->categories as $category) { ?>
        <option value='<?php echo $category['id']; ?>'><?php echo PostsFunctions::GetFullCategoryName($tag->categories, $category); ?></option>
      <?php } ?>
    </select>
  </p>
  <p class='title'>
    <?php echo T('XMLcms_text_content').' ('.T('XMLcms_text_short').')'; ?>:<br />
    <textarea name="html_short" class="ckeditor"></textarea>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_content'); ?>:<br />
    <textarea name="html_full" class="ckeditor"></textarea>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_date'); ?>:
    <input id='datepicker' class='input-small' type='text' name='date' value='<?php echo date('Y-m-d'); ?>' />
    <span class='space'></span>
    <?php _T('XMLcms_text_time'); ?>:
    <input class='input-small' type='text' name='time' value='<?php echo date('H:i:s'); ?>' />
    <span class='space'></span>
    <?php _T('XMLcms_text_order'); ?>:
    <input class='input-small' type='text' name='order' value='0' onkeyup="fsCMS.IsNumeric(this, 0, true, true);" />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template'); ?>:
    <select id="tpl" name='tpl'>
    <?php foreach ($tag->templates as $template) { ?>
      <option value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select>
    <span class='space'></span>
    <?php echo T('XMLcms_text_template_short'); ?>:
    <select id="tpl_short" name='tpl_short'>
    <?php foreach ($tag->templates_short as $template) { ?>
      <option value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select>    
  </p>
  <p class='title'>
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