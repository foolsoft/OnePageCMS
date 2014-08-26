<div style="width:600px;">
  <div class="div-row">
    <div class="float-left label"><?php _T('XMLcms_text_he_active'); ?></div>
    <div class="float-left editor">
      <input type="checkbox" name="active" <?php echo $tag->comment->active == '1' ? 'checked' : ''; ?> />
    </div>
    <div class="clr"></div>
  </div>
  <div class="div-row">
    <div class="float-left label"><?php _T('XMLcms_text_content'); ?></div>
    <div class="float-left editor">
      <textarea name="text" style="height:100px;"><?php echo $tag->comment->text; ?></textarea>
    </div>
    <div class="clr"></div>
  </div>
  <?php foreach($fields as $field) { ?>
  <div class="div-row">
    <div class="float-left label"><?php _T($field['title']); ?></div>
    <div class="float-left editor">
      <input type="text" name="additional[<?php echo $field['name']; ?>]" value="<?php echo $field['value']; ?>" />
    </div>
    <div class="clr"></div>
  </div>
  <?php } ?>
  <div class="div-row center small" id="message"></div>
  <div class="div-row center">
    <input type="submit" value="<?php _T('XMLcms_save'); ?>" />
  </div>
</div>