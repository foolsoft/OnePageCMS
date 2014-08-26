<div class="template-manager">
<div class="row">
  <?php
  echo fsHtml::Label(T('XMLcms_theme'));
  echo fsHtml::Select('template', $tag->templates, $tag->template, array('onchange' => 'TemplateManagerLoadFiles(this.value);', 'class' => 'space', 'style' => 'width:200px;')); 
  echo fsHtml::Label(T('XMLcms_text_template'));
  echo fsHtml::Select('file', $tag->files, false, array('onchange' => 'TemplateManagerLoadFile(this.value);', 'class' => 'space', 'style' => 'width:400px;'));
  echo fsHtml::Label(T('XMLcms_lighter'));
  echo fsHtml::Select('syntax', array('php', 'css', 'javascript', 'html'), false, array('onchange' => 'cmsAdminAceEditor.getSession().setMode(\'ace/mode/\' + this.value);'));
  ?>
</div>
<div class="row" style="margin-top:10px;" id="template-redactor"></div>
<div class="row center" id="template-manager-message" style="margin:5px 0;"></div>
<div class="row center"><input type="submit" value="<?php _T('XMLcms_save'); ?>" /></div>
</div>