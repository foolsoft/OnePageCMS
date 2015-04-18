[parent:../AdminPanel/Index.php]

[block-content]
<?php echo fsHtml::Link($myLink.'Index', T('XMLcms_back'), false, array('class' => 'fsCMS-btn admin-btn-back')); ?>
<hr />
<form action='<?php echo $myLink; ?>DoAdd/referer/Index/' method='post'>
  <p class='title'>
    <?php _T('XMLcms_text_title'); ?>:
    <input class='input-100' type='text' name='title' onkeyup='fsCMS.Chpu(this.value, "name");' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_marker'); ?>:
    <input class='input-100' id='name' type='text' name='name' onkeyup='fsCMS.Chpu(this.value, this.id);' />
  </p>
  <p class='title'>
    <?php _T('XMLcms_text_template'); ?>:
    <select class='select-small' name='tpl'>    
    <?php
    foreach ($tag->templates as $template) {
      $selected = $template == $tag->current_template ? 'selected' : '';
    ?>
      <option value='<?php echo $template; ?>'><?php echo $template; ?></option>
    <?php  
    } 
    ?>
     </select>
  </p>
  <hr />
  <input class='fsCMS-btn admin-btn-save' type='submit' value='<?php _T('XMLcms_add'); ?>' />     
</form>      
[endblock-content]