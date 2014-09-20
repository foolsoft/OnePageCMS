[parent:../AdminPanel/Index.php]

[block-content]
<?php
$postDate = explode(' ', $tag->post->date);
$postTime = $postDate[1];
$postDate = $postDate[0];
?>
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr /> 
<form action="<?php echo $myLink; ?>DoEdit/key/<?php echo $tag->post->id; ?>/call/Post/" method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_name'); ?>:<br />
    <input value="<?php echo $tag->post->title; ?>" class='input-100' maxlength='100' type='text' name='title' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_link'); ?>:<br />
    <input  value="<?php echo $tag->post->alt; ?>" onkeyup="fsCMS.Chpu(this.value, this.id);" id='alt' class='input-100' maxlength='100' type='text' name='alt' />
  </p>
  <p class='title'>
    META - <?php _T('XMLcms_text_description'); ?>:<br />
    <input  value="<?php echo $tag->post->meta_description; ?>" class='input-100' maxlength='500' type='text' name='meta_description' />
  </p>
  <p class='title'>
    META - <?php _T('XMLcms_text_kw'); ?>:<br />
    <input  value="<?php echo $tag->post->meta_keywords; ?>" class='input-100' maxlength='500' type='text' name='meta_keywords' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_category'); ?>: <br />
    <select onclick="PostTemplateLoad($(this).val());" class='input-100' multiple name='id_category[]' size='10'>
      <?php foreach ($tag->categories as $category) { 
        $selected = in_array($category['id'], $tag->post_categories) ? 'selected' : '';
      ?>
        <option <?php echo $selected; ?> value='<?php echo $category['id']; ?>'><?php echo $category['name']; ?></option>
      <?php } ?>
    </select>
  </p>
  <p class='title'>
    <?php echo T('XMLcms_text_content').' ('.T('XMLcms_text_short').')'; ?>:<br />
    <textarea name="html_short" class="ckeditor"><?php echo $tag->post->html_short; ?></textarea>
  </p>
  <p class='title'>
    <?php echo T('XMLcms_text_content'); ?>:<br />
    <textarea name="html_full" class="ckeditor"><?php echo $tag->post->html_full; ?></textarea>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_date'); ?>:
    <input value='<?php echo $postDate; ?>' id='datepicker' class='input-small' type='text' name='date' value='<?php echo date('Y-m-d'); ?>' />
    <span class='space'></span>
    <?php _T('XMLcms_text_time'); ?>:
    <input value='<?php echo $postTime; ?>' class='input-small' type='text' name='time' value='<?php echo date('H:i:s'); ?>' />
    <span class='space'></span>
    <?php _T('XMLcms_text_order'); ?>:
    <input class='input-small' type='text' name='order' value='<?php echo $tag->post->order; ?>' onkeyup="fsCMS.IsNumeric(this, 0, true, true);" />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template'); ?>:
    <select name='tpl' id='tpl'>
    <?php foreach ($tag->templates as $template) { 
      $selected = $tag->post->tpl == $template ? 'selected' : '';
    ?>
      <option <?php echo $selected; ?> value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select>
    <span class='space'></span>
    <?php _T('XMLcms_text_template_short'); ?>:
    <select name='tpl_short' id='tpl_short'>
    <?php foreach ($tag->templates_short as $template) { 
      $selected = $tag->post->tpl_short == $template ? 'selected' : '';
    ?>
      <option <?php echo $selected; ?> value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php } ?>
    </select>    
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_is_active'); ?>:
    <input <?php echo $tag->post->active == 1 ? 'checked' : ''; ?> type='checkbox' name='active' />
  </p>
  <hr /> 
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />   
</form>
[endblock-content]