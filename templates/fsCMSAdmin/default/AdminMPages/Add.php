[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoAdd/referer/Index/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_title'); ?>:<br />
    <input onkeyup="fsCMS.Chpu(this.value, 'alt');" class='input-100' maxlength='100' type='text' name='title' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <input id='alt' onkeyup="fsCMS.Chpu(this.value, this.id);" class='input-100' maxlength='100' type='text' name='alt' />
  </p>
  <p>
    META - <?php _T('XMLcms_text_description'); ?>:<br />
    <input class='input-100' maxlength='500' type='text' name='description' />
  </p>
  <p>
    META - <?php _T('XMLcms_text_kw'); ?>:<br />
    <input class='input-100' maxlength='500' type='text' name='keywords' />
  </p>
  <p>
    <?php _T('XMLcms_text_content'); ?>:<br />
    <textarea name='html' class='ckeditor'></textarea>
  </p>
  <p>
    <?php _T('XMLcms_text_template'); ?>:
    <select name='tpl'>
    <?php
    foreach ($tag->templates as $template) {
    ?>
      <option value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php
    }
    ?>
    </select>
  </p>
  <p>
    <?php _T('XMLcms_text_as_menu'); ?>: 
    <input type='checkbox' name='in_menu' checked />
    <span class='space'></span>
    <?php _T('XMLcms_text_is_active'); ?>: 
    <input type='checkbox' name='active' checked />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />
</form>
[endblock-content]