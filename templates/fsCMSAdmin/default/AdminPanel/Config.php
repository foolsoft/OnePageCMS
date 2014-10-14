[parent:Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Hello', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action="<?php echo fsHtml::Url($myLink.'DoConfig'); ?>" method="post">
  <p class='title'>
    META - <?php _T('XMLcms_text_description'); ?>:
  </p>
  <p>
    <input class='input-100' type='text' name='default_description' value='<?php echo $tag->settings->default_description; ?>' />
  </p>
  <p class='title'>
    META - <?php _T('XMLcms_text_kw'); ?>:
  </p>
  <p>
    <input class='input-100' type='text' name='default_keywords' value='<?php echo $tag->settings->default_keywords; ?>' />   
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_email_for_send'); ?>:
  </p>
  <p>
    <input class='input-100' type='text' name='robot_email' value='<?php echo $tag->settings->robot_email; ?>' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_url_auth_need'); ?>:
  </p>
  <p>
    <input class='input-100' type='text' name='auth_need_page' value='<?php echo $tag->settings->auth_need_page; ?>' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_start_page'); ?>:
    <?php echo fsHtml::Select('start_page', $tag->start_pages, fsConfig::GetInstance('start_page')); ?>
    <?php echo T('XMLcms_or_custom').': '.URL_ROOT; ?>
    <input type="text" name="start_page_custom" value="<?php echo $tag->settings->start_page_custom; ?>" />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_404'); ?>:
  </p>
  <p>
    <textarea class='ckeditor' name='page_not_found'><?php echo $tag->settings->page_not_found ?></textarea>
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template'); ?>:
    <span>
      <select class='select-small' name='template' onchange="fsCMS.Ajax('AjaxTemplateFiles', 'POST', 'theme='+this.value, 'main_template', true);">    
      <?php
      foreach ($tag->templates as $template) {
        echo fsFunctions::StringFormat('<option value="fsCMS/{0}" {1}>{0}</option>',
                                       array($template,
                                             $tag->settings->template == $template
                                              ? 'selected'
                                              : ''
                                       )   
             );
      } 
      ?>
      </select>
    </span>
    <span class='space'></span>
    <?php _T('XMLcms_text_default_page_template'); ?>:
    <span>
      <select id='main_template' class='select-small' name='main_template'>
      <?php
      foreach ($tag->templatesFiles as $templateFile) {
        echo fsFunctions::StringFormat('<option value="{0}" {1}>{0}</option>',
                                       array($templateFile,
                                             $tag->settings->main_template == $templateFile
                                              ? 'selected'
                                              : ''
                                       )   
             );
      } 
      ?>  
      </select>
    </span>
  </p>
  <p class='title'>
    <?php _T('XMLcms_page_suffix'); ?>:
    <span>
    <?php echo fsHtml::Select(
      'links_suffix',
      array('' => T('XMLcms_no'), '/' => '/', '.html' => '.html', '.htm' => '.htm', 
        '.asp' => '.asp', '.jsp' => '.jsp'), 
      fsConfig::GetInstance('links_suffix'),
      array('onchange' => "var m='';if(this.value!='".fsConfig::GetInstance('links_suffix')."'){m='".T('XMLcms_linksuffix_warning')."';}$('#war_message').text(m);")); ?>
    </span>
    <span class='space'></span>
    <?php _T('XMLcms_multilang'); ?>:
    <span>
    <?php echo fsHtml::Select('multi_language', array('true' => T('XMLcms_yes'), 'false' => T('XMLcms_no')), fsConfig::GetInstance('multi_language') ? 'true' : 'false',
    array('onchange' => "var m='';if(this.value!='".fsConfig::GetInstance('links_suffix')."'){m='".T('XMLcms_linksuffix_warning')."';}$('#war_message').text(m);")); ?>
    </span>
    <br />
    <p class="title" id="war_message"></p>  
  </p>
  <hr />
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_save'); ?>' />     
</form>      
[endblock-content]