[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Categories', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoEdit/key/<?php echo $tag->category->id; ?>/call/Category/table/posts_category/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input class='input-100'
           maxlength='100'
           type='text'
           value='<?php echo $tag->category->name; ?>'
           name='name' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <input onkeyup="fsCMS.Chpu(this.value, this.id);"
           id='alt'
           class='input-100'
           maxlength='100'
           value='<?php echo $tag->category->alt; ?>'
           type='text'
           name='alt' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_description'); ?>:<br />
    <input class='input-100'
           maxlength='500'
           value='<?php echo $tag->category->meta_description; ?>'
           type='text'
           name='meta_description' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_kw'); ?>:<br />
    <input class='input-100'
           maxlength='500'
           value='<?php echo $tag->category->meta_keywords; ?>'
           type='text'
           name='meta_keywords' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template'); ?>:
    <select name='tpl' class='input-small'>
    <?php foreach ($tag->templates as $template) { 
      $selected = $template == $tag->category->tpl ? 'selected' : '';
    ?>
      <option <?php echo $selected; ?> value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select> 
  </p>
  <br />
  <p class='title'>
    <?php _T('XMLcms_text_template_short'); ?>:
    <select name='tpl_short' class='input-small'>
    <?php foreach ($tag->templates_ps as $template) { 
        $selected = $template == $tag->category->tpl_short ? 'selected' : '';            
    ?>
      <option <?php echo $selected; ?> value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select> 
    <span class="space"></span>
    <?php _T('XMLcms_text_template'); ?>:
    <select name='tpl_full' class='input-small'>
    <?php foreach ($tag->templates_pf as $template) {
        $selected = $template == $tag->category->tpl_full ? 'selected' : '';
    ?>
      <option <?php echo $selected; ?> value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select> 
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_auth_needed'); ?>: 
    <input type='checkbox' name='auth' <?php echo $tag->category->auth == 1 ? 'checked' : ''; ?> />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
</form>
[endblock-content]