[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Categories', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoAdd/call/Category/table/posts_category/referer/Categories/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input onkeyup="fsCMS.Chpu(this.value, 'alt');" class='input-100' maxlength='100' type='text' name='name' />
  </p>
  <p class='title'>
    <?php echo T('XMLcms_text_link'); ?>:<br />
    <input onkeyup="fsCMS.Chpu(this.value, this.id);" id='alt' class='input-100' maxlength='100' type='text' name='alt' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_description'); ?>:<br />
    <input class='input-100'
           maxlength='500'
           type='text'
           name='meta_description' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_kw'); ?>:<br />
    <input class='input-100'
           maxlength='500'
           type='text'
           name='meta_keywords' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_default_page_template'); ?>:
    <select name='tpl' class='input-small'>
    <?php foreach ($tag->templates as $template) { ?>
      <option value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select> 
  </p>
  <br />
  <p class='title'>
    <?php _T('XMLcms_text_template_short'); ?>:
    <select name='tpl_short' class='input-small'>
    <?php foreach ($tag->templates_ps as $template) { ?>
      <option value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select> 
    <span class="space"></span>
    <?php _T('XMLcms_text_template'); ?>:
    <select name='tpl_full' class='input-small'>
    <?php foreach ($tag->templates_pf as $template) { ?>
      <option value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select> 
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_auth_needed'); ?>: 
    <input type='checkbox' name='auth' />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />   
</form>
[endblock-content]