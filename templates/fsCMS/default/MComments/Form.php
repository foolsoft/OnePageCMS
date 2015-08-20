<div>
  <div class="div-row">
    <div class="cell"><?php _T('XMLcms_text_your_name'); ?>:</div>
    <div class="cell">
      <?php if(AUTH) { ?>
        <?php echo fsSession::GetArrInstance('AUTH', 'login'); ?>
      <?php } else { ?>
        <input type="text" name="author_name" />
      <?php } ?>
    </div>
  </div>
  <div class="div-row">
    <div class="cell"><?php _T('XMLcms_text_comment_text'); ?>:</div>
    <div class="cell"><textarea name="text"></textarea></div>
  </div>
  <div class="div-row center" id="loader"></div>
  <?php /* foreach($fields as $field) { ?>
  <!--
  <div class="div-row">
    <div class="cell"><?php echo T($field['title']).($field['required'] == '1' ? '<span class="required_field">*</span>' : ''); ?></div>
    <div class="cell"><input type="text" name="additional[<?php echo $field['name']; ?>]" /></div>
  </div>
    -->
  <?php } */ ?>
  <div class="div-row">
      <div class="cell"><?php _T('XMLcms_captcha'); ?>: (<a href="javascript:;" onclick="fsCMS.UpdateImage('captcha')"><?php _T('XMLcms_update'); ?></a>)</div>
    <div class="cell">
        <img id="captcha" src="<?php echo fsHtml::Url(URL_ROOT.'MCaptcha/Create'); ?>" alt="Captcha" />
        <input type="text" name="captcha" value="" />
    </div>
  </div>
  <div class="div-row">
    <input type="submit" value="<?php _T('XMLcms_add'); ?>" />
  </div>
</div>