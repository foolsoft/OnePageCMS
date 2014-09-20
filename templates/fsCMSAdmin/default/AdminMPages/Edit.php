[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo $myLink; ?>DoEdit/key/<?php echo $tag->page->id; ?>/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_title'); ?>:<br />
    <input value='<?php echo $tag->page->title; ?>' class='input-100' maxlength='100' type='text' name='title' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <input value='<?php echo $tag->page->alt; ?>' id='alt' onkeyup="fsCMS.Chpu(this.value, this.id);" class='input-100' maxlength='100' type='text' name='alt' />
  </p>
  <p>
    META - <?php _T('XMLcms_text_description'); ?>:<br />
    <input value='<?php echo $tag->page->description; ?>' class='input-100' maxlength='500' type='text' name='description' />
  </p>
  <p>
    META - <?php _T('XMLcms_text_kw'); ?>:<br />
    <input  value='<?php echo $tag->page->keywords; ?>' class='input-100' maxlength='500' type='text' name='keywords' />
  </p>
  <p>
    <?php _T('XMLcms_text_content'); ?>:<br />
    <textarea name='html' class='ckeditor'><?php echo $tag->page->html; ?></textarea>
  </p>
  <p>
    <?php _T('XMLcms_text_template'); ?>:
    <select name='tpl'>
    <?php
    foreach ($tag->templates as $template) {
      $selected = $tag->page->tpl == $template ? 'selected' : '';
    ?>
      
      <option <?php echo $selected; ?> value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php
    }
    ?>
    </select>
  </p>
  <p>
    <?php _T('XMLcms_text_as_menu'); ?>: 
    <input type='checkbox' name='in_menu' <?php echo $tag->page->in_menu == 1 ? 'checked' : ''; ?> />
    <span class='space'></span>
    <?php _T('XMLcms_text_is_active'); ?>: 
    <input type='checkbox' name='active' <?php echo $tag->page->active == 1 ? 'checked' : ''; ?> />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />
</form>
[endblock-content]